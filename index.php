<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Caching.php';
require 'Database.php';

class App
{
    /** @var Database */
    protected $database;
    protected $api_key = '5VT3N7UoLwXIADhMrQ31sv2G7DXJXR7N';
    protected $cacheFolder = __DIR__ . '/cache/';
    protected $caching;
    protected $categories;
    protected $images = [];
    protected $purity;
    protected $query = 'anime girl';
    protected $sorting = 'random';
    protected $topRange = null;

    function __construct()
    {
        $this->caching = new Caching(
            $this->cacheFolder,
            100
        );

        $this->database = new Database('db/database.db');
    }

    /**
     * @throws Exception
     */
    function __destruct()
    {
        $this->caching->cleanCache();
        $interval = $_GET['interval'] ?? 900;
        $time = (new DateTime('+' . $interval . ' SECONDS'))->format('H:i');
        list($filenames, $cachingSize) = $this->caching->readCachingFolder();
        require 'view/index.php';
    }

    public function run()
    {
        if (isset($_GET['ignore'])) {
            $this->database->ignoreImage($_GET['ignore']);
        }

        if (isset($_GET['favorite'])) {
            $this->database->favorImage($_GET['favorite']);
        }

        $this->query = $_GET['q'] ?? $this->query;
        $this->topRange = $_GET['topRange'] ?? $this->topRange;
        $this->handleCategories();
        $this->handlePurity();

        foreach ($this->getResolution() as $screen => $resolution) {
            $this->handleImage($screen, $resolution);
        }
    }

    protected function handleImage($screen, $resolutions)
    {
        if ($data = $this->getData($resolutions)) {

            $images = [];
            $ignoreImages = $this->database->getIgnoreImages();
            foreach ($data->data as $image) {
                if (false === isset($ignoreImages[$image->id])) {
                    $images[] = $image;
                }
            }
            $this->images[] = reset($images);

            $this->downloadThumbImage($data);
            $imageName = $this->downloadImage($data);
            shell_exec("osascript -e 'tell application \"System Events\" to tell desktop ".($screen+1)." to set picture to \"" . $imageName . "\"'");
        }
    }

    protected function getData($resolutions): ?stdClass {
        $query = [
            'apikey' => $this->api_key,
            'resolutions' => $resolutions,
            'sorting' => $this->sorting,
            'purity' => $this->purity,
            'categories' => $this->categories,
        ];
        if ($this->query) {
            $query['q'] = $this->query;
        }
        if (isset($_GET['topRange']) && $_GET['topRange']) {
            $query['topRange'] = $_GET['topRange'];
        }

        $url = 'https://wallhaven.cc/api/v1/search?' . http_build_query($query);
        $content = file_get_contents($url);
        $data = json_decode($content);

        if (false === isset($data->data[0]->path)) {
            echo '<br>';
            echo 'no image found: ' . $this->query . ' ' . $resolutions;
            return null;
        }
        return $data;
    }

    protected function handleCategories()
    {
        $this->categories = $_GET['categories'] ?? $this->categories;
        $general = (int)(isset($this->categories['general']));
        $anime = (int)(isset($this->categories['anime']));
        $people = (int)(isset($this->categories['people']));
        $this->categories = $general . $anime . $people;
    }

    protected function handlePurity()
    {
        $this->purity = $_GET['purity'] ?? $this->purity;
        $sfw = (int)(isset($this->purity['sfw']));
        $sketchy = (int)(isset($this->purity['sketchy']));
        $nsfw = (int)(isset($this->purity['nsfw']));
        $this->purity = $sfw . $sketchy . $nsfw;
    }

    protected function downloadImage($data)
    {
        $extension = $this->getFileType($data->data[0]->file_type);
        $imageName = $this->cacheFolder . $data->data[0]->id . $extension;
        if (false === file_exists($imageName)) {
            file_put_contents($imageName, file_get_contents($data->data[0]->path));
        }
        return $imageName;
    }

    protected function downloadThumbImage($data)
    {
        $extension = $this->getFileType($data->data[0]->file_type);
        $imageName = $this->cacheFolder . $data->data[0]->id . '_thumb' . $extension;
        if (false === file_exists($imageName)) {
            file_put_contents($imageName, file_get_contents($data->data[0]->thumbs->small));
        }
    }

    protected function getFileType($fileType): string
    {
        return $fileType === 'image/jpeg' ? '.jpg' : '.png';
    }

    protected function getResolution(): array
    {
        return explode(',', $_GET['resolution'] ?? '1920x1080');
    }

}

(new App())->run();
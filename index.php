<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Caching.php';
require 'Database.php';

class App
{
    protected $api_key = '5VT3N7UoLwXIADhMrQ31sv2G7DXJXR7N';
    protected $cacheFolder = '/cache/';
    protected $caching;
    protected $categories;
    protected $images = [];
    protected $purity;
    protected $query = 'anime girl';
    protected $sorting = 'random';
    protected $tmpFile = '/Users/${USER}/wallpaper';
    protected $topRange = null;
    /** @var Database */
    protected $database;

    function __construct()
    {
        $this->caching = new Caching(
            $this->cacheFolder,
            $this->tmpFile,
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
        $this->caching->unlinkTempFiles();
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

        $this->tmpFile = str_replace(
            '${USER}',
            $this->getUserName(),
            $this->tmpFile
        );

        $this->query = $_GET['q'] ?? $this->query;
        $this->topRange = $_GET['topRange'] ?? $this->topRange;
        $this->handleCategories();
        $this->handlePurity();

        $resolutions = $_GET['resolution'] ?? '1920x1080';
        $resolutions = explode(',', $resolutions);
        foreach ($resolutions as $screen => $resolution) {
            $this->handleImage($screen, $resolutions);
        }
    }

    protected function handleImage($screen, $resolutions)
    {
        if (trim($this->topRange) != '') {
            $query = http_build_query([
                'apikey' => $this->api_key,
                'resolutions' => $resolutions,
                'sorting' => $this->sorting,
                'topRange' => $_GET['topRange'] ?? null,
                'purity' => $this->purity
            ]);
        } else {
            $query = http_build_query([
                'apikey' => $this->api_key,
                'resolutions' => $resolutions,
                'q' => $this->query,
                'sorting' => $this->sorting,
                'categories' => $this->categories,
                'purity' => $this->purity
            ]);
        }

        $url = 'https://wallhaven.cc/api/v1/search?' . $query;
        $content = file_get_contents($url);
        $data = json_decode($content);

        if (false === isset($data->data[0]->path)) {
            echo '<br>';
            echo 'no image found: ' . $this->query . ' ' . $resolutions;
            return null;
        }

        $images = [];
        $ignoreImages = $this->database->getIgnoreImages();
        foreach ($data->data as $image) {
            if (false === isset($ignoreImages[$image->id])) {
                $images[] = $image;
            }
        }
        $this->images[] = reset($images);

        $this->downloadImage($data);
        $this->downloadThumbImage($data);

        shell_exec('/usr/local/bin/wallpaper set --screen ' . $screen . ' ' . $this->tmpFile);
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

    /**
     * @param $data
     */
    protected function downloadImage($data)
    {
        $extension = $this->getFileType($data->data[0]->file_type);
        if (false === strpos($this->tmpFile, '.')) {
            $this->tmpFile = $this->tmpFile . $extension;
        }
        $imageName = __DIR__ . $this->cacheFolder . $data->data[0]->id . $extension;
        if (false === file_exists($imageName)) {
            file_put_contents($imageName, file_get_contents($data->data[0]->path));
        }
        copy($imageName, $this->tmpFile);
    }

    /**
     * @param $data
     */
    protected function downloadThumbImage($data)
    {
        $extension = $this->getFileType($data->data[0]->file_type);
        if (false === strpos($this->tmpFile, '.')) {
            $this->tmpFile = $this->tmpFile . $extension;
        }
        $imageName = __DIR__ . $this->cacheFolder . $data->data[0]->id . '_thumb' . $extension;
        if (false === file_exists($imageName)) {
            file_put_contents($imageName, file_get_contents($data->data[0]->thumbs->small));
        }
    }

    /**
     * @param $fileType
     * @return string
     */
    protected function getFileType($fileType): string
    {
        return $fileType === 'image/jpeg' ? '.jpg' : '.png';
    }

    /**
     * @return string
     */
    protected function getUserName(): string
    {
        return trim(shell_exec('id -un'));
    }

}

(new App())->run();
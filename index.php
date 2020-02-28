<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Caching.php';

class App
{
    protected $api_key = '5VT3N7UoLwXIADhMrQ31sv2G7DXJXR7N';
    protected $cacheFolder = '/cache/';
    protected $caching;
    protected $categories;
    protected $images = [];
    protected $purity;
    protected $query = 'anime girl';
    protected $resolutions = '1920x1080';
    protected $screens = 'all';
    protected $sorting = 'random';
    protected $tmpFile = '/Users/${USER}/wallpaper';
    protected $topRange = null;

    function __construct()
    {
        $this->caching = new Caching(
            $this->cacheFolder,
            $this->tmpFile,
            100
        );
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
        $this->tmpFile = str_replace(
            '${USER}',
            $this->getUserName(),
            $this->tmpFile
        );

        $this->query = $_GET['q'] ?? $this->query;
        $this->resolutions = $_GET['resolution'] ?? $this->resolutions;
        $this->topRange = $_GET['topRange'] ?? $this->topRange;
        $this->handleCategories();
        $this->handlePurity();

        $resolutions = explode(',', $this->resolutions);
        foreach ($resolutions as $screen => $resolution) {
            $this->screens = $screen;
            $this->resolutions = $resolution;
            $this->handleImage();
        }
    }

    protected function handleImage()
    {
        if (trim($this->topRange) != '') {
            $query = http_build_query([
                'apikey' => $this->api_key,
                'resolutions' => $this->resolutions,
                'sorting' => $this->sorting,
                'topRange' => $_GET['topRange'] ?? null,
                'purity' => $this->purity
            ]);
        } else {
            $query = http_build_query([
                'apikey' => $this->api_key,
                'resolutions' => $this->resolutions,
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
            echo 'no image found: ' . $this->query . ' ' . $this->resolutions;
            return null;
        }

        $this->images[] = $data->data[0];

        $this->downloadImage($data);

        shell_exec('/usr/local/bin/wallpaper set --screen ' . $this->screens . ' ' . $this->tmpFile);
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
        $extension = $this->getFileType($data);
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
     * @return string
     */
    protected function getFileType($data)
    {
        $extension = '.png';
        if ($data->data[0]->file_type === 'image/jpeg') {
            $extension = '.jpg';
        }
        return $extension;
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
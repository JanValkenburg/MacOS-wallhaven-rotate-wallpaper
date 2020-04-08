<?php

/**
 * Class Caching
 */
class Caching
{

    /** @var string */
    protected $cacheFolder;
    /** @var string */
    public $maxCachingSize;

    /**
     * Caching constructor.
     * @param string $cacheFolder
     * @param int $maxCachingSize
     */
    public function __construct(
        string $cacheFolder,
        int $maxCachingSize
    )
    {
        $this->cacheFolder = $cacheFolder;
        $this->maxCachingSize = $maxCachingSize;
    }

    /**
     * @param $maxCachingSize
     */
    public function cleanUpCaching($maxCachingSize)
    {
        list($filenames, $sum) = $this->readCachingFolder();
        ksort($filenames);
        foreach ($filenames as $filename) {
            if ($sum > $maxCachingSize * 1000000) {
                unlink($this->cacheFolder . $filename['name']);
                $sum = $sum - $filename['size'];
            }
        }
    }

    /**
     * @return array
     */
    public function readCachingFolder(): array
    {
        $filenames = [];
        $sum = 0;
        $iterator = new DirectoryIterator($this->cacheFolder);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() and $fileinfo->getFilename() !== 'index.html') {
                $filenames[$fileinfo->getMTime()] = [
                    'name' => $fileinfo->getFilename(),
                    'size' => $fileinfo->getSize()
                ];
                $sum += $fileinfo->getSize();
            }
        }
        return array($filenames, $sum);
    }

    public function cleanCache()
    {
        if (isset($_GET['clearCache'])) {
            $this->cleanUpCaching(0);
            $args = $_GET;
            unset($args['clearCache']);
            $query = http_build_query($args);
            header("Location: /?" . $query);
            die;
        } else {
            $this->cleanUpCaching($this->maxCachingSize);
        }
    }
}
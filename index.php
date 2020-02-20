<?php

class App
{
    protected $api_key = '5VT3N7UoLwXIADhMrQ31sv2G7DXJXR7N';
    protected $sorting = 'random';
    protected $resolutions = '1920x1080';
    protected $tmpFile = '/Users/${USER}/wallpaper.png';
    protected $screens = 'all';
    protected $query = '';

    public function run()
    {
        $this->query = $_GET['q'] ?? 'anime girl';
        $this->screens = 1;
        $this->handleImage();

        $this->screens = 0;
//        $this->resolutions = '1080x1920';
        $this->handleImage();
    }

    protected function handleImage()
    {
        $query = http_build_query([
            'apikey' => $this->api_key,
            'resolutions' => $this->resolutions,
            'q' => 'type:{png}|' . $this->query,
            'sorting' => $this->sorting
        ]);

        $url = 'https://wallhaven.cc/api/v1/search?' . $query;
        $content = file_get_contents($url);
        $data = json_decode($content);

        if (false === isset($data->data[0]->path)) {
            echo '<br>';
            echo 'no image found: '. $this->query . ' ' . $this->resolutions;
            return null;
        }

        $userId = trim(shell_exec('id -un'));
        $this->tmpFile = str_replace('${USER}', $userId, $this->tmpFile);
        file_put_contents($this->tmpFile, file_get_contents($data->data[0]->path));
        shell_exec('/usr/local/bin/wallpaper set --screen ' . $this->screens . ' ' . $this->tmpFile);
    }
}

(new App())->run();
?>
<meta http-equiv="refresh" content="900">

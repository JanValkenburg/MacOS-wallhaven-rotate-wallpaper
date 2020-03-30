<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="<?= $interval; ?>">
    <link rel="shortcut icon" href="favicon.ico"/>
    <link rel="stylesheet" href="view/screen.css"/>
    <link rel="stylesheet" href="view/darkmodus.css" media="(prefers-color-scheme: dark)"/>
    <title>MacOS WallHaven wallpaper rotator</title>
</head>
<body>

<header>
    <h1>MacOS Wallhaven Wallpaper Rotator</h1>
    <time>Next refresh: <?= $time ?></time>
    <span>Cache size: <?= ceil($cachingSize / 1000 / 1000); ?>M/<?= $this->caching->maxCachingSize; ?>M</span>
</header>

<main>

    <form onsubmit="saveFormState()">
        <legend>Filter wallpaper</legend>

        <label>Query</label>
        <input type="search"
               name="q"
               value="<?= $_GET['q'] ?? ''; ?>"
        />

        <label>Resolution</label>
        <input type="text"
               name="resolution"
               value="<?= $_GET['resolution'] ?? ''; ?>"
        />

        <div>

            <label>Categories</label>
            <?php foreach (['general', 'anime', 'people'] as $item): ?>
                <input id="categories<?= $item; ?>"
                       type="checkbox"
                       name="categories[<?= $item; ?>]"
                       <?php if (isset($_GET['categories'][$item])): ?>checked<?php endif; ?>
                />
                <label for="categories<?= $item; ?>"
                       class="col-1-3"
                >
                    <?= $item; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div>
            <label>Purity</label>
            <?php foreach (['sfw', 'sketchy', 'nsfw'] as $item): ?>
                <input id="purity<?= $item; ?>"
                       type="checkbox"
                       name="purity[<?= $item; ?>]"
                       <?php if (isset($_GET['purity'][$item])): ?>checked<?php endif; ?>
                />
                <label for="purity<?= $item; ?>"
                       class="col-1-3"
                >
                    <?= $item; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Top range</label>
        <select name="topRange">
            <option value="">disabled</option>
            <?php foreach (['1d', '3d', '1w', '1M', '3M', '6M', '1y'] as $topRange): ?>
                <option value="<?= $topRange; ?>"
                        <?php if (isset($_GET['topRange']) && $_GET['topRange'] == $topRange): ?>selected<?php endif ?>
                >
                    <?= $topRange ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Refresh interval</label>
        <select name="interval">
            <?php foreach (['30' => '30 sec', '60' => '1 min', '300' => '5 min', '600' => '10 min', '900' => '15 min', '1800' => '30 min', '3600' => '1 hour'] as $value => $interval): ?>
                <option value="<?= $value; ?>"
                        <?php if (isset($_GET['interval']) && $_GET['interval'] == $value): ?>selected<?php endif ?>
                >
                    <?= $interval ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <button class="right">
            Submit
        </button>

        <button name="clearCache"
                class="danger"
                value="true">
            Clear caching
        </button>

    </form>

    <?php foreach ($this->images as $image): ?>
        <figure>

            <a href="<?= $image->url; ?>" target="_blank">
                <?php if ($image->file_type === 'image/jpeg'): ?>
                    <img src="cache/<?= $image->id ?>_thumb.jpg"/>
                <?php else: ?>
                    <img src="cache/<?= $image->id ?>_thumb.png"/>
                <?php endif; ?>
            </a>

            <figcaption>
                <span class="right">
                    <?php $query = $_GET; ?>
                    <?php unset($query['ignore']); ?>
                    <?php $query = http_build_query($query); ?>
                    <a href="?favorite=<?= $image->id; ?>&<?= $query; ?>"
                       title="Like image">
                        💖
                    </a>
                    <a href="?ignore=<?= $image->id; ?>&<?= $query; ?>"
                       title="Block image">
                        🚫
                    </a>
                    ❤️ <?= number_format($image->favorites, 0, '', '.'); ?>
                    👁️ <?= number_format($image->views, 0, '', '.'); ?>

                </span>
                Purity: <?= $image->purity; ?>
                <br>
                Category: <?= $image->category; ?>
                <br>
                Created: <?= (new DateTime($image->created_at))->format('j F Y'); ?>
            </figcaption>

        </figure>
    <?php endforeach; ?>

</main>

<footer>
    Copyright 2020,
    <a href="https://jv-dezign.com/" target="_blank">JV-Dezign.com</a>,
    created by:
    <a href="https://janvalkenburg.nl/" target="_blank">janvalkenburg.nl</a>
</footer>

<script src="view/script.js"></script>
</body>
</html>
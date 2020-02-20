<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="<?= $interval; ?>">
    <title>MacOS WallHaven wallpaper rotator</title>
</head>
<body>
<header>
    <h1>MacOS Wallhaven Wallpaper Rotator</h1>
    <time>Next refresh: <?= $time ?></time>
</header>
<main>

    <?php foreach ($this->images as $image): ?>
        <figure style="float:left;border: 1px solid silver;">
            <a href="<?= $image->url; ?>" target="_blank">
                <img src="<?= $image->thumbs->small; ?>"/>
            </a>
            <figcaption style="padding: 10px;">
                purity: <?= $image->purity; ?>
                <br>
                category: <?= $image->category; ?>
                <br>
                created_at: <?= $image->created_at; ?>
                <br>
                views: <?= $image->views; ?>
                |
                favorites: <?= $image->favorites; ?>
            </figcaption>
        </figure>
    <?php endforeach; ?>
    <form>
        <fieldset>

            <label for="">Zoekopdracht</label>
            <input type="search"
                   name="q"
                   value="<?= $_GET['q'] ?? ''; ?>"
            />
            <br>

            <label for="">Resolution</label>
            <input type="text"
                   name="resolution"
                   value="<?= $_GET['resolution'] ?? ''; ?>"
            />
            <br>

            <label for="">purity</label>
            <select name="purity" id="">
                <?php foreach (['sfw', 'sketchy', 'nsfw'] as $purity): ?>
                    <option value="<?= $purity; ?>"
                            <?php if (isset($_GET['purity']) && $_GET['purity'] == $purity): ?>selected<?php endif ?>
                    >
                        <?= $purity ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <label for="">topRange</label>
            <select name="topRange" id="">
                <option value="">disabled</option>
                <?php foreach (['1d', '3d', '1w', '1M', '3M', '6M', '1y'] as $topRange): ?>
                    <option value="<?= $topRange; ?>"
                            <?php if (isset($_GET['topRange']) && $_GET['topRange'] == $topRange): ?>selected<?php endif ?>
                    >
                        <?= $topRange ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <label for="">refresh interval</label>
            <select name="interval" id="">
                <?php foreach (['30', '60', '300', '600', '900', '1800', '3600'] as $interval): ?>
                    <option value="<?= $interval; ?>"
                            <?php if (isset($_GET['interval']) && $_GET['interval'] == $interval): ?>selected<?php endif ?>
                    >
                        <?= $interval ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <br>

            <button>
                Verzenden
            </button>
        </fieldset>
    </form>
</main>
<footer>
    created by:
    <a href="https://jv-dezign.com/" target="_blank">JV-Dezign.com</a>
    |
    <a href="https://janvalkenburg.nl/" target="_blank">janvalkenburg.nl</a>
</footer>
<script>
    if (document.querySelector('[name="resolution"]').value === '') {
        document.querySelector('[name="resolution"]').value = window.screen.width + 'x' + window.screen.height;
    }
</script>
<style>
    * {
        padding: 0;
        margin: 0;
        outline: none;
        box-sizing: border-box;
    }
    html {
        cursor: default;
        font-family: Arial;
        color: #202020;
    }
    a {
        color: inherit;
    }
    header {
        background: white;
        padding: 5px 10px;
        box-shadow: 0 0 5px rgba(0,0,0,.5);
        margin-bottom: 1rem;
    }
    header h1 {
        display: inline-block;
    }
    header time {
        float: right;
    }
    main {
        padding: 5px;
    }
    figure {
        margin-left: 5px;
        margin-right: 5px;
    }
    form {
        padding: 5px 10px;
    }
    form label {
        display: block;
        margin-top: .5rem;
    }
    fieldset {
        padding: 10px;
    }
    form button {
        background: blue;
        border-radius: 3px;
        border: none;
        cursor: pointer;
        padding: 5px 10px;
        color: white;
    }
    footer {
        clear: both;
        position: fixed;
        bottom:0;
        left:0;
        right: 0;
        box-shadow: 0 0 5px rgba(0,0,0,.5);
        text-align: center;
        padding: 5px 10px;
        font-size: 12px;
        background: #fff;

    }

</style>
</body>
</html>
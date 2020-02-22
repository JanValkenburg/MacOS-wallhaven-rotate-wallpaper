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

    <form>
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

        <label>Categories</label>
        <input id="categoriesGeneral"
               type="checkbox"
               name="categories[general]"
               style="display: none;"
               <?php if (isset($_GET['categories']['general'])): ?>checked<?php endif; ?>
        />
        <label for="categoriesGeneral"
               style="width: 32%; display: inline-block; border: 1px solid silver; text-align: center;">
            General
        </label>

        <input id="categoriesAnime"
               type="checkbox"
               name="categories[anime]"
               style="display: none;"
               <?php if (isset($_GET['categories']['anime'])): ?>checked<?php endif; ?>
        />
        <label for="categoriesAnime"
               style="width: 32%; display: inline-block; border: 1px solid silver; text-align: center;">
            Anime
        </label>

        <input id="categoriesPeople"
               type="checkbox"
               name="categories[people]"
               style="display: none;"
               <?php if (isset($_GET['categories']['people'])): ?>checked<?php endif; ?>
        />
        <label for="categoriesPeople"
               style="width: 32%; display: inline-block; border: 1px solid silver; text-align: center;">
            People
        </label>

        <label>Purity</label>
        <select name="purity">
            <?php foreach (['100' => 'sfw', '010' => 'sketchy', '001' => 'nsfw'] as $value => $purity): ?>
                <option value="<?= $value; ?>"
                        <?php if (isset($_GET['purity']) && $_GET['purity'] == $value): ?>selected<?php endif ?>
                >
                    <?= $purity ?>
                </option>
            <?php endforeach; ?>
        </select>

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

        <button>
            Submit
        </button>
    </form>


    <?php foreach ($this->images as $image): ?>
        <figure>
            <a href="<?= $image->url; ?>" target="_blank">
                <img src="<?= $image->thumbs->small; ?>"/>
            </a>
            <figcaption style="padding: 10px;">
                file type: <?= $image->file_type; ?>
                <br>
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
</main>
<footer>
    Created by:
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

    [type="checkbox"] + label {
        cursor: pointer;
    }
    [type="checkbox"]:checked + label {
        background: silver;
    }
    header {
        background: white;
        padding: 5px 10px;
        box-shadow: 0 0 5px rgba(0, 0, 0, .5);
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
        float: left;
        overflow: hidden;
        border: 1px solid silver;
        border-radius: 5px;
    }

    form {
        overflow: hidden;
        border: 1px solid silver;
        border-radius: 5px;
        padding: 5px 10px;
        float: left;
        width: 30%;
    }

    form label {
        display: block;
        margin-top: .5rem;
    }

    form select,
    form input {
        display: block;
        width: 100%;
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
        bottom: 0;
        left: 0;
        right: 0;
        box-shadow: 0 0 5px rgba(0, 0, 0, .5);
        text-align: center;
        padding: 5px 10px;
        font-size: 12px;
        background: #fff;
    }

</style>
</body>
</html>
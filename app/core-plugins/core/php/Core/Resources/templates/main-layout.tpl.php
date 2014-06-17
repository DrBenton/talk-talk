<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?/*= $this->app()->exec('forum-base.get_site_title') */?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php /*
    <meta name="csrf-param" content="<?= $this->app()->get('csrf')->getTokenName() ?>"/>
    <meta name="csrf-token" content="<?= $this->app()->get('csrf')->getTokenValue() ?>"/>
    */ ?>

    <?php
    $appStyleSheets = $app->get('hooks')->getHookFlattenedResult('layout.css.get_stylesheets');
    foreach($appStyleSheets as $cssResource): ?>
        <link rel="stylesheet" href="<?= $cssResource['url'] ?>">
    <?php endforeach ?>


</head>
<body>
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<div id="site-container" class="waiting-initialization">

    <header>
        <div class="header-content"></div>
    </header>

    <div id="main-content-container">
        <div id="main-content">
            <?php /* I'm waiting your orders, Sire! */ ?>
        </div>
    </div>

    <footer>
        <div class="footer-content"></div>
    </footer>


</div><?php /* end #site-container */ ?>

<?php
/* App data to expose to JavaScript */
$appData = $app->get('hooks')->getHookFlattenedResult('layout.js.get_data');
?>
<div id="app-data"
     data-config="<?= $app->get('utils.string')->escape(json_encode($appData)) ?>"></div>

<?php
/* JavaScript, it's up to you now! */
$appJavascripts = $app->get('hooks')->getHookFlattenedResult('layout.js.get_scripts');
foreach($appJavascripts as $jsResource):
    $jsResource = $app->get('utils.array')->getArray($jsResource, 'url');
?>
    <script src="<?= $jsResource['url'] ?>"></script>
<?php endforeach ?>

</body>
</html>
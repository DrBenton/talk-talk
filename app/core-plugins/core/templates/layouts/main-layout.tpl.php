<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?/*= $this->app()->exec('forum-base.get_site_title') */?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-param" content="<?= $this->app()->get('csrf')->getTokenName() ?>"/>
    <meta name="csrf-token" content="<?= $this->app()->get('csrf')->getTokenValue() ?>"/>

    <?= $this->hooks()->html('app_stylesheets') ?>
    <?php foreach($this->appAssets()->getCss() as $cssResource): ?>
    <link rel="stylesheet" href="<?= $cssResource['url'] ?>">
    <?php endforeach ?>

    <?php
    /* Do we have "HEAD" JavaScript files? Let's include them now! */
    $this->insert('core::common/javascripts-inclusion', array(
        'javascripts' => $this->appAssets()->getHeadJs()
    ))
    ?>

</head>
<body>
<!--[if lt IE 8]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<?= $this->hooks()->html('site_container') ?>
<div id="site-container">

    <?php if (isset($this->header)): /*a custom header has been requested*/ ?>
        <?= $this->header ?>
    <?php else: /*default header*/ ?>
        <?= $this->insert('core::common/header') ?>
    <?php endif ?>

    <?php $this->insert('core::common/alerts-display', array(
        'alerts' => $this->app()->get('flash')->getFlashes('alerts.')
    )) ?>

    <?php if (isset($this->breadcrumb)): /*a custom breadcrumb has been requested*/ ?>
        <?= $this->breadcrumb ?>
    <?php elseif (isset($this->breadcrumbData)): /*default breadcrumb*/ ?>
        <?= $this->insert('core::common/breadcrumb', array('data' => $this->breadcrumbData)) ?>
    <?php endif ?>

    <?= $this->hooks()->html('main_content_container') ?>
    <div id="main-content-container">
        <div id="main-content" class="clearfix">
            <?= $this->content() ?>
        </div>
    </div>

    <?php if (isset($this->footer)): /*a custom footer has been requested*/ ?>
        <?= $this->footer ?>
    <?php else: /*default footer*/ ?>
        <?= $this->insert('core::common/footer') ?>
    <?php endif ?>


    <?php
    if ($this->app()->vars['debug'] && !empty($this->app()->vars['config']['debug']['perfs.tracking.enabled'])) {
        $this->insert('utils::debug/app-perfs-info');
    }
    ?>

</div><?php /* end #site-container */ ?>

<?php $this->insert('core::common/js-data') ?>

<?php
/* JavaScript, it's up to you now! */
$this->insert('core::common/javascripts-inclusion', array(
    'javascripts' => $this->appAssets()->getEndOfBodyJs()
))
?>

</body>
</html>

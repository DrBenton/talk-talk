<?php $this->layout('forum-base::all-forums-display') ?>

<?php $this->start('intro') ?>

    <?= $this->hooks()->html('page.home') ?>
    <div id="home-heading">
        <h1><?= $this->trans('core-plugins.forum-base.home.title', array('%site-title%' => $this->siteTitle)) ?></h1>
    </div>

<?= $this->end() ?>
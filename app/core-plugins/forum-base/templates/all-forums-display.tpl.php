<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache='<?= json_encode(array('duration' => 120)) ?>'></span>

<?php if (isset($this->intro)): ?>
    <?= $this->intro ?>
<? endif ?>

<?= $this->hooks()->html('page.forums') ?>
<?php $this->insert('forum-base::inc/forums-list', array('forums' => $this->forumsTree)) ?>
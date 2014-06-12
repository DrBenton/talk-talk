<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache=<?= $this->e(json_encode(array('duration' => 120))) ?>"></span>

<?php if (isset($this->intro)): ?>
    <?= $this->intro ?>
<?php endif ?>

<?= $this->hooks()->html('page.forums') ?>
<?php $this->insert('forum-base::inc/forums-list', array('forums' => $this->forumsTree)) ?>

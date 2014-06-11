<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 30))) ?>"></span>

<?php $this->insert('forum-base::inc/new-topic-form', array(
    'topic' => $this->topic,
    'forum' => $this->forum,
)) ?>
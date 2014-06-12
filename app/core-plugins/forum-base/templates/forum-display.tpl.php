<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 30))) ?>"></span>

<?= $this->hooks()->html('page.forum', 'forum') ?>
<div class="forum-container parent-forum level-<?= $this->forum->depth() ?>"
     data-forum-id="<?= $this->forum->id ?>">
    <div class="forum">
        <h2 class="forum-title"><?= $this->e($this->forum->title) ?></h2>
        <div class="forum-desc"><?= $this->e($this->forum->desc) ?></div>
    </div><?php /* end .forum */ ?>
</div><?php /* end .forum-container */ ?>

<?php if (!empty($this->forumChildren)): ?>
    <div class="forum-children">
        <div class="forums-list-desc list-desc">
            <?= count($this->forumChildren) ?> sub-forums
        </div>
        <?php $this->insert('forum-base::inc/forums-list', array('forums' => $this->forumChildren)) ?>
    </div>
<?php endif ?>

<?php $this->insert('utils::common/pagination', $this->paginationData) ?>

<?php $this->insert('forum-base::macro/new-topic-link', array('forumId' => $this->forum->id)) ?>

<?php
$this->insert('forum-base::inc/topics-list', array(
    'topics' => $this->topics,
    'nbTopicsTotal' => $this->nbTopicsTotal,
    'paginationData' => $this->paginationData,
))
?>

<?php if (!empty($this->topics)): ?>
    <?php $this->insert('forum-base::macro/new-topic-link', array('forumId' => $this->forum->id)) ?>
<?php endif ?>

<?php $this->insert('utils::common/pagination', $this->paginationData) ?>

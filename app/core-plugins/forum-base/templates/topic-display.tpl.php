<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 30))) ?>"></span>

<?= $this->hooks()->html('page.topic', 'topic') ?>
<div class="topic-container"
     data-topic-id="<?= $this->e($this->topic->id) ?>">
    <div class="topic">
        <h2 class="topic-title"><?= $this->e($this->topic->title) ?></h2>
        <div class="topic-desc">
            Created <?= $this->topic->created_at ?> by
            <span class="topic-author"><?= $this->e($this->topic->author->login) ?></span><br>
            <?php if ($this->topic->lastPost->id !== $this->posts[0]->id): ?>
                Last reply <?= $this->topic->lastPost->created_at ?> by
                <span class="topic-last-participant"><?= $this->topic->lastPost->author->login ?></span>
            <?php else: ?>
                No reply.
            <?php endif ?>
        </div>

        <?php $this->insert('utils::common/pagination', $this->paginationData) ?>

        <?php $this->insert('forum-base::macro/new-post-link', array('topicId' => $this->topic->id)) ?>

        <?php $this->insert('forum-base::inc/posts-list', array(
            'posts' => $this->posts,
            'topic' => $this->topic,
            'nbPostsTotal' => $this->nbPostsTotal,
            'paginationData' => $this->paginationData,
        )) ?>

        <?php if (!empty($this->posts)): ?>
            <?php $this->insert('forum-base::macro/new-post-link', array('topicId' => $this->topic->id)) ?>
        <?php endif ?>

        <?php $this->insert('utils::common/pagination', $this->paginationData) ?>

    </div><?php /* end .topic-display */ ?>
</div><?php /* end .topic-display-container */ ?>

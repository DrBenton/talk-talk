<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 30))) ?>"></span>

<?php $this->insert('forum-base::inc/new-post-form', array(
    'post' => $this->post,
    'topic' => $this->topic,
)) ?>

<div class="topic-posts-summary-container">
    <div class="topic-posts-summary posts-list">
        <div class="intro list-desc">
            <?= $this->transE('core-plugins.forum-base.new-post.topic-posts-summary.intro') ?>
        </div>
        <div class="topic-first-post-container">
            <div class="intro">
                <?= $this->transE('core-plugins.forum-base.new-post.topic-posts-summary.first-post') ?>
            </div>
            <div class="posts-list-container">
                <div class="posts-list">
                    <?php $this->insert('forum-base::inc/post', array(
                        'post' => $this->firstPost,
                        'topic' => $this->topic
                    )) ?>
                </div>
            </div>
        </div>
        <?php if ($this->topic->firstPost->id !== $this->topic->lastPost->id): ?>
            <div class="topic-last-posts-container">
                <div class="intro">
                    <?= $this->transE('core-plugins.forum-base.new-post.topic-posts-summary.last-posts') ?>
                </div>
                <?= $this->hooks()->html('posts_list') ?>
                <div class="posts-list-container">
                    <div class="posts-list">
                        <?php foreach ($this->lastPosts as $post): ?>
                            <?php $this->insert('forum-base::inc/post', array(
                                'post' => $post,
                                'topic' => $this->topic
                            )) ?>
                        <?php endforeach ?>
                    </div><?php /* end .posts-list */ ?>
                </div><?php /* end .posts-list-container */ ?>
            </div>
        <?php else: ?>
            <div class="empty-list-msg">
                <?= $this->transE('core-plugins.forum-base.new-post.topic-posts-summary.single-post-only') ?>
            </div>
        <?php endif ?>
    </div>
</div>

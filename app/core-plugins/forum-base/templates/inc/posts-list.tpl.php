<?php if (!empty($this->posts)): ?>

    <?= $this->hooks()->html('posts_list') ?>
    <div class="posts-list-container">
        <div class="posts-list">
            <div class="posts-list-desc list-desc">
                <?= $this->nbPostsTotal ?> posts - page <?= $this->paginationData['currentPageNum'] ?> -
                <?= count($this->posts) ?> posts on this page
            </div>
            <?php foreach($this->posts as $post): ?>
                <?php $this->insert('forum-base::inc/post', array(
                    'post' => $post,
                    'topic' => $this->topic
                )) ?>
            <?php endforeach ?>
        </div><?php /* end .posts-list */ ?>
    </div><?php /* end .posts-list-container */ ?>

<?php else: ?>
    <div class="empty-list-msg">
        <?= $this->transE('core-plugins.forum-base.posts-list.no-post') ?>
    </div>
<?php endif ?>

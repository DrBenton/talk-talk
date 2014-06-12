<?= $this->hooks()->html('post') ?>
<div class="post-container <?= $this->post->author->id === $this->topic->author->id ? 'topic-author' : '' ?>">
    <div id="post-<?= $this->post->id ?>" class="post">
        <h4 class="post-title">
            <?= $this->forumUtils()->bbcodeToHtml($this->post->title) ?> <i>(<?= $this->post->id ?>)</i>
        </h4>
        <div class="post-content">
            <?= $this->forumUtils()->bbcodeToHtml($this->post->content) ?>
        </div>
        <div class="post-info">
            <i>
                Posted <?= $this->post->created_at ?><br>
                <?php if ($this->post->created_at != $this->post->updated_at): ?>
                    Last edited <?= $this->post->updated_at ?>
                <?php endif ?>
            </i>
        </div>
        <div class="post-author">
            by <span class="post-author-name"><?= $this->post->author->login ?></span>
        </div>
    </div>
</div>

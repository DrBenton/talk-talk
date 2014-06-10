<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 30))) ?>"></span>

<?= $this->hooks()->html('page.new_post_form') ?>

<?= $this->hooks()->html('form', 'new_post_form') ?>
<form id="new-post-form" role="form"
      action="<?= $this->app()->path('forum-base/new-post-form/target', array('topic' => $this->topic->id)) ?>"
      method="post"
      class="new-post-form post-content-form ajax-form">

    <?php $this->insert('core::common/csrf-hidden-input') ?>

    <fieldset id="core-inputs">

        <div class="form-group title">
            <label for="new-post-input-title">
                <?= $this->transE('core-plugins.forum-base.new-post.form.title') ?>
            </label>

            <div class="input-container">
                <input type="text" name="post[title]"
                       id="new-post-input-title"
                       class="input input-text title"
                       value="<?= $this->e($this->post->title) ?>"
                       required>
            </div>
        </div>

        <div class="form-group content">
            <label for="new-post-input-content">
                <?= $this->transE('core-plugins.forum-base.new-post.form.content') ?>
            </label>

            <div class="input-container">
                <textarea type="text" name="post[content]"
                          id="new-post-input-content"
                          class="input input-text content"
                          required><?= $this->e($this->post->content) ?></textarea>
            </div>
        </div>

    </fieldset>

    <div class="form-group submit">
        <div class="input-container">
            <button type="submit" class="submit-button">
                <?= $this->transE('core-plugins.forum-base.new-post.form.submit') ?>
            </button>
        </div>
    </div>

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

</form>
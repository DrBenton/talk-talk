<?php if ($this->app()->get('user')->isAnonymous()): ?>
    <?= $this->hooks()->html('authentication_required_msg') ?>
    <div class="authentication-required-msg">
        <?= $this->trans(
            'core-plugins.forum-base.acl.must_be_authenticated.for-new-post',
            array(
                '%sign-in-url%' => $this->app()->path('auth/sign-in', array('return-url' => $this->utils()->getCurrentPath())),
                '%sign-up-url%' => $this->app()->path('auth/sign-up', array('return-url' => $this->utils()->getCurrentPath()))
            )
        ) ?>
    </div>
<?php else: ?>
    <?= $this->hooks()->html('create_new_post_link') ?>
    <a href="<?= $this->app()->path('forum-base/new-post-form', array('topic' => $this->topicId)) ?>"
       class="create-new-post-link ajax-link"
       data-topic-id="<?= $this->e($this->topicId) ?>">
        <?= $this->transE('core-plugins.forum-base.posts-list.create-new-post') ?>
    </a>
<?php endif ?>

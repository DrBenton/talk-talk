<?php if ($this->app()->get('user')->isAnonymous()): ?>
    <?= $this->hooks()->html('authentication_required_msg') ?>
    <div class="authentication-required-msg">
        <?= $this->trans(
            'core-plugins.forum-base.acl.must_be_authenticated.for-new-topic',
            array(
                '%sign-in-url%' => $this->app()->path('auth/sign-in', array('return-url' => $this->utils()->getCurrentPath())),
                '%sign-up-url%' => $this->app()->path('auth/sign-up', array('return-url' => $this->utils()->getCurrentPath()))
            )
        ) ?>
    </div>
<?php else: ?>
    <?= $this->hooks()->html('create_new_topic_link') ?>
    <a href="<?= $this->app()->path('forum-base/new-topic-form', array('forum' => $this->forumId)) ?>"
       class="create-new-topic-link ajax-link"
       data-forum-id="<?= $this->e($this->forumId) ?>">
        <?= $this->transE('core-plugins.forum-base.topics-list.create-new-topic') ?>
    </a>
<?php endif ?>
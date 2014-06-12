<?php $this->layout('ajax-post-writing::layouts/ajax-writing-frame') ?>

<?php $this->start('headerContent') ?>
    <?= $this->transE('core-plugins.ajax-post-writing.ajax-writing-frame.post.intro', array(
        '%topic-title%' => $this->topic->title,
    )) ?>
<?php $this->end() ?>

<?php $this->insert('forum-base::inc/new-post-form', array(
    'post' => $this->post,
    'topic' => $this->topic,
)) ?>
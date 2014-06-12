<?php $this->layout('ajax-post-writing::layouts/ajax-writing-frame') ?>

<?php $this->start('headerContent') ?>
    <?= $this->transE('core-plugins.ajax-post-writing.ajax-writing-frame.topic.intro', array(
        '%forum-title%' => $this->forum->title,
    )) ?>
<?php $this->end() ?>

<?php $this->insert('forum-base::inc/new-topic-form', array(
    'topic' => $this->topic,
    'forum' => $this->forum,
)) ?>
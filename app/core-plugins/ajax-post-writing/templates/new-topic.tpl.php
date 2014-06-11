
<?= $this->hooks()->html('ajax_topic_writing_widget') ?>
<div class="ajax-writing-frame-container">
    <div class="ajax-writing-frame">

        <div class="frame-tools">
            <button class="minimize"> - </button>
            <button class="close"> &times; </button>
        </div>

        <div class="header">
            <?= $this->transE('core-plugins.ajax-post-writing.ajax-writing-frame.topic.intro', array(
                '%forum-title%' => $this->forum->title,
            )) ?>
        </div>

        <?php $this->insert('forum-base::inc/new-topic-form', array(
            'topic' => $this->topic,
            'forum' => $this->forum,
        )) ?>

    </div>
</div>

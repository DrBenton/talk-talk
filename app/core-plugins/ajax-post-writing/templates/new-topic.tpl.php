
<?= $this->hooks()->html('ajax_topic_writing_widget') ?>
<div class="ajax-writing-frame-container">
    <div class="ajax-writing-frame">

        <div class="frame-tools-container">
            <div class="frame-tools">
                <button class="bt bt-minimize"> - </button>
                <button class="bt bt-cancel-minimize"> + </button>
                <button class="bt bt-fullscreen"> &#9635; </button>
                <button class="bt bt-cancel-fullscreen"> &#9633; </button>
                <button class="bt bt-close"> &times; </button>
            </div>
        </div>

        <div class="header-container">
            <div class="header">
                <?= $this->transE('core-plugins.ajax-post-writing.ajax-writing-frame.topic.intro', array(
                    '%forum-title%' => $this->forum->title,
                )) ?>
            </div>
        </div>

        <div class="form-container">
            <?php $this->insert('forum-base::inc/new-topic-form', array(
                'topic' => $this->topic,
                'forum' => $this->forum,
            )) ?>
        </div>

    </div>
</div>

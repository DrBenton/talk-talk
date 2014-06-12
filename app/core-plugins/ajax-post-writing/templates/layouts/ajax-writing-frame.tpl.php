
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
                <?= $this->headerContent ?>
            </div>
        </div>

        <div class="form-container">
            <?= $this->content() ?>
        </div>

    </div>
</div>

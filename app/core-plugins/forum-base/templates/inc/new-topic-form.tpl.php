
<?= $this->hooks()->html('page.new_topic_form') ?>

<?= $this->hooks()->html('form', 'component.post_content_editor') ?>
<form id="new-topic-form" role="form"
      action="<?= $this->app()->path('forum-base/new-topic-form/target', array('forum' => $this->forum->id)) ?>"
      method="post"
      class="new-topic-form post-content-form ajax-form">

    <?php $this->insert('core::common/csrf-hidden-input') ?>

    <fieldset id="core-inputs">

        <div class="form-group title">
            <label for="new-topic-input-title">
                <?= $this->transE('core-plugins.forum-base.new-topic.form.title') ?>
            </label>

            <div class="input-container">
                <input type="text" name="topic[title]"
                       id="new-topic-input-title"
                       class="input input-text title"
                       value="<?= $this->e($this->topic->title) ?>"
                       required>
            </div>
        </div>

        <div class="form-group content">
            <label for="new-topic-input-content">
                <?= $this->transE('core-plugins.forum-base.new-topic.form.content') ?>
            </label>

            <div class="input-container">
                <textarea type="text" name="topic[content]"
                          id="new-topic-input-content"
                          class="input input-text content"
                          required><?= $this->e($this->topic->content) ?></textarea>
            </div>
        </div>

    </fieldset>

    <div class="form-group submit">
        <div class="input-container">
            <button type="submit" class="submit-button">
                <?= $this->transE('core-plugins.forum-base.new-topic.form.submit') ?>
            </button>
        </div>
    </div>

</form>
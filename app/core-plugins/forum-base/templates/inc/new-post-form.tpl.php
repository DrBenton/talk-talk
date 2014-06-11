
<?= $this->hooks()->html('page.new_post_form') ?>

<?= $this->hooks()->html('form', 'component.post_content_editor') ?>
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

</form>
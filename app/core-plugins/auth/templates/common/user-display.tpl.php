<?= $this->hooks()->html('user_profile') ?>
<div class="logged-user-display">
    <?= $this->app()->get('user')->getUser()->login ?>
</div>

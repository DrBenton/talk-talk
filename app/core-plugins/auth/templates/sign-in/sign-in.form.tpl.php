<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache='<?= json_encode(array('duration' => 600)) ?>'></span>

<?= $this->hooks()->html('form', 'signin_form') ?>
<form id="sign-in-form" role="form"
      action="<?= $this->app()->path('auth/sign-in/target') ?>"
      method="post"
      class="sign-form ajax-form">

    {% include 'core/common/csrf-hidden-input.twig' %}

    <fieldset id="core-inputs">

        <div class="form-group login <?= isset($this->failed_fields) && isset($this->failed_fields['login']) ? 'form-error' : '' ?>">
            <label for="signin-input-login">
                <?= $this->e($this->trans('core-plugins.auth.sign-in.form.login')) ?>
            </label>

            <div class="input-container">
                <input type="text" name="user[login]"
                       id="signin-input-login"
                       class="input input-text login"
                       value="<?= $this->e($this->user->login) ?>"
                       required>
            </div>
        </div>

        <div class="form-group password <?= isset($this->failed_fields) && isset($this->failed_fields['password']) ? 'form-error' : '' ?>">
            <label for="signin-input-password">
                <?= $this->e($this->trans('core-plugins.auth.sign-in.form.password')) ?>
            </label>

            <div class="input-container">
                <input type="password" name="user[password]"
                       id="signin-input-password"
                       class="input input-password password"
                       required>
            </div>
        </div>

    </fieldset>

    <div class="form-group submit">
        <div class="input-container">
            <button type="submit" class="submit-button">
                <?= $this->e($this->trans('core-plugins.auth.sign-in.form.submit')) ?>
            </button>
        </div>
    </div>

</form>

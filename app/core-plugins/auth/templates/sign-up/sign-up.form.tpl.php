<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache='<?= json_encode(array('duration' => 600)) ?>'></span>

<div class="already-have-account">
    <?= $this->trans('core-plugins.auth.sign-up.form.already-have-account', array(
        '%sign-in-url%' => $this->app()->path('auth/sign-in')
    )) ?>
</div>

<?= $this->hooks()->html('form', 'signup_form') ?>
<form id="sign-up-form" role="form"
      action="<?= $this->app()->path('auth/sign-up/target') ?>"
      method="post"
      class="sign-form ajax-form">

    {% include 'core/common/csrf-hidden-input.twig' %}

    <fieldset id="core-inputs">

        <div class="form-group login <?= isset($this->failed_fields) && isset($this->failed_fields['login']) ? 'form-error' : '' ?>">
            <label for="signup-input-login">
                <?= $this->e($this->trans('core-plugins.auth.sign-up.form.login')) ?>
            </label>

            <div class="input-container">
                <input type="text" name="user[login]"
                       id="signup-input-login"
                       class="input input-text login"
                       value="<?= $this->e($this->user->login) ?>"
                       required>
            </div>
        </div>

        <div class="form-group email <?= isset($this->failed_fields) && isset($this->failed_fields['email']) ? 'form-error' : '' ?>">
            <label for="signup-input-email">
                <?= $this->e($this->trans('core-plugins.auth.sign-up.form.email')) ?>
            </label>

            <div class="input-container">
                <input type="email" name="user[email]"
                       id="signup-input-email"
                       class="input input-email email"
                       value="<?= $this->e($this->user->email) ?>"
                       required>
            </div>
        </div>

        <div class="form-group password <?= isset($this->failed_fields) && isset($this->failed_fields['password']) ? 'form-error' : '' ?>">
            <label for="signup-input-password">
                <?= $this->e($this->trans('core-plugins.auth.sign-up.form.password')) ?>
            </label>

            <div class="input-container">
                <input type="password" name="user[password]"
                       id="signup-input-password"
                       class="input input-password password"
                       required>
            </div>
        </div>

        <div class="form-group password-confirmation">
            <label for="signup-input-password-confirmation">
                <?= $this->e($this->trans('core-plugins.auth.sign-up.form.password_confirmation')) ?>
            </label>

            <div class="input-container">
                <input type="password" name="user[password_confirmation]"
                       id="signup-input-password-confirmation"
                       class="input input-password password-confirmation"
                       required="">
            </div>
        </div>

    </fieldset>

    <div class="form-group submit">
        <div class="input-container">
            <button type="submit" class="submit-button">
                <?= $this->e($this->trans('core-plugins.auth.sign-up.form.submit')) ?>
            </button>
        </div>
    </div>

</form>

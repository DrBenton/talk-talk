<?php $this->layout('ajax-nav::layouts/ajax-layout') ?>

<div class="ajax-redirecting">
    <?= $this->transE('core-plugins.ajax-navigation.redirecting') ?>
</div>

<?php $tmpUserProfileContainerId = $this->app()->get('uuid')->numeric() ?>

<!-- temporary HTML container for our user profile display -->
<div id="user-profile-<?= $tmpUserProfileContainerId ?>" class="hidden">
    <?php $this->insert('auth::common/user-display') ?>
</div>

<script>
    require(["jquery"], function ($) {
        // The user profile must be displayed in the #logged-user-container
        var $userProfile = $("#user-profile-<?= $tmpUserProfileContainerId ?>");
        $('#logged-user-container').html($userProfile.html());
        $userProfile.remove();

        // No "signup"/"signin" links in our header
        $("header .sign-up, header .sign-in").addClass("hidden");
        // ...but we show the "signout" link
        $("header .sign-out").removeClass("hidden");

        // Our pages data cache is flushed: since our status has changed,
        // pages content may change!
        $(document).trigger("uiNeedsContentAjaxLoadingCacheClearing");

        // Back to intended page or home, please!
        $(document).trigger("uiNeedsContentAjaxLoading", {
            url: "<?= $this->app()->get('session')->get('url.intended', $this->app()->path('core/home')) ?>",
            target: "#main-content",
            keepAlerts: true
        });
        <?php $this->app()->get('session')->remove('url.intended') ?>

    });
</script>

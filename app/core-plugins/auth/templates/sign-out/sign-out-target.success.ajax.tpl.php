<?php $this->layout('ajax-nav::layouts/ajax-layout') ?>

<div class="ajax-redirecting">
    <?= $this->transE('core-plugins.ajax-navigation.redirecting') ?>
</div>

<script>
    require(["jquery"], function ($) {
        // The user profile must be removed
        $('#logged-user-container').text('');

        // "sign-up"/"sign-in" links are back in our header
        $("header .sign-up, header .sign-in").removeClass("hidden");
        // ...but we hide the "signout" link
        $("header .sign-out").addClass("hidden");

        // Our pages data cache is flushed: since our status has changed,
        // pages content may change!
        $(document).trigger("uiNeedsContentAjaxLoadingCacheClearing");

        // Back to home, please!
        $(document).trigger("uiNeedsContentAjaxLoading", {
            url: "<?= $this->app()->path('core/home') ?>",
            target: "#main-content",
            keepAlerts: true
        });

    });
</script>

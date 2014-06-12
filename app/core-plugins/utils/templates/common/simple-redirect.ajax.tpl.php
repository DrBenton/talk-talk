<?php $this->layout('ajax-nav::layouts/ajax-layout') ?>

<div class="ajax-redirecting">
    <?= $this->transE('core-plugins.ajax-navigation.redirecting') ?>
</div>

<script>
    (function () {
        // Redirection through JS Ajax content loading, please!
        $(document).trigger("uiNeedsContentAjaxLoading", {
            url: "<?= $this->e($this->targetUrl) ?>",
            target: "#main-content"
        });
    })();
</script>

<?php
// Keep Flashes for the next page, please!
$this->app()->get('session')->keepFlashes();
?>

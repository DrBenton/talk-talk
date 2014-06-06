<div class="ajax-loaded-content">

    <script>
        (function () {
            document.getElementById("app-http-status-code")
                    .setAttribute("data-code", "<?= $this->e($this->app()->vars['app.http_status_code']) ?>");
        })();
    </script>

    <?php /* Notifications display through JavaScript (if any) */ ?>
    <?php $this->insert('core::common/alerts-display', array(
        'alerts' => $this->app()->get('flash')->getFlashes('alerts.')
    )) ?>

    <?php if (isset($this->breadcrumb)): /*a custom breadcrumb has been requested*/ ?>
        <?= $this->breadcrumb ?>
    <?php elseif(isset($this->breadcrumbData)): /*default breadcrumb*/ ?>
        <?= $this->insert('core::common/breadcrumb', array('data' => $this->breadcrumbData)) ?>
    <?php endif ?>

    <?= $this->content() ?>

</div><!-- end .ajax-loaded-content -->

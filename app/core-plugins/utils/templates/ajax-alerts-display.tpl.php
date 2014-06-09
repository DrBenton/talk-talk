<div class="ajax-alerts">

    <?php /* Notifications display through JavaScript */ ?>
    <?php $this->insert('core::common/alerts-display', array(
        'alerts' => $this->alerts
    )) ?>

</div><!-- end .ajax-alerts -->
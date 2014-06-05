<?php
if (! $this->app()->vars['isAjax'] || !empty($this->alerts)):
?>

    <?php
    if ($this->app()->vars['isAjax']) {
        // We use a temporary container when in an Ajax context
        $tmpNotificationsContainerId = $this->app()->get('uuid')->numeric();
        $alertsContainerId = 'alerts-' . $tmpNotificationsContainerId;
    } else {
        // We use a real, permanent container when not in an Ajax context
        $alertsContainerId = 'alerts-container';
    }
    ?>

    <?= $this->hooks()->html('alerts_container') ?>
    <div id="<?= $alertsContainerId ?>"
         class="alerts-to-display <?php $this->app()->vars['isAjax'] ? 'hidden' : '' ?>">

        <?php if (!empty($this->alerts)): ?>
            <?= $this->hooks()->html('component.alert') ?>
            <?php foreach(array('error', 'success', 'info') as $alertType): ?>
                <?php
                $alerts = $this->app()->get('flash')->getFlashes("alerts.$alertType.");

                if (!empty($alerts)):
                ?>
                    <div class="alert alert-<?= $alertType ?>">
                        <ul>
                            <?php foreach($alerts as $alert): ?>
                            <li>
                                <?php if (is_string($alert)): ?>
                                    <?= $this->e($alert) ?>
                                <?php elseif (isset($alert['field'])): ?>
                                    <?= $this->e(implode(' ', $alert['messages'])) ?>
                                <?php elseif (isset($alert['secured']) && $alert['secured']): ?>
                                    <?= $alert['message'] ?>
                                <?php endif ?>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

    </div><? /* end .alerts-to-display */ ?>

    <?php if ($this->app()->vars['isAjax']): ?>
        <script>
            require(["jquery"], function ($) {
                // These alerts are displayed in the layout #alerts-container
                var alertToDisplayContainerSelector = "#<?= $alertsContainerId ?>";
                $(document).trigger("uiNeedsAlertDisplayFromHtml", {
                    fromSelector: alertToDisplayContainerSelector
                });
            });
        </script>
    <?php endif ?>

<?php
endif
?>
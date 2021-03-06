<?php if ($this->app()->vars['debug']) { echo '<!-- Alerts data:' . print_r($this->alerts, true) . ' -->'; } ?>
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
                $alertsToDisplay = array();
                array_walk( /* PHP, why can't we use array_filter with associative arrays ? */
                    $this->alerts,
                    function ($alertContent, $alertKey) use (&$alertsToDisplay, $alertType) {
                        if (0 === strpos($alertKey, "alerts.$alertType.")) {
                            $alertsToDisplay[$alertKey] = $alertContent;
                        }
                    }
                );
                if (!empty($alertsToDisplay)):
                ?>
                    <div class="alert alert-<?= $alertType ?>">
                        <ul>
                            <?php foreach($alertsToDisplay as $alert): ?>
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

    </div><?php /* end .alerts-to-display */ ?>

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

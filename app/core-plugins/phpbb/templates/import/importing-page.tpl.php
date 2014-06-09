<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 600))) ?>"></span>

<div class="imports-container flight-component"
     data-component="<?= $this->e(
            implode(',', array(
                'app-modules/phpbb/components/ui/phpbb-imports-gui',
                'app-modules/phpbb/components/data/phpbb-imports-handler',
            ))
        )?>"
     data-items-types="<?= $this->e(json_encode($this->itemsTypes)) ?>">

    <div class="intro">
        <?= nl2br($this->transE('core-plugins.phpbb.import.importing.intro')) ?>
    </div>

    <?= $this->hooks()->html('component.progress') ?>
    <?php foreach ($this->itemsTypes as $itemType => $itemLabel): ?>
        <div id="phpbb-<?= $this->e($itemType) ?>-import-container">
            <h4><?= $this->e($itemLabel) ?></h4>

            <p class="import-preparation hidden">
                <?= $this->transE('core-plugins.phpbb.import.importing.item-type-import-preparation') ?>
            </p>
            <p class="import-in-progress hidden">
                <?= $this->transE('core-plugins.phpbb.import.importing.item-type-import-in-progress') ?>
            </p>

            <p class="nb-items-to-import hidden">
                <b class="number">0</b> items to import
            </p>

            <progress value="0" max="100" class="progress-component"></progress>

            <span class="progress-percentage">0</span>%

            <div class="done hidden">
                Done! (<span class="duration"></span> seconds)
            </div>
        </div>
    <?php endforeach ?>

    <div class="please-wait hidden">
        <?= $this->transE('core-plugins.phpbb.import.importing.please-wait') ?>
    </div>
    <button class="start-import"><?= $this->transE('core-plugins.phpbb.import.importing.form.submit') ?></button>
</div>
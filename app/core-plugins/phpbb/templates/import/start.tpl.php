<?php $this->layout( $this->app()->vars['isAjax'] ? 'ajax-nav::layouts/ajax-layout' : 'core::layouts/main-layout') ?>

<span class="ajax-loading-data" data-ajax-cache="<?= $this->e(json_encode(array('duration' => 600))) ?>"></span>

<div class="intro">
    <?= $this->trans('core-plugins.phpbb.import.start.form.intro') ?>
</div>

<?= $this->hooks()->html('form', 'phpbb_db_settings_form') ?>
<form id="phpbb-import-start-form" role="form"
      action="<?= $this->app()->path('phpbb/import/start/target') ?>"
      method="post"
      class="ajax-form">

    <?php $this->insert('core::common/csrf-hidden-input') ?>

    <fieldset id="core-inputs">

        <div class="form-group driver">
            <label for="phpbb-data-input-driver">
                <?= $this->transE('core-plugins.phpbb.import.start.form.driver') ?>
            </label>

            <div class="input-container">
                <select name="db-settings[driver]"
                        id="phpbb-data-input-driver"
                        class="input input-select driver">
                        required>
                    <option value="mysql" <?= $this->dbSettings['driver'] === 'mysql' ? 'selected' : '' ?>>MySQL</option>
                    <option value="pgsql" <?= $this->dbSettings['driver'] === 'pgsql' ? 'selected' : '' ?>>PostgreSQL</option>
                </select>

                <p class="help-block">
                    <?= $this->transE('core-plugins.phpbb.import.start.form.driver.help') ?>
                </p>
            </div>

        </div>

        <div class="form-group host">
            <label for="phpbb-data-input-host">
                <?= $this->transE('core-plugins.phpbb.import.start.form.host') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[host]"
                       id="phpbb-data-input-host"
                       class="input input-text host"
                       value="<?= $this->e($this->dbSettings['host']) ?>"
                       required>

                <p class="help-block">
                    <?= $this->transE('core-plugins.phpbb.import.start.form.host.help') ?>
                </p>
            </div>
        </div>

        <div class="form-group username">
            <label for="phpbb-data-input-username">
                <?= $this->transE('core-plugins.phpbb.import.start.form.username') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[username]"
                       id="phpbb-data-input-username"
                       class="input input-text username"
                       value="<?= $this->e($this->dbSettings['username']) ?>"
                       required>
            </div>
        </div>

        <div class="form-group password">
            <label for="phpbb-data-input-password">
                <?= $this->transE('core-plugins.phpbb.import.start.form.password') ?>
            </label>

            <div class="input-container">
                <input type="password" name="db-settings[password]"
                       id="phpbb-data-input-password"
                       class="input input-password password">
            </div>
        </div>

        <div class="form-group database">
            <label for="phpbb-data-input-database">
                <?= $this->transE('core-plugins.phpbb.import.start.form.database') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[database]"
                       id="phpbb-data-input-database"
                       class="input input-text database"
                       value="<?= $this->e($this->dbSettings['database']) ?>"
                       required>
            </div>
        </div>

        <div class="form-group prefix">
            <label for="phpbb-data-input-prefix">
                <?= $this->transE('core-plugins.phpbb.import.start.form.prefix') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[prefix]"
                       id="phpbb-data-input-prefix"
                       class="input input-text prefix"
                       value="<?= $this->e($this->dbSettings['prefix']) ?>"
                       required>
            </div>
        </div>

        <div class="form-group port">
            <label for="phpbb-data-input-port">
                <?= $this->transE('core-plugins.phpbb.import.start.form.port') ?>
            </label>

            <div class="input-container">
                <input type="number" name="db-settings[port]"
                       id="phpbb-data-input-port"
                       class="input input-text port"
                       value="<?= $this->e($this->dbSettings['port']) ?>"
                       required>
            </div>
        </div>

        <div class="form-group charset">
            <label for="phpbb-data-input-charset">
                <?= $this->transE('core-plugins.phpbb.import.start.form.charset') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[charset]"
                       id="phpbb-data-input-charset"
                       class="input input-text charset"
                       value="<?= $this->e($this->dbSettings['charset']) ?>"
                       required>

                <p class="help-block">
                    <?= $this->transE('core-plugins.phpbb.import.start.form.charset.help') ?>
                </p>
            </div>
        </div>

        <div class="form-group collation">
            <label for="phpbb-data-input-collation">
                <?= $this->transE('core-plugins.phpbb.import.start.form.collation') ?>
            </label>

            <div class="input-container">
                <input type="text" name="db-settings[collation]"
                       id="phpbb-data-input-collation"
                       class="input input-text collation"
                       value="<?= $this->e($this->dbSettings['collation']) ?>"
                       required>

                <p class="help-block">
                    <?= $this->transE('core-plugins.phpbb.import.start.form.collation.help') ?>
                </p>
            </div>
        </div>

    </fieldset>

    <div class="form-group submit">
        <div class="input-container">
            <button type="submit" class="submit-button">
                <?= $this->transE('core-plugins.phpbb.import.start.form.submit') ?>
            </button>
        </div>
    </div>

</form>

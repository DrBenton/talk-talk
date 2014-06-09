<?php

use Illuminate\Database\Eloquent\Model;

$app->vars['phpbb.import.provider.name'] = 'phpbb-import';
$app->vars['phpbb.import.provider.version'] = 0.1;

$app->defineFunction(
    'phpbb.import.add_provider_data',
    function (Model $model, $sourceEntityId) use ($app) {
        $model->provider = $app->vars['phpbb.import.provider.name'];
        $model->provider_version = $app->vars['phpbb.import.provider.version'];
        $model->provider_data = json_encode(
            array(
                'import_ts' => time(),
                'src_id' => (int) $sourceEntityId,
            )
        );
    }
);

$app->defineFunction(
    'phpbb.import.clear_imports_ids_mappings',
    function () use ($app) {
        // Okay, let's remove these heavy Session vars
        foreach (array('users', 'forums', 'topics', 'post') as $importType) {
            $app->get('session')->remove('phpbb.import.' . $importType . '.ids_mapping');
        }
    }
);

<?php

use Illuminate\Database\Eloquent\Model;

$app['phpbb.import.provider.name'] = 'phpbb-import';
$app['phpbb.import.provider.version'] = 0.1;

$app['phpbb.import.add_provider_data'] = $app->protect(
    function (Model $model) use ($app) {
        $model->provider = $app['phpbb.import.provider.name'];
        $model->provider_version = $app['phpbb.import.provider.version'];
        $model->provider_data = json_encode(
            array(
                'import_ts' => time()
            )
        );
    }
);

<?php

use TalkTalk\Core\PhpFilesPacking\Packer;

call_user_func(
    function () {

        $vendorsPackedFilePath = __DIR__ . '/app/var/cache/php-packs/vendors.pack.php';

        $hasPackedFiles = file_exists($vendorsPackedFilePath);
        if ($hasPackedFiles) {
            require_once $vendorsPackedFilePath;
        }

        // Composer init
        $vendorsPath = __DIR__ . '/vendor/php';
        require_once $vendorsPath . '/autoload.php';

        // Go! Go! Go!
        $appInitClosure = require_once __DIR__ . '/app/bootstrap/app.php';
        $app = call_user_func($appInitClosure);
        $app->run();

        if (!$hasPackedFiles) {
            Packer::compileAppFiles($app);
            Packer::compileAppVendors($app);
        }

    }
);

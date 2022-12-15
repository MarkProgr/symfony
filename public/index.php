<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $s3Client = new \Aws\S3\S3Client([
        ''
    ]);
    $s3Client->registerStreamWrapper();

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

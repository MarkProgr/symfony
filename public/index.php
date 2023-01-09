<?php

use App\Kernel;
use Aws\S3\S3Client;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    $s3Client = new S3Client([
        'region' => 'us-west-2',
        'version' => 'latest',
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => 'storage_login',
            'secret' => 'storage_password',
        ],
        'endpoint' => 'http://minio:9000',
    ]);
    $s3Client->registerStreamWrapper();

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

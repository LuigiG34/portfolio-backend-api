<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    /** @var string $appEnv */
    $appEnv = $context['APP_ENV'];
    return new Kernel($appEnv, (bool) $context['APP_DEBUG']);
};
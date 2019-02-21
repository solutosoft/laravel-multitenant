<?php

$app = new \Illuminate\Foundation\Application(
    realpath(__DIR__ . '/../')
);

$app->singleton(
    \Illuminate\Contracts\Console\Kernel::class,
    \Illuminate\Foundation\Console\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \Illuminate\Foundation\Exceptions\Handler::class
);

return $app;
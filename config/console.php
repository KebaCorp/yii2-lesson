<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$mongo = require __DIR__ . '/mongo.php';

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components'          => [
        'cache'   => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user'    => [
            'identityClass'   => \app\models\user\User::class,
            'enableAutoLogin' => true,
        ],
        'log'     => [
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'      => $db,
        'mongodb' => $mongo,
    ],
    'params'              => $params,
    'controllerMap'       => [
        'fixture' => [ // Fixture generation command line.
                       'class' => 'yii\faker\FixtureController',
        ],

        'migrate-mongodb' => [
            'class'               => \yii\mongodb\console\controllers\MigrateController::class,
            'migrationCollection' => 'migration',
            'migrationPath'       => '@app/migrations/mongodb',
            'db'                  => 'mongodb',
        ],

        'migrate-mongodb-data' => [
            'class'               => \yii\mongodb\console\controllers\MigrateController::class,
            'migrationCollection' => 'migration_data',
            'migrationPath'       => '@app/migrations/mongodb-data',
            'db'                  => 'mongodb',
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

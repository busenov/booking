<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable'       => 'auth_items',
            'itemChildTable'  => 'auth_item_children',
            'assignmentTable' => 'auth_assignments',
            'ruleTable'       => 'auth_rules',
//            'defaultRoles'    => ['user'],// роль которая назначается всем пользователям по умолчанию
        ],
    ],
];

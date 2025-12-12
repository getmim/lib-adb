<?php

return [
    '__name' => 'lib-adb',
    '__version' => '0.3.0',
    '__git' => 'git@github.com:getmim/lib-adb.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-adb' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibAdb\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-adb/library'
            ]
        ],
        'files' => []
    ],
    '__inject' => [
        [
            'name' => 'libAdb',
            'children' => [
                [
                    'name' => 'bin',
                    'question' => 'ADB binary path file',
                    'rule' => '!^.+$!'
                ]
            ]
        ]
    ]
];

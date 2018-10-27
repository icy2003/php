<?php

return [
    'Logger' => [
        'isLog' => true,
        'infoTemplete' => '{date} [INFO] {file}(line:{line}) {message}',// 2018-10-27 19:01:21 [INFO] test.php(line:8) hello world
        'errorTemplete' => '{date} [ERRO] {errfile}(line:{errline}) {errstr}{errno}',// 2018-10-27 19:01:21 [ERRO] test.php(line:4) Undefined variable: var(8)
        'dateFormat' => 'Y-m-d H:i:s',
        'type' => 'file,print',//file,print,db
        'file' => [
            'filePath' => '@icy2003/logs',
            'fileName' => function () {// string|callable
                return date('Y-m-d') . '.log';
            },
            'flag' => FILE_APPEND // FILE_BINARY|FILE_APPEND
        ],
        'print' => [
            'function' => ['\icy2003\ihelpers\Logger', 'echo'],// string|callable
        ],
    ],
    'Language' => [
        'language' => 'zh-cn',
        'basePath' => '@icy2003/language'
    ]
];
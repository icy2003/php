<?php

return [
    'Logger' => [
        'isLog' => true,
        'infoTemplete' => '{date} [INFO] {message} {file}(line:{line})',// 2018-10-27 19:01:21 [INFO] hello world test.php(line:8)
        'errorTemplete' => '{date} [ERROR] {errstr} {errfile}(line:{errline}, errno:{errno})',// 2018-10-27 19:01:21 [ERROR] Undefined variable: var test.php(line:4, errno:8)
        'dateFormat' => 'Y-m-d H:i:s',
        'type' => 'file,print',//file,print,db
        'info' => [
            'function' => ['\icy2003\ihelpers\Logger', 'iEcho'],// string|callable
        ],
        'file' => [
            'filePath' => '@icy2003/logs',
            'fileName' => function () {// string|callable
                return date('Y-m-d') . '.log';
            },
            'flag' => FILE_APPEND // FILE_BINARY|FILE_APPEND
        ],
    ],
    'Language' => [
        'language' => 'zh-cn',
        'basePath' => '@icy2003/language'
    ]
];
<?php

use icy2003\php\BaseI;

require __DIR__ . '/BaseI.php';

/**
 * @namespace
 * @encoding UTF-8
 *
 * @author icy2003 <2317216477@qq.com>
 *
 * @see https://github.com/icy2003
 */
class I extends BaseI
{
}

spl_autoload_register(['I', 'autoload'], true, true);

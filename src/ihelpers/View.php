<?php
/**
 * Class View
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use Exception;
use icy2003\php\I;
use icy2003\php\icomponents\file\LocalFile;
use Throwable;

/**
 * 视图渲染类
 */
class View
{

    /**
     * 布局文件，支持别名
     *
     * @var string
     */
    public $layout = false;

    /**
     * 布局文件的参数
     *
     * @var array
     */
    public $layoutParams = [];

    /**
     * 渲染视图
     *
     * @param string $view 视图文件名
     * @param array $params 参数
     *
     * @return string
     */
    public function render($view, $params = [])
    {
        $content = $this->_renderContent($view, $params);
        $layoutFile = I::getAlias($this->layout);
        return $this->_renderContent($layoutFile, ['content' => $content, 'layoutParams' => $this->layoutParams]);
    }

    /**
     * 渲染视图，不使用布局
     *
     * @param string $view 视图文件名
     * @param array $params 参数
     *
     * @return string
     */
    public function renderPartial($view, $params = [])
    {
        return $this->_renderContent($view, $params);
    }

    /**
     * 渲染视图内容
     *
     * @param string $view 视图文件名
     * @param array $params 参数
     *
     * @return string
     */
    protected function _renderContent($view, $params = [])
    {
        $viewFile = I::getAlias($view);
        if (false === (new LocalFile())->isFile($viewFile)) {
            throw new Exception('找不到视图文件：' . $viewFile);
        }
        return $this->_renderPhpFile($viewFile, $params);
    }

    /**
     * 渲染 PHP 文件
     *
     * @param string $viewFile 视图文件名
     * @param array $params 参数
     *
     * @return string
     */
    protected function _renderPhpFile($viewFile, $params)
    {
        $level = ob_get_level();
        ob_start();
        ob_implicit_flush(0);
        extract($params, EXTR_OVERWRITE);
        try {
            require $viewFile;
            return ob_get_clean();
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}

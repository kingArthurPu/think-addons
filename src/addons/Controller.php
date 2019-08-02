<?php

namespace think\addons;

use think\facade\Env;
use think\facade\Config;
use think\Loader;
use think\facade\Hook;
/**
 * 插件基类控制器
 * Class Controller
 * @package think\addons
 */
class Controller extends \think\Controller
{
    // 当前插件操作
    public $addon = null;
    public $module = null;
    public $controller = null;
    public $action = null;
    // 当前template
    protected $template;
    // 模板配置信息
    protected $config = [
        'type' => 'Think',
        'view_path' => '',
        'view_suffix' => 'html',
        'strip_space' => true,
        'view_depr' => DIRECTORY_SEPARATOR,
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '{',
        'taglib_end' => '}',
        'layout_on' => false,
        'layout_name' => 'layout',
        'tpl_cache' => true,
        'tpl_replace_string' => '',
        'taglib_pre_load' => ''
    ];

    /**
     * 架构函数
     * @param App $app App对象
     * @access public
     */
    public function __construct($app = null)
    {
        parent::__construct($app);
    }


    public function initialize()
    {
        parent::initialize();
        // 处理路由参数
        $route = [
            $this->request->param('addon'),
            $this->request->param('module'),
            $this->request->param('control'),
            $this->request->param('action'),
        ];

        // 是否自动转换控制器和操作名
        $convert = Config::get('app.url_convert');
        // 格式化路由的插件位置
        $this->action = $convert ? strtolower(array_pop($route)) : array_pop($route);
        $this->controller = $convert ? strtolower(array_pop($route)) : array_pop($route);
        $this->module = $convert ? strtolower(array_pop($route)) : array_pop($route);
        $this->addon = $convert ? strtolower(array_pop($route)) : array_pop($route);



        // 生成view_path
        $view_path = Env::get('addons_path') . $this->addon . ($this->module ? '/' . $this->module : '') . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;

        // 重置配置
        $this->config['view_path'] = $view_path;
        $this->config['tpl_cache'] = !\config('app_debug');

        // 初始化配置信息
        $this->config = array_merge($this->app['config']->get('template.'), $this->config);
        foreach ($this->config as $key => $item) {
            $this->view->engine->config($key, $item);
        }
    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array $vars 模板输出变量
     * @param array $replace 模板替换
     * @param array $config 模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $controller = Loader::parseName($this->controller);
        if ('think' == strtolower($this->config['type']) && $controller && 0 !== strpos($template, '/')) {
            $depr = $this->config['view_depr'];
            $template = str_replace(['/', ':'], $depr, $template);

            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $this->action;
            } elseif (false === strpos($template, $depr)) {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
            }
        }
        return parent::fetch($template, $vars, $replace, $config);
    }
}

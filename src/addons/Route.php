<?php

namespace think\addons;

use think\facade\Env;
use think\facade\Hook;
use think\facade\Request;

/**
 * 插件执行默认控制器
 * Class AddonsController
 * @package think\addons
 */
class Route extends Controller
{
    # 资源类型
    protected $mimeType = [
        'xml' => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js' => 'text/javascript,application/javascript,application/x-javascript',
        'css' => 'text/css',
        'rss' => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf' => 'application/pdf',
        'text' => 'text/plain',
        'png' => 'image/png',
        'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
        'gif' => 'image/gif',
        'csv' => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    /**
     * 插件执行
     */
    public function execute()
    {
        if (!empty($this->addon) && !empty($this->controller) && !empty($this->action)) {
            // 获取类的命名空间
            $class = get_addon_class($this->addon, 'controller', $this->controller, $this->module);

            if (class_exists($class)) {
                $model = new $class();
                Request::setModule($this->module);
                Request::setController($this->controller);
                Request::setAction($this->action);
                Env::set("ADDONS_NAME",$this->addon);
                if ($model === false) {
                    abort(500, lang('addon init fail'));
                }
                // 调用操作
                if (!method_exists($model, $this->action)) {
                    abort(500, lang('Controller Class Method Not Exists'));
                }
                // 监听addons_init
                Hook::listen('addons_init', $this);
                return call_user_func_array([$model, $this->action], [$this->request]);
            } else {
                abort(500, lang('Controller Class Not Exists'));
            }
        }
        abort(500, lang('addon cannot name or action'));
    }

    /**
     * 样式读取
     */
    public function assets()
    {
        $assets_path = dirname(__DIR__, 5);
        $path = $this->request->pathinfo();
        $len = strlen('assets/' . $this->module);
        $ext = $this->request->ext();
        if ($ext) {
            $type = 'text/html';
            $content = file_get_contents($assets_path . '/addons/' . $this->module . '/assets' . substr($path, $len));
            if (array_key_exists($ext, $this->mimeType)) {
                $type = $this->mimeType[$ext];
            }
            return response($content, 200, ['Content-Length' => strlen($content)])->contentType($type);
        }
    }


}

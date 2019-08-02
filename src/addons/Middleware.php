<?php


namespace think\addons;

use think\facade\Config;
use think\facade\Env;

class Middleware
{
    /**
     * 插件控制器
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 路由地址
        $pathinfo = explode('/', $request->path());
        $rules = explode('.', $pathinfo[1]);
        $request->setModule(array_shift($rules));
        $request->setController(join('/', $rules));
        $request->setAction($pathinfo[2]);

        // 生成view_path
        $view_path = Env::get('addons_path') . $request->module() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        Config::set('template.view_path', $view_path);

        return $next($request);
    }
}

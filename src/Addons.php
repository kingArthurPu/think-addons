<?php

namespace think;
use think\facade\Env;
/**
 * 插件基类
 * Class Addns
 * @package think\addons
 */
abstract class Addons extends Controller
{
    // 当前错误信息
    protected $error;

    /**
     * $info = [
     *  'name'          => 'Test',
     *  'title'         => '测试插件',
     *  'description'   => '用于thinkphp5的插件扩展演示',
     *  'status'        => 1,
     *  'author'        => 'kingarthur',
     *  'version'       => '0.1'
     * ]
     */
    public $info = [];
    public $addons_path = '';
    public $config_file = '';

    // 初始化
    protected function initialize()
    {
        // 获取当前插件目录
        $this->addons_path = Env::get('addons_path') . $this->getName() . DIRECTORY_SEPARATOR;

        // 重新定义模板的根目录
//        if ($this->view) {
//            $this->view->config('view_path', $this->addons_path );
//        }

    }
    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();

    /**
     * 获取插件信息
     * @return array
     */
    final public function getInfo()
    {
        $info_path = $this->addons_path . 'info.ini';
        if (is_file($info_path)) {
            $info = parse_ini_file($info_path);
            if (is_array($info)) {
                $this->info = array_merge($this->info, $info);
            }
        }
        return $this->info;
    }

    // 获取扩展项目的配置信息
    public function getConfig()
    {
        $file = env('addons_path') . $this->getAddonName() . '/config.php';
        if (!file_exists($file)) return false;
        return require_once $file;
    }


    /**
     * 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = array_reverse(explode('\\', get_class($this)));
        return $data[1];
    }

    // 获取扩展配置基本信息
    public function getManifest()
    {
        $file = env('addons_path') . $this->getAddonName() . '/manifest.php';
        if (!file_exists($file)) return false;
        return require_once $file;
    }

    // 获取继承者的类名称  不含Addon的小写字母名称
    protected function getAddonName()
    {
        $arr = explode('\\', get_class($this));
        return $arr[1];
    }

    // 创建安装成功标识文件
    protected function createInstallFile()
    {
        $file = env('addons_path') . $this->getAddonName() . '/install.lock';
        if (file_exists($file)) return true;
        return file_put_contents($file, 'dir', FILE_USE_INCLUDE_PATH);
    }

    protected function deleteInstallFile()
    {
        $file = env('addons_path') . $this->getAddonName() . '/install.lock';
        if (!file_exists($file)) return true;
        return unlink($file);
    }

    /**
     * 重写模板渲染
     * @param string $template
     * @param array $vars
     * @param array $config
     * @return mixed|string
     * @throws \Exception
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        // 拼接插件视图文件的默认目录
        return $this->view->fetch($this->addons_path.$template.'.'.config('template.view_suffix'), $vars, $config);
    }


    /**
     * 创建扩展
     * @param string $addonName
     * @param int $type 0单模块 1多模块
     * @param array $config 配置
     */
    public function createAddon($addonName = '', $type = 0, $config = [])
    {
        $addonName = strtolower($addonName);
        $filePath = env('addons_path') . $addonName;
        file_put_contents($filePath . '/config.php');
    }

    /**
     * 检查配置信息是否完整
     * @return bool
     */
    final public function checkInfo()
    {
        $info_check_keys = ['name', 'title', 'description', 'status', 'author', 'version'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $this->getInfo())) {
                return false;
            }
        }
        return true;
    }

}
    

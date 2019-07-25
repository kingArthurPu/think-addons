# kingarthur/think-addons

#### 介绍
基于thinkPHP5.1的插件扩展



#### 安装教程

composer require kingarthur/think-addons

#### 使用说明

1. 安装以后运行框架，将自动在框架根目录下生成addons目录;
2. 在config目录下新增配置文件`addons.php`:
    ```php
        <?php

            return [
                // 是否自动读取取插件钩子配置信息（默认是关闭）
                'autoload' => false,
                // 当关闭自动获取配置时需要手动配置hooks信息
                'hooks' => [
                    // 可以定义多个钩子
                    'testhook'=>'test' // 键为钩子名称，用于在业务中自定义钩子处理，值为实现该钩子的插件，
                    // 多个插件可以用数组也可以用逗号分割
                ]
            ];
    ```
3. 在addons目录中创建test目录，目录名称test即为插件名称;
4. 在test中创建`Widget.php`,代码如下:
    ```php
        <?php

            namespace addons\test;	// 注意命名空间规范
            use addons\test\model\Manage;
            use think\Addons;
            use think\App;
            use think\facade\Env;

            /**
            * 插件测试
            * @author byron sampson
            */
            class Widget extends Addons	// 需继承think\addons\Addons类
            {
                // 该插件的基础信息
                public $info = [
                    'name' => 'test',	// 插件标识
                    'title' => '插件测试',	// 插件名称
                    'description' => 'thinkph5插件测试',	// 插件简介
                    'status' => 0,	// 状态
                    'author' => 'zhangsan',
                    'version' => '0.1'
                ];

                /**
                * 插件安装方法
                * @return bool
                */
                public function install()
                {
                    return true;
                }

                /**
                * 插件卸载方法
                * @return bool
                */
                public function uninstall()
                {
                    return true;
                }

                /**
                * 实现的testhook钩子方法
                * @return mixed
                */
                public function testhook($param)
                {
                    // 调用钩子时候的参数信息
                    print_r($param);
                    // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
                    print_r($this->getConfig());
                    $this->assign("name","zhangsan");
                    echo  $this->fetch("info");
                }
            }
    ```

#### 参与贡献

@kingarthur


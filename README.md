# kingarthur/think-addons

#### 介绍
基于thinkPHP5.1的插件扩展



#### 安装教程

composer require kingarthur/think-addons


安装以后运行框架，将自动在框架根目录下生成addons目录;


#### 配置

在config目录下新增配置文件`addons.php`:
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

#### 创建插件

> 创建的插件可以在view视图中使用，也可以在php业务逻辑中使用

在addons目录中创建test目录，目录名称test即为插件名称;

在test中创建`Widget.php`,代码如下:

```php
<?php

    namespace addons\test;	// 注意命名空间规范
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
            $this->assign("name","kingarthur-addons");
            echo  $this->fetch("info");
        }
    }
```

#### 创建钩子模板文件

在`test`目录下新建`info.html`视图文件:
```html
    <body>
        <h1>hello tpl---</h1>

        如果插件中需要有链接或提交数据的业务，可以在插件中创建controller业务文件，
        要访问插件中的controller时使用addons_url生成url链接。
        如下：
        <a href="{:addons_url('test://Action/link',['g'=>'xxxxx'])}">link test</a>
        格式为：
        test为插件名，Action为controller中的类名，link为controller中的方法
        <hr>
        {$name}
    </body>
```

#### 创建插件的controller文件

> 在test目录中创建controller目录，在controller目录中创建Index.php文件 controller类的用法与tp5.1中的controller一致

```php
<?php
namespace addons\test\controller;

use think\addons\Controller;

class Action extends Controller
{
    public function link()
    {
        return $this->fetch();
    }
}
```



#### 使用钩子

> 创建好插件后就可以在正常业务中使用该插件中的钩子了 使用钩子的时候第二个参数可以省略

- 模板中使用钩子

```html
<div>{:hook('testhook', ['id'=>1])}</div>
```

- php业务中使用

```php
hook('testhook', ['id'=>1])
```


另外，可以在`test`目录中创建`model`以及`validate`，使用方式同模块中使用一致



#### 特别感谢

zzstudio/think-addons

[git地址](https://github.com/zz-studio/think-addons)

[composer地址](https://packagist.org/packages/zzstudio/think-addons)


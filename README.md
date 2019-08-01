# kingarthur/think-addons

# think-addons
- The ThinkPHP 5.1 Addons Package
- 站在巨人的肩膀上二次开发的
- 支持单模块和多模块
- 支持模板布局
- 支持模块参数配置(参数和config.template 一模一样) 详见下面的代码示例[模板配置参数]

## 安装
> composer require kingarthur/think-addons


安装完成后访问系统时会在项目根目录生成名为`addons`的目录，在该目录中创建需要的插件。

## 配置

- 在config目录下新增配置文件`addons.php`:

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

- 自己配置的话可以写在公共配置

```

'addons'=>[
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

## 创建插件

> 创建的插件可以在view视图中使用，也可以在php业务中使用
 


下面写两个例子：

### 例子1: 单模块

#### 1.创建test插件

> 在addons目录中创建test目录(小写)

> 在test目录中创建controller目录(小写)

> 创建控制器Action.php控制器(类文件首字母需大写)

  
```
<?php
namespace addons\test\controller;

class Action
{
    public function link()
    {
        echo 'hello link';
    }
}
```

- controller类的用法与tp5.1中的controller一致

- 如果需要使用view模板则需要继承`\think\addons\Controller`类

- 单模块URL访问 addons_url('test://action/link',['name'=>'test/action'])  参数可与省略

- 多模块URL访问 addons_url('test://admin@index/link')

> 在test目录中创建view目录(小写)
> 在view目录中创建action/link.html文件(小写)


```
<?php
namespace addons\test\controller;

use think\addons\Controller;

class Action extends Controller
{
    /**
     * 模板配置参数
     * @var array
     */
    protected $config = [
        'layout_on' => true,
        'layout_name' => '/../../../../application/admin/view/layout',
        'taglib_pre_load' => 'app\admin\taglib\Custom',
        'tpl_replace_string'=>[
            '__STATIC__' => '/static/admin'
        ]
    ];
    
    
    public function link()
    {
        echo 'link test';
        return $this->fetch();
    }
}
```

#### 2.创建钩子实现类

> 在test目录中创建Test.php类文件(类文件首字母需大写)


```
<?php
namespace addons\signle;	// 注意命名空间规范

use think\Addons;

/**
 * 插件测试
 * @author okcoder
 */
class Test extends Addons	// 需继承think\addons\Addons类
{
	// 该插件的基础信息
    public $info = [
        'name' => 'test',	// 插件标识
        'title' => '插件测试',	// 插件名称
        'description' => 'thinkph5.1插件测试',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'okcoder',
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
		// 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info');
    }

}
```

#### 3.创建插件配置文件
> 在test目录中创建config.php类文件(插件配置文件可以省略)


```
<?php
return [
    'display' => [
        'title' => '是否显示:',
        'type' => 'radio',
        'options' => [
            '1' => '显示',
            '0' => '不显示'
        ],
        'value' => '1'
    ]
];
```

#### 4.创建钩子模板文件
> 在test目录中创建info.html模板文件，钩子在使用fetch方法时对应的模板文件。


```
<h1>hello tpl</h1>

如果插件中需要有链接或提交数据的业务，可以在插件中创建controller业务文件，
要访问插件中的controller时使用addon_url生成url链接。
如下：、

1.单模块
<a href="{:addons_url('signle://admin/link')}">link test</a>

2.多模块
<a href="{:addons_url('signle://admin@index/link')}">link test</a>


格式为：
test为插件名，Action为controller中的类名，link为controller中的方法
```

#### 5.使用钩子
> 创建好插件后就可以在正常业务中使用该插件中的钩子了

> 使用钩子的时候第二个参数可以省略

> 模板中使用钩子 `<div>{:hook('testhook', ['id'=>1])}</div>`

> php业务中使用 `hook('testhook', ['id'=>1])` 只要是thinkphp5正常流程中的任意位置均可以使用

#### 6.资源路径
> 在test目录中创建assets目录

> 在assets目录中创建style.css文件

> 模板中使用钩子 `<link href="{:assets_url('test/style.css')}" />`



### 例子2: 多模块

> 与单模块基本相同 只是多了一层目录

### 插件目录结构


 ```
 ·
 ├── addons
 │   ├── test                 // 单模块
 │   │   ├── controller         // 控制器
 │   │   │   └── Action.php
 │   │   │   └── ...
 │   │   ├── view
 │   │   │   ├── action
 │   │   │   │   ├── link.html
 │   │   │   │   ├── ...  
 │   │   │   └── ...  
 │   │   ├── model
 │   │   ├── validate
 │   │   └── ...
 │   ├── many                   // 多模块
 │   │   ├── admin
 │   │   │   ├── controller
 │   │   │   │   ├── Index.php
 │   │   │   │   └── ...
 │   │   │   ├── model
 │   │   │   ├── validate
 │   │   │   ├── view
 │   │   │   │   ├── index
 │   │   │   │   │   ├── index.html
 │   │   │   │   │   ├── ...
 │   │   │   │   └── ...
 │   │   │   └── ...   
 │   │   ├── api
 │   │   └── ...
 │   └── ...
 ├── application
 ├── config
 │   ├── addons.php
 │   └── ...
 └─ ...
 
```



#### 特别感谢

zzstudio/think-addons

[gitee地址](https://github.com/zz-studio/think-addons)



OkCoder/think-addons

[gitee地址](https://gitee.com/okcoder/think-addons)
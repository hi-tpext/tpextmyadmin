# tpextmyadmin for ThinkPHP6

此版本本1.0和3.0有所不同，没有强制依赖`tpext`相关的库，需要自己添加依赖。

`composer require` 命令或编辑网站根目录下的`composer.json`文件，添加以下依赖：

然后执行`composer update`命令，更新依赖。

```josn
"require": {
    "topthink/framework": "^6.1.4",
    "topthink/think-orm": "^2.0",
    "ichynul/tpextmyadmin": "^5.0.1",
    "ichynul/tpext": "^3.0",
    "ichynul/tpext-tinyvue": "^5.0.1",
    "ichynul/tpextmanager": "^3.0",
    "ichynul/lightyearadmin": "^1.0"
},
```

`ichynul/lightyearadmin`  是为了兼容bootstrap的页面（如dashbord）。

`ichynul/tpext-tinyvue` 是替换`ichynul/tpextbuilder`的依赖，基于Vue3和tinyvue框架。

支持tp6以上版本，`tpextmyadmin3.0`的可以修改`composer.json`升级，

升级后页面样式乱，访问一次 `/admin/extension/prepare`刷新资源。

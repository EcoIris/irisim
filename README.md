## 介绍

使用laravel框架，PHPSocketIO + layui 实现web端即时通信，仅作为个人学习使用。
- [laravel](https://learnku.com/docs/laravel/7.x/installation/7447) 7.6
- [php](https://www.php.net/downloads) >= 7.2.5
- [PHPSocketIO](https://github.com/walkor/phpsocket.io)
- [layui](https://www.layui.com/doc/modules/layim.html)

## 配置

根目录:```irisim/public```

数据库SQL文件:```irisim/irisim.sql```

PHPSocketIO核心文件:```irisim/public/start.php```

layui核心目录:``` irisim/public/asset/layui```

## 测试
导入数据库文件,生成测试账号1:abcd123  测试账号2:abcd456  默认密码:123456

在```irisim/public```目录下打开doc窗口,运行命令```php start.php start```,开启3333端口进程

用两种浏览器分别打开配置到根目录的ip或虚拟域名,使用测试账号1和测试账号2登录即可测试

## 项目截图

<p align="center">
<img src="http://img.xiliujia.com/src/login.png">
</p>
<p align="center">
<img src="http://img.xiliujia.com/src/index.png">
</p>
<p align="center">
<img src="http://img.xiliujia.com/src/find.png">
</p>
<p align="center">
<img src="http://img.xiliujia.com/src/msg.png">
</p>
<p align="center">
<img src="http://img.xiliujia.com/src/background.png">
</p>

## 介绍

使用laravel + PHPSocketIO + layui 实现web端仿QQ即时通讯IM，仅作为个人学习使用。
- [laravel7.6](https://learnku.com/docs/laravel/7.x/installation/7447) 
- [php >= 7.2.5](https://www.php.net/downloads) 
- [PHPSocketIO](https://github.com/walkor/phpsocket.io)
- [layui](https://www.layui.com/doc/modules/layim.html)

## 配置

项目根目录:```irisim/public```

数据库SQL文件:```irisim/irisim.sql```

PHPSocketIO核心文件:```irisim/public/start.php```

layui核心目录:``` irisim/public/asset/layui```

## 测试
导入数据库文件,生成测试账号1:```admin```  测试账号2:```root```  默认密码:```123456```

在```irisim/public```目录下打开doc窗口,运行命令```php start.php start```,开启3333端口进程

用两种浏览器分别访问根目录地址,使用测试账号1和测试账号2登录即可测试

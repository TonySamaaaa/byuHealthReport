# byuHealthReport

**程序统仅供个人学习、研究之用，使用程序时造成的一切后果和责任由使用者承担。**

### 使用方法

1. 安装依赖`composer install`

1. 登录[统一身份认证系统](https://byu.educationgroup.cn "统一身份认证系统")，或者登录[test/login.html](http://localhost/byuHealthReport/test/login.html "test/login.html")获取AuthToken

1. 将获取到的AuthToken放入/your/path/crond/cookie（一行一个）

1. 修改脚本内的URL`vi /your/path/crond/do.sh`

1. 赋予脚本运行权限`chmod +x /your/path/crond/do.sh`

1. 编辑计划任务`crontab -e`

1. 贴入以下代码`1 0 * * * /your/path/crond/do.sh save /your/path/crond/cookie`

### 引用代码

 - [bootstrap-4-login-page](https://github.com/nauvalazhar/bootstrap-4-login-page "bootstrap-4-login-page")

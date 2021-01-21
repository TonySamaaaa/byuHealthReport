# byuHealthReport

**程序统仅供个人学习、研究之用**

### 使用方法

1. 登录[统一身份认证系统](https://byu.educationgroup.cn "统一身份认证系统")获取Cookie

1. 将获取到的Cookie放入/your/path/cookie（一行一个）

1. 修改/your/path/do.sh内的url

1. 打开终端
`crontab -e`

1. 贴入以下代码
`1 0 * * * /your/path/do.sh /your/path/cookie save > /your/path/log`

至于Windows自己想办法

### 引用代码

 - [curl.class.php](https://github.com/lepunk/curl.class.php "curl.class.php")

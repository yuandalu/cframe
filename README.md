# cframe


├── all_check_syntax.sh           语法检查工具，请安装tree命令后使用
├── app                           App应用目录，主要的业务目录
│   ├── Conf                          业务配置目录，存放一些业务需要的配置数据，当做文件数据库使用
│   │   └── UserConf.php
│   ├── Console                       控制台应用目录
│   │   ├── Command                       工具类的命令放入此处
│   │   ├── Cron                          计划任务类的命令放入此处
│   │   └── header.php                    命令公共引入的头文件
│   ├── Controllers                   Controller，可以根据需要添加新的应用控制器
│   │   ├── Admin                         默认的后台控制器
│   │   └── Front                         默认的前台控制器
│   ├── Ext                           简单地第三方类、自定义类可以放这里，一些复杂的类库尽量使用Composer加载
│   │   ├── Browser.php
│   │   ├── Captcha.php
│   │   ├── SocketPOPClient.php
│   │   └── Timer.php
│   ├── Models                        业务模型
│   │   ├── Dao                           数据库操作层
│   │   ├── Entity                        数据对象映射层
│   │   └── Svc                           公共服务层，对所有的应用提供公用的服务，Models的对外接口由此提供
│   └── Support                       框架功能支持类，自定义类不要加入此处，尽量这里不用管
│       ├── DBCache.php
│       ├── Entity.php
│       ├── helpers.php
│       ├── Loader.php
│       ├── ......
│       └── SQLExecutor.php
├── bootstrap                         应用启动加载所需引入的公用文件
│   ├── app.php
│   └── autoload.php
├── composer.json                     Composer配置文件
├── config                            框架配置目录
│   ├── env_conf_test                     控制台相关配置
│   ├── nginx.conf_test                   服务器相关配置
│   └── server_conf.php                   框架业务相关配置
├── public                            网站单一入口
│   ├── admin                             默认后台入口
│   │   ├── index.php
│   │   └── static
│   └── front                             默认前台入口
│       └── index.php
└── resources                         资源文件
    ├── data                              业务所需的一些字体、key、ip库文件等
    │   └── verdana.ttf
    ├── database                          数据库文件、数据初始化SQL文件
    │   ├── init.sql
    │   ├── test_class.sql
    │   └── users.sql
    └── views                             iew层
        ├── admin                             默认后台View
        └── front                             默认前台View
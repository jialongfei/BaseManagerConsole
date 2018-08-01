# BaseManagerConsole

### 基本介绍

    基于 ThinkPHP3.2 和 Layui2.3 搭建的后台管理系统.

### 已有功能

    * RBAC
    * Site Config

### 使用方式

    1.获取代码
    2.创建base_manager_console数据库
    3.使用/Common/Common/database/base_manager_console.sql文件填充数据库
    4.修改/Common/Common/Conf/config.php中的数据库配置项
    5.站点(host/vhosts)配置(非必选)
    6.访问/登录/即可使用
    outer:
        登录账户: dragon
        登录密码: dragon
        新增用户和重置密码的默认密码: 12341234

### 目录结构

    ├─index.php       入口文件
    ├─README.md       README文件
    ├─App             应用目录
        ├─Common          公共目录
            ├─Common          公共目录
            ├─Conf            配置文件目录
            ├─database        数据库文件目录
            ├─View            公共模板目录
        ├─Manage          后台目录
            ├─View            公共模板目录
        └─...             其他自定义模块
    ├─Public          资源文件目录
            ├─layui          Layui框架目录
            ├─login          登录页静态资源目录
            ├─manage         后台静态资源目录
            └─...             其他自定义目录
    ├─Runtime         缓存目录
    ├─ThinkPHP        框架目录
    └─Uploads         上传文件目录
    
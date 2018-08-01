<?php
return array(
    //'配置项'=>'配置值'

    /* 开发环境设置 上线时本段设置应全部修改为相反的状态 */
    'APP_DEBUG' => true,// 开启调试模式
//    'SHOW_PAGE_TRACE' => true,// 开启调试工具
    'TMPL_CACHE_ON'     => false,// 开启模板缓存

    /* 路由设置 */
    'URL_ROUTER_ON' => true,// 开启路由

    /* 数据库设置 */
    'DB_TYPE'         => 'mysql',     // 数据库类型
    'DB_HOST'         => '127.0.0.1', // 服务器地址
    'DB_NAME'         => 'base_manager_console',    // 数据库名
    'DB_USER'         => 'root',      // 用户名
    'DB_PWD'          => 'root',      // 密码
    'DB_PORT'         => '',          // 端口
    'DB_PREFIX'       => '',         // 数据库表前缀

    'URL_ROUTE_RULES' => array(
        // login
        'login$' => 'Manage/Login/show',// 登录页面
        'dologin' => 'Manage/Login/dologin',// 登录验证
        'outlogin' => 'Manage/Login/outlogin',// 登录验证

        // 站点配置
        'basesetting$' => 'Manage/Basesetting/index',

        // 用户
        'user$' => 'Manage/User/index',// 用户列表页
        'user/create' => 'Manage/User/create',// 用户创建
        'user/edit' => 'Manage/User/edit',// 展示用户编辑页面
        'user/delete' => 'Manage/User/delete',// 用户删除
        'user/changestatus' => 'Manage/User/changestatus',// 用户状态更新
        'user/resetpassword' => 'Manage/User/resetpassword',// 用户密码重置

        // 个人资料
        'mysetting' => 'Manage/My/mysetting',// 修改个人资料
        'setmypassword' => 'Manage/My/setmypassword',// 修改自己的密码

        // 角色
        'role$' => 'Manage/Role/index',// 角色列表页
        'role/create' => 'Manage/Role/create',// 角色创建
        'role/edit' => 'Manage/Role/edit',// 展示角色编辑页面
        'role/delete' => 'Manage/Role/delete',// 角色删除
        'role/changestatus' => 'Manage/Role/changestatus',// 角色状态更新

        // 权限
        'premission$' => 'Manage/Premission/index',// 权限列表页
        'premission/create' => 'Manage/Premission/create',// 权限创建
        'premission/edit' => 'Manage/Premission/edit',// 展示权限编辑页面
        'premission/delete' => 'Manage/Premission/delete',// 权限删除

        // 前端页面所需系统接口 需要绕过权限判断的接口
        'pretree/api' => 'Manage/FrontEndApi/preTreeApi',// 权限树数据接口
        'user/search' => 'Manage/FrontEndApi/usersearch',// 用户列表数据接口
        'role/search' => 'Manage/FrontEndApi/rolesearch',// 角色列表数据接口
        'premission/search' => 'Manage/FrontEndApi/premissionsearch',// 权限列表数据接口
        'uploadone' => 'Manage/FrontEndApi/uploadone',// 普通图片上传接口
        'role/bind/pre' => 'Manage/FrontEndApi/updatePreApi',// 角色关联权限接口

    ),
);
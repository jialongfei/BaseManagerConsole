<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/11
 * Time: 13:59
 */

namespace Manage\Controller;

use Manage\Model\PremissionModel;
use Manage\Model\UserModel;
use Think\Controller;

class LoginController extends Controller
{
    /**
     * 显示登录页面
     */
    public function show()
    {
        $this->display();
    }

    /**
     * 用户登录操作
     */
    public function dologin()
    {
        if (!IS_POST || !IS_AJAX) $this->ajaxReturn(['status'=>false,'msg'=>'非法请求']);

        $login_data = I('post.');// 数据接收

        if (!$login_data['name']) $this->ajaxReturn(['status'=>false,'msg'=>'用户名不能为空']);

        if (!$login_data['password']) $this->ajaxReturn(['status'=>false,'msg'=>'密码不能为空']);

        $login_status = $this->checkPassword($login_data);

        if ($login_status != 'success') $this->ajaxReturn(['status'=>false,'msg'=>$login_status]);

        $this->ajaxReturn(['status'=>true,'msg'=>'登录成功']);

    }

    /**
     * 登录验证
     */
    public function checkPassword($data)
    {
        $password = md5(md5('dragon-'.$data['password']));// 密码处理

        $need_field = 'id,name,true_name,phone,email,avatar,status';// 要查询的字段

        $user = (new UserModel())->field($need_field)->where(['name'=>['eq',$data['name']],'password'=>['eq',$password]])->find();// 密码匹配

        if (!$user) return '用户名或密码不正确';// 没有匹配的用户

        if (!$user['status']) return '账户已被禁用';// 用户被禁用

        // 获取当前用户角色
        $current_role = M('UserRole')->where(['user_id'=>['eq',$user['id']]])->find()['role_id'];

        $user['role_id'] = $current_role;// 当前用户角色ID

        // 如果不是超级用户
        if ($user['status'] != 9)
        {
            // 获取当前用户权限IDS
            $_current_pre_ids = M('RolePre')->field('pre_id')->where(['role_id'=>['eq',$current_role]])->select();

            // 二维转一维
            $current_pre_ids = array_map(function ($v){
                return $v['pre_id'];
            },$_current_pre_ids);

            // 如果当前用户没有任何权限
            if (!$_current_pre_ids)
            {
                return '您的账号暂未获得任何访问权限,请联系管理员授权';// 没有任何操作权限
            }

            // 获取当前用户可操作uri
            $_current_pre = (new PremissionModel())->field('uri')->where(['id'=>['in',$current_pre_ids]])->select();

            // 二维转一维
            $current_pre = array_map(function ($v){
                return $v['uri'];
            },$_current_pre);

            $user['has_pre'] = $current_pre;// 当前用户可操作uri
        }


        // 登录成功
        session('_manager',$user);// 存入session

        // 记录最后登录时间和IP
        $this->recordLastLogin($user);

        return 'success';

    }

    /**
     * 退出登录
     */
    public function outlogin()
    {
        session('_manager',null);// 清空登录状态

        if (!session('_manager')) $this->success('已退出当前账号','/login',1);
    }

    /**
     * 更新最后登录时间和IP
     * 无返回值
     */
    public function recordLastLogin($data)
    {
        $data['login_time'] = time();
        $data['login_ip'] = $this->getClientIP();
        // 更新最后登录时间和登录IP
        (new UserModel())->save($data);
    }

    /**
     * 获取客户端IP
     */
    public function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/17
 * Time: 9:00
 */

namespace Manage\Controller;

use Manage\Model\PremissionModel;
use Think\Controller;

class BaseController extends Controller
{
    /**
     * 初始化
     */
    public function _initialize()
    {
        $this->isLogin();// 判断用户是否登录

        if (session('_manager')['status'] != '9') // 特殊用户跳过判断
        {
            $this->hasCurrentRequestPre();// 判断用户是否有当前请求的操作权限

            $this->getLeftNav();// 初始化左侧导航栏
        }else{
            $this->getAllLeftNav();// 跳过权限,获取所有左侧导航
        }
        
        $this->site_init();// 获取基本配置信息
    }

    /**
     * 判断用户是否登录
     */
    public function isLogin()
    {
        if (!session('_manager')) $this->error('请先登录!','/login',2);
    }

    /**
     * 判断用户是否有当前请求的操作权限
     */
    public function hasCurrentRequestPre()
    {
        $current_request_uri = $_SERVER['REQUEST_URI'];// 当前请求URI

        $current_user_has_pre = session('_manager')['has_pre'];// 当前用户已有权限
        
        // 如果当前用户没有想要操作的uri的权限 由于部分链接会带有get参数,所以增加条件截取 ? 之前的部分
        if (!in_array($current_request_uri,$current_user_has_pre) && !in_array(substr($current_request_uri,0,strrpos($current_request_uri,'?')),$current_user_has_pre))
        {
            if (IS_AJAX) $this->ajaxReturn(['status'=>false,'msg'=>'无操作权限!']);// AJAX请求

            return $this->error('无操作权限');// 普通请求
        }

    }

    /**
     * 初始化左侧导航栏
     */
    public function getLeftNav()
    {
        // 实例化权限模型
        $pre_model = new PremissionModel();

        // 获取当前用户角色
        $current_role = M('UserRole')->where(['user_id'=>['eq',session('_manager')['id']]])->find()['role_id'];
        
        // 获取当前用户权限IDS
        $_current_pre_ids = M('RolePre')->field('pre_id')->where(['role_id'=>['eq',$current_role]])->select();
        
        // 如果当前用户没有任何权限
        if (!$_current_pre_ids)
        {
            $this->display('/Public/notpremission');
            exit();// 终止程序
        }
        
        // 二维转一维
        $current_pre_ids = array_map(function ($v){
            return $v['pre_id'];
        },$_current_pre_ids);

        // 获取当前左侧导航目录树
        $left_nav = $pre_model->where(['id'=>['in',$current_pre_ids],'is_nav'=>['eq',1],'pid'=>['eq',0]])->select();

        if ($left_nav) // 如果不为空
        {
            // 准备获取子导航
            foreach ($left_nav as $k => $v)
            {
                if ($pre_model->where(['uri'=>['eq',$_SERVER['REQUEST_URI']], 'pid'=>['eq',$v['id']]])->select())
                {
                    $left_nav[$k]['selected'] = 1;// 父导航选中状态
                }
                // 获取子导航
                $left_nav[$k]['child'] = $pre_model->where(['id'=>['in',$current_pre_ids],'is_nav'=>['eq',1], 'pid'=>['eq',$v['id']]])->select();
                foreach ($left_nav[$k]['child'] as $key => $val)
                {
                    if ($val['uri'] == $_SERVER['REQUEST_URI'])
                    {
                        $left_nav[$k]['child'][$key]['selected'] = 1;// 子导航选中状态
                    }
                }
            }
        }

        // 分配导航到模板中
        $this->left_nav = $left_nav;
    }

    /**
     * 初始化所有左侧导航栏
     */
    public function getAllLeftNav()
    {
        // 实例化权限模型
        $pre_model = new PremissionModel();

        // 获取当前左侧导航目录树
        $left_nav = $pre_model->where(['is_nav'=>['eq',1],'pid'=>['eq',0]])->select();

        if ($left_nav) // 如果不为空
        {
            // 准备获取子导航
            foreach ($left_nav as $k => $v)
            {
                if ($pre_model->where(['uri'=>['eq',$_SERVER['REQUEST_URI']], 'pid'=>['eq',$v['id']]])->select())
                {
                    $left_nav[$k]['selected'] = 1;// 父导航选中状态
                }
                // 获取子导航
                $left_nav[$k]['child'] = $pre_model->where(['is_nav'=>['eq',1], 'pid'=>['eq',$v['id']]])->select();
                foreach ($left_nav[$k]['child'] as $key => $val)
                {
                    if ($val['uri'] == $_SERVER['REQUEST_URI'])
                    {
                        $left_nav[$k]['child'][$key]['selected'] = 1;// 子导航选中状态
                    }
                }
            }
        }

        // 分配导航到模板中
        $this->left_nav = $left_nav;
    }

    /**
     * 获取网站基本信息
     */
    public function site_init()
    {
        $site_info = M('base_setting')->find(1);
        $this->current_user = session('_manager');
        $this->site_info = $site_info;
    }

}
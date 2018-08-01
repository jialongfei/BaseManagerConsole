<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/20
 * Time: 9:08
 */

namespace Manage\Controller;


use Manage\Model\UserModel;
use Think\Controller;

class MyController extends Controller
{
    public function mysetting()
    {
        if (IS_POST && IS_AJAX)// 如果修改
        {
            $par = I('post.');// 参数接收

            if (!$par['id']) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'用户名不能为空']);// 参数过滤

            // 获取原始数据
            $before_data = (new UserModel())->find($par['id']);

            if (!$before_data)  $this->ajaxReturn(['status'=>false,'msg'=>'数据不存在,账号可能已被删除!']);// 数据不存在

            // 如果要修改用户名称,则需要判断新的用户名称是否已经存在
            if ($before_data['name'] != $par['name'])
            {
                if ((new UserModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'用户名已存在!']);
            }

            // TODO validate...

            // 执行更新操作
            try{
                $res = (new UserModel())->save($par);// 更新
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

            $this->ajaxReturn(['status'=>true,'msg'=>'修改成功!']);// 更新成功
        }

        // 展示修改页面
        $id = ltrim(I('get.id'),',');// 参数接收

        if (!$id) exit('缺少关键参数');// 缺少关键参数

        $info = (new UserModel())->find($id);// 获取要修改的数据

        if (!$info)  exit('数据不存在,当前数据可能已被删除');// 数据不存在

        $this->info = $info;// 分配数据到模板

        $this->display();// 展示模板
    }

    public function setmypassword()
    {
        if (IS_AJAX && IS_POST)
        {
            $id = session('_manager')['id'];// 当前用户ID

            $password = I('post.password');// 参数接收

            if (!$id) $this->ajaxReturn(['status'=>false,'msg'=>'请重新登录']);// 获取不到当前登录用户的信息

            if (!$password) $this->ajaxReturn(['status'=>false,'msg'=>'密码不能为空']);// 密码不能为空

            $change['password'] = md5(md5('dragon-'.$password));// 生成指定密码

            try{
                $res = (new UserModel())->where(['id'=>['eq',$id]])->save($change);// 更新
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

            $this->ajaxReturn(['status'=>true,'msg'=>'更新成功!']);//更新成功
        }

        $this->display();
    }
}
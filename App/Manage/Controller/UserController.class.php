<?php
namespace Manage\Controller;

use Manage\Model\RoleModel;
use Manage\Model\UserModel;

class UserController extends BaseController
{

    /**
     * 列表页显示
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 新增用户
     */
    public function create()
    {

        // 如果新增
        if (IS_AJAX && IS_POST)
        {
            if (!$par = I('post.')) $this->ajaxReturn(['status'=>false,'msg'=>'缺少参数']);// 参数接受

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'用户名不能为空']);// 参数过滤

            if ((new UserModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'用户名已存在!']);

//            if ((new UserModel())->where(['phone'=>['eq',$par['phone']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'手机号已存在!']);

//            if ((new UserModel())->where(['email'=>['eq',$par['email']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'邮箱已存在!']);

            $data = $this->validatePar($par);// 数据验证

            try{
                $res = (new UserModel())->add($data);// 插入
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

            if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请联系管理员']);// 如果添加失败

            if ($data['role'])// 如果关联角色
            {
                M('UserRole')->add(['user_id'=>$res,'role_id'=>$data['role']]);
            }

            $this->ajaxReturn(['status'=>true,'msg'=>'添加成功']);// 添加成功
        }

        $role = (new RoleModel())->select();

        $this->roles = $role;

        $this->display();// 展示模板
    }

    /**
     * 用户修改
     */
    public function edit()
    {

        if (IS_POST && IS_AJAX)// 如果修改
        {
            $par = I('post.');// 参数接收

            if (!$par['id']) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'用户名不能为空']);// 参数过滤

            // 获取原始数据
            $before_data = (new UserModel())->find($par['id']);

            if (!$before_data)  $this->ajaxReturn(['status'=>false,'msg'=>'数据不存在,当前数据可能已被删除!']);// 数据不存在

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

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

            if ($par['role'])// 如果关联角色
            {
                M('UserRole')->where(['user_id'=>['eq',$par['id']]])->delete();// 删除原有的
                M('UserRole')->add(['user_id'=>$par['id'],'role_id'=>$par['role']]);// 新增角色关联
            }

            $this->ajaxReturn(['status'=>true,'msg'=>'修改成功!']);// 更新成功
        }

        // 展示修改页面
        $id = ltrim(I('get.id'),',');// 参数接收

        if (!$id) exit('缺少关键参数');// 缺少关键参数

        $info = (new UserModel())->find($id);// 获取要修改的数据

        if (!$info)  exit('数据不存在,当前数据可能已被删除');// 数据不存在

        $info['has_role'] = M('UserRole')->where(['user_id'=>['eq',$info['id']]])->find()['role_id'];// 已有角色

        $role = (new RoleModel())->select();

        $this->info = $info;// 分配数据到模板
        $this->roles = $role;

        $this->display();// 展示模板
    }

    /**
     * 删除用户
     */
    public function delete()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        if (in_array(session('_manager')['id'],$ids)) $this->ajaxReturn(['status'=>false,'msg'=>'非法操作,不能删除当前登录账号']);// 缺少关键参数

        try{
            $res = (new UserModel())->where(['id'=>['IN',$ids]])->delete();// 删除
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 删除失败

        M('UserRole')->where(['user_id'=>['IN',$ids]])->delete();// 删除关联数据

        $this->ajaxReturn(['status'=>true,'msg'=>'删除成功!']);// 删除成功
    }

    /**
     * 更新用户状态
     */
    public function changestatus()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        $_befor_check = (new UserModel())->where(['id'=>['IN',$ids],'status'=>['eq',9]])->select();// 查询要操作的用户中是否有超级用户

        if ($_befor_check) $this->ajaxReturn(['status'=>false,'msg'=>'非法操作,不可操作超级用户']);// 不可操作超级用户

        if (I('post.target_status'))
        {
            $change = ['status'=>1];
        }else{
            $change = ['status'=>0];
        }

        try{
            $res = (new UserModel())->where(['id'=>['IN',$ids]])->save($change);// 更新
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

        $this->ajaxReturn(['status'=>true,'msg'=>'更新成功!']);//更新成功
    }

    /**
     * 重置密码
     */
    public function resetpassword()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        $change['password'] = $this->get_password();// 生成默认密码

        try{
            $res = (new UserModel())->where(['id'=>['IN',$ids]])->save($change);// 更新
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

        $this->ajaxReturn(['status'=>true,'msg'=>'更新成功!']);//更新成功
    }

    /**
     * 数据验证
     */
    private function validatePar($par=[])
    {
        $data = $par;

        // TODO validate...
        if (!$data['password']){
            $data['password'] = $this->get_password();// 生成默认密码
        }else{
            $data['password'] = $this->get_password($data['password']);// 生成指定密码
        }

        // 拼接数据
        $data['create_user'] = session('_manager')['id'];// 创建者
        $data['create_time'] = time();// 创建时间

        return $data;
    }

    /**
     * 生成密码
     */
    private function get_password($password = '')
    {
        if (!$password) return md5(md5('dragon-'.'12341234'));// 生成默认密码

        return md5(md5('dragon-'.$password));// 生成指定密码
    }

}
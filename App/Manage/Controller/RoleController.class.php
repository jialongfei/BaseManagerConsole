<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/16
 * Time: 14:36
 */

namespace Manage\Controller;

use Manage\Model\PremissionModel;
use Manage\Model\RoleModel;
use Manage\Model\UserModel;

class RoleController extends BaseController
{
    /**
     * 列表页显示
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 新增角色
     */
    public function create()
    {
        // 如果新增
        if (IS_AJAX && IS_POST)
        {
            if (!$par = I('post.')) $this->ajaxReturn(['status'=>false,'msg'=>'缺少参数']);// 参数接受

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'角色名不能为空']);// 参数过滤

            if ((new RoleModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'角色名已存在!']);

            // TODO validate...
            $data = $this->validatePar($par);

            try{
                $res = (new RoleModel())->add($data);// 插入
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

            if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请联系管理员']);// 如果添加失败

            // 7-19 需求调整,关联权限调整为异步请求,且新增时无需关联角色.
//            if ($par['premission'])// 如果关联权限
//            {
//                for ( $i=0; $i<count($par['premission']); $i++ )
//                {
//                    M('RolePre')->add(['role_id'=>$res,'pre_id'=>$par['premission'][$i]]);// 新增权限关联
//                }
//            }

            $this->ajaxReturn(['status'=>true,'msg'=>'添加成功']);// 添加成功
        }

        $this->display();// 展示模板
    }

    /**
     * 展示编辑页面
     */
    public function edit()
    {

        if (IS_POST && IS_AJAX) // 如果修改
        {
            $par = I('post.');// 参数接收

            if (!$par['id']) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'角色名不能为空']);// 参数过滤

            // 获取原始数据
            $before_data = (new RoleModel())->find($par['id']);

            if (!$before_data)  $this->ajaxReturn(['status'=>false,'msg'=>'数据不存在,当前数据可能已被删除!']);// 数据不存在

            // 如果要修改角色名称,则需要判断新的角色名称是否已经存在
            if ($before_data['name'] != $par['name'])
            {
                if ((new RoleModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'角色名已存在!']);
            }

            // TODO validate...

            // 执行更新操作
            try{
                $res = (new RoleModel())->save($par);// 更新
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

            // 7-19 需求调整,如果没有任何修改,提示修改成功
//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

            // 7-19 需求调整,关联权限调整为异步请求
//            if ($par['premission'])// 如果关联权限
//            {
//                M('RolePre')->where(['role_id' => ['eq', $par['id']]])->delete();// 删除原有的
//                foreach ($par['premission'] as $k => $v)
//                {
//                    M('RolePre')->add(['role_id'=>$par['id'],'pre_id'=>$par['premission'][$k]]);// 新增权限关联
//                }
//            }else{ // 如果清空权限
//                M('RolePre')->where(['role_id' => ['eq', $par['id']]])->delete();// 删除原有的
//            }

            $this->ajaxReturn(['status'=>true,'msg'=>'修改成功!']);// 更新成功
        }

        $id = ltrim(I('get.id'),',');// 参数接收

        if (!$id) exit('缺少关键参数');// 缺少关键参数

        $info = (new RoleModel())->find($id);// 获取要修改的数据

        if (!$info)  exit('数据不存在,当前数据可能已被删除');// 数据不存在

        $this->info = $info;

        $this->display();// 展示模板
    }

    /**
     * 更新角色状态
     */
    public function changestatus()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        if (I('post.target_status'))
        {
            $change = ['status'=>1];
        }else{
            $change = ['status'=>0];
        }

        try{
            $res = (new RoleModel())->where(['id'=>['IN',$ids]])->save($change);// 更新
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

        $this->ajaxReturn(['status'=>true,'msg'=>'更新成功!']);//更新成功
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        try{
            $res = (new RoleModel())->where(['id'=>['IN',$ids]])->delete();// 更新
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 删除失败

        $this->ajaxReturn(['status'=>true,'msg'=>'删除成功!']);// 删除成功
    }

    /**
     * 数据验证
     */
    private function validatePar($par=[])
    {
        $data = $par;

        // 拼接数据
        $data['create_user'] = session('_manager')['id'];// 创建者
        $data['create_time'] = time();// 创建时间

        return $data;
    }

}
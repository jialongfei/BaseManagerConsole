<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/16
 * Time: 15:28
 */

namespace Manage\Controller;

use Manage\Model\PremissionModel;

class PremissionController extends BaseController
{
    /**
     * 列表页显示
     */
    public function index()
    {
        // header('Content-Type: text/html; charset=utf-8'); // 网页编码
        $this->display();
    }

    /**
     * 新增权限
     */
    public function create()
    {
        // 如果新增
        if (IS_AJAX && IS_POST)
        {
            if (!$par = I('post.')) $this->ajaxReturn(['status'=>false,'msg'=>'缺少参数']);// 参数接受

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'权限名不能为空']);// 参数过滤

            if ((new PremissionModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'权限名已存在!']);

            // TODO validate...
            $data = $this->validatePar($par);

            try{
                $res = (new PremissionModel())->add($data);// 插入
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

            if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请联系管理员']);// 如果添加失败

            $this->ajaxReturn(['status'=>true,'msg'=>'添加成功']);// 添加成功
        }
        
        $this->display();// 展示模板
    }

    /**
     * 编辑
     */
    public function edit()
    {

        if (IS_POST && IS_AJAX) // 如果更新
        {
            $par = I('post.');// 参数接收

            if (!$par['id']) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

            if (!$par['name']) $this->ajaxReturn(['status'=>false,'msg'=>'权限名不能为空']);// 参数过滤

            // 获取原始数据
            $before_data = (new PremissionModel())->find($par['id']);

            if (!$before_data)  $this->ajaxReturn(['status'=>false,'msg'=>'数据不存在,当前数据可能已被删除!']);// 数据不存在

            // 如果要修改权限名称,则需要判断新的权限名称是否已经存在
            if ($before_data['name'] != $par['name'])
            {
                if ((new PremissionModel())->where(['name'=>['eq',$par['name']]])->select()) $this->ajaxReturn(['status'=>false,'msg'=>'权限名已存在!']);
            }

            // TODO validate...
            if (!$par['uri']) $par['uri'] = 'javascript:;';// 默认无链接

            // 执行更新操作
            try{
                $res = (new PremissionModel())->save($par);// 更新
            }catch (\Exception $exception){
                $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
            }

//        if (!$res) $this->ajaxReturn(['status'=>false,'msg'=>'系统维护,请稍后再试!']);// 更新失败

            $this->ajaxReturn(['status'=>true,'msg'=>'修改成功!']);// 更新成功
        }

        $id = ltrim(I('get.id'),',');// 参数接收

        if (!$id) exit('缺少关键参数');// 缺少关键参数

        $info = (new PremissionModel())->find($id);// 获取要修改的数据

        if (!$info)  exit('数据不存在,当前数据可能已被删除');// 数据不存在

        $this->info = $info;// 分配数据到模板

        $this->display();// 展示模板
    }

    /**
     * 删除权限
     */
    public function delete()
    {
        $ids = explode(',',ltrim(I('post.ids'),','));// 参数接收

        if (!$ids) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);// 缺少关键参数

        try{
//            $res = (new PremissionModel())->where(['id'=>['IN',$ids]])->delete();// 删除
            // 为配合model层的后置动作,删除动作修改为循环删除 而不是 IN 删除.
            foreach ($ids as $k => $v)
            {
                $res = (new PremissionModel())->delete($v);// 删除
            }
        }catch (\Exception $exception){
            $this->ajaxReturn(['status'=>false,'msg'=>$exception->getMessage()]);// 捕获异常
        }

        $this->ajaxReturn(['status'=>true,'msg'=>'删除成功!']);// 删除成功
    }

    /**
     * 数据验证
     */
    private function validatePar($par=[])
    {
        $data = $par;

        if (!$data['uri']) $data['uri'] = 'javascript:;';// 默认无链接

        // 拼接数据
        $data['create_user'] = session('_manager')['id'];// 创建者
        $data['create_time'] = time();// 创建时间

        return $data;
    }

}
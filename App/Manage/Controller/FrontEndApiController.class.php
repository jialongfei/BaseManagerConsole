<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/18
 * Time: 14:01
 */

namespace Manage\Controller;

use Manage\Model\CallLogModel;
use Manage\Model\CallStudentModel;
use Manage\Model\CallTeacherModel;
use Manage\Model\CasesCateModel;
use Manage\Model\CasesModel;
use Manage\Model\KoubeiModel;
use Manage\Model\PremissionModel;
use Manage\Model\RoleModel;
use Manage\Model\SuccessCaseModel;
use Manage\Model\UserModel;
use Think\Controller;

class FrontEndApiController extends Controller
{
    /**
     * 有层次的权限列表
     */
    public function preTreeApi()
    {

        // 获取所有权限
        $premissions = (new PremissionModel())->field('id,name,pid')->select();
        
        if (I('get.pre_id'))// 如果来自权限修改
        {
            $pre_pid = (new PremissionModel())->find(I('get.pre_id'))['pid'];// 获取要修改的权限父ID
            foreach ($premissions as $k => $v)
            {
                if ($v['id'] == $pre_pid)
                {
//                  $premissions[$k]['is_checked'] = 1;
                    $checked_parent = $v;
                }
            }
        }

        if (I('get.role_id'))// 如果来自角色修改
        {
            // 获取当前角色已有权限
            $_has_premission = M('RolePre')->where(['role_id'=>['eq',I('get.role_id')]])->field('pre_id')->select();
            // 二维转一维
            $has_premission = array_map(function ($v){
                return $v['pre_id'];
            },$_has_premission);
            // 添加选中状态
            foreach ($premissions as $k => $pre)
            {
                foreach ($has_premission as $key => $has)
                {
                    if ($has == $pre['id'])
                    {
                        $premissions[$k]['is_checked'] = 1;
                    }
                }
            }
        }

        // 格式化权限树
        $tree = getPreTree($premissions);
        
        $this->ajaxReturn(['status'=>true,'data'=>$tree,'is_checked'=>$checked_parent]);
    }

    /**
     * 有层次的案例数据分类列表
     */
    public function casesCateTreeApi()
    {

        // 获取所有分类
        $casescate = (new CasesCateModel())->field('id,name,pid')->select();

        if (I('get.self_id'))// 如果来自权限修改
        {
            $self_pid = (new CasesCateModel())->find(I('get.self_id'))['pid'];// 获取要修改的权限父ID
            foreach ($casescate as $k => $v)
            {
                if ($v['id'] == $self_pid)
                {
//                  $premissions[$k]['is_checked'] = 1;
                    $checked_parent = $v;
                }
            }
        }

        // 格式化分类树
        $tree = getPreTree($casescate);

        $this->ajaxReturn(['status'=>true,'data'=>$tree,'is_checked'=>$checked_parent]);
    }

    /**
     * 用户数据
     */
    public function usersearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $user_model = new UserModel();// 实例化用户模型

        $users = $user_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($users as $k => $v) {
            $users[$k]['create_user_name'] = $user_model->field('name')->find($v['create_user'])['name'];
            $role_id = M('UserRole')->field('role_id')->where(['user_id'=>['eq',$v['id']]])->find()['role_id'];
            $users[$k]['role_name'] = ' - ';
            if ($role_id) $users[$k]['role_name'] = (new RoleModel())->field('name')->find($role_id)['name'];
        }

        $count = $user_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$users// 数据列表
        ]);
    }

    /**
     * 角色数据
     */
    public function rolesearch()
    {

        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $role_model = new RoleModel();// 实例化角色模型

        $users = $role_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($users as $k => $v) {
            $users[$k]['create_user_name'] = (new UserModel())->field('name')->find($v['create_user'])['name'];
        }

        $count = $role_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$users// 数据列表
        ]);
    }

    /**
     * 权限数据
     */
    public function premissionsearch()
    {

        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $premission_model = new PremissionModel();// 实例化权限模型

        $premissions = $premission_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($premissions as $k => $v) {
            $premissions[$k]['create_user_name'] = (new UserModel())->field('name')->find($v['create_user'])['name'];
            if ($v['pid'])
            {
                $premissions[$k]['pid_name'] = (new PremissionModel())->field('name')->find($v['pid'])['name'];
            }else{
                $premissions[$k]['pid_name'] = '顶级权限';
            }
        }

        $count = $premission_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$premissions// 数据列表
        ]);
    }

    /**
     * 口碑数据
     */
    public function koubeisearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new KoubeiModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 创建者
            $kbs[$k]['create_user'] = $kbs[$k]['create_user']?(new UserModel())->field('name')->find($v['create_user'])['name']:' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 案例数据
     */
    public function casessearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字
        $tag = $par['tag'];

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new CasesModel();// 实例化模型

        // 如果按分类搜索
        if ($tag && is_array($tag))
        {
            $_where_str = '';
            foreach ($tag as $k => $v)
            {
                $_where_str .= "`cate_ids` like '%".$v."%' and";// 如果按标签查询
            }
            $where_str = rtrim($_where_str,'and');

            if ($where_str) $condition['_string'] = $where_str;
        }

        $kbs = $kb_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 创建者
            $kbs[$k]['create_user'] = $kbs[$k]['create_user']?(new UserModel())->field('name')->find($v['create_user'])['name']:' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 案例数据分类
     */
    public function casescatesearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new CasesCateModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($kbs as $k => $v) {

            if ($v['pid'])
            {
                $kbs[$k]['pid_name'] = $kb_model->field('name')->find($v['pid'])['name'];
            }else{
                $kbs[$k]['pid_name'] = '顶级分类';
            }
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 创建者
            $kbs[$k]['create_user'] = $kbs[$k]['create_user']?(new UserModel())->field('name')->find($v['create_user'])['name']:' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 成功案例数据
     */
    public function successcasesearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new SuccessCaseModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->select();// 获取数据列表

        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 创建者
            $kbs[$k]['create_user'] = $kbs[$k]['create_user']?(new UserModel())->field('name')->find($v['create_user'])['name']:' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 名师数据
     */
    public function callteachersearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new CallTeacherModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->order('sort')->select();// 获取数据列表

        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 创建者
            $kbs[$k]['create_user'] = $kbs[$k]['create_user']?(new UserModel())->field('name')->find($v['create_user'])['name']:' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 学员数据
     */
    public function callstudentsearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        if ($search_key) $condition['name'] = ['LIKE','%'.$search_key.'%'];// 如果按关键字查询

        $kb_model = new CallStudentModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->order('create_time desc')->select();// 获取数据列表
        
        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            // 更新时间
            $kbs[$k]['update_time'] = $kbs[$k]['update_time']?date('Y-m-d H:i:s',$kbs[$k]['update_time']):' - ';
            // 更新者
            $kbs[$k]['update_user'] = $kbs[$k]['update_user']?(new UserModel())->field('name')->find($v['update_user'])['name']:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 打call日志数据
     */
    public function calllogsearch()
    {
        $par = I();// 获取参数

        $page = $par['page']?:1;// 当前页
        $limit = $par['limit']?:15;// 每页显示条数
        $start = $limit * ($page-1);// 当前查询起始条数
        $search_key = $par['search_key']?:'';// 查询关键字

        $condition = [];// 准备查询条件

        // 如果按学员姓名关键字查询
        if ($search_key)
        {
            $users = (new CallStudentModel())->field('id,name')->where(['name'=>['like','%'.$search_key.'%']])->select();

            if ($users)
            {
                $ids = array_map(function ($v){
                    return $v['id'];
                },$users);

                $condition['stu_id'] = ['in',$ids];
            }else{
                // 数据为空
                $this->ajaxReturn([
                    'code'=>0,// 0为成功
                    'msg'=>'',// 错误提示
                    'count'=>0,// 总条数
                    'data'=>[]// 数据列表
                ]);
            }
        }

        $kb_model = new CallLogModel();// 实例化模型

        $kbs = $kb_model->where($condition)->limit($start,$limit)->order('create_time desc')->select();// 获取数据列表

        foreach ($kbs as $k => $v) {
            // 创建时间
            $kbs[$k]['create_time'] = $kbs[$k]['create_time']?date('Y-m-d H:i:s',$kbs[$k]['create_time']):' - ';
            $kbs[$k]['stu_name'] = (new CallStudentModel())->find($v['stu_id'])['name']?:' - ';
            $kbs[$k]['tea_name'] = (new CallTeacherModel())->find($v['tea_id'])['name']?:' - ';
        }

        $count = $kb_model->where($condition)->count();// 统计数据总数

        $this->ajaxReturn([
            'code'=>0,// 0为成功
            'msg'=>'',// 错误提示
            'count'=>$count,// 总条数
            'data'=>$kbs// 数据列表
        ]);
    }

    /**
     * 角色关联权限接口
     */
    public function updatePreApi()
    {
        if (IS_POST)
        {
            $type = I('post.type');// 更新类型
            $role_id = I('post.role_id');// 角色ID
            $pre_id = I('post.pre_id');// 权限ID

            if ( !$type || !$role_id || !$pre_id ) $this->ajaxReturn(['status'=>false,'msg'=>'缺少关键参数']);

            if (!is_array($pre_id)) $this->ajaxReturn(['status'=>false,'msg'=>'参数类型错误']);

            if ($type == 'del')// 取消关联 删除操作
            {
                foreach ($pre_id as $k => $v)
                {
                    M('RolePre')->where(['role_id' => ['eq', $role_id], 'pre_id'=>['eq',$v]])->delete();// 删除原有的
                }
//                $res = M('RolePre')->where(['role_id' => ['eq', $role_id], 'pre_id'=>['eq',$pre_id]])->delete();// 删除原有的
                $this->ajaxReturn(['status'=>true,'msg'=>'更新成功']);
            }

            if ($type == 'add')// 添加关联 新增操作
            {
                foreach ($pre_id as $k => $v)
                {
                    $res = M('RolePre')->add(['role_id'=>$role_id,'pre_id'=>$v]);// 新增权限关联
                }
                $this->ajaxReturn(['status'=>true,'msg'=>'更新成功']);
            }

            $this->ajaxReturn(['status'=>false,'msg'=>'未知的请求']);

        }

        $this->ajaxReturn(['status'=>false,'msg'=>'非法请求']);
    }

    /**
     * 图片上传接口
     */
    public function uploadone()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =      'img/'; // 设置附件上传子目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->ajaxReturn(['status'=>false,'msg'=>$upload->getError()]);
        }else{// 上传成功
            // 拼接文件路径
            $save_file = $upload->rootPath.$info['file']['savepath'].$info['file']['savename'];
            $this->ajaxReturn(['status'=>true,'data'=>$save_file]);// 返回文件保存路径
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/17
 * Time: 10:13
 */

namespace Manage\Controller;


class BasesettingController extends BaseController
{
    /**
     * 基本配置
     * GET = 展示
     * POST = 更新
     */
    public function index()
    {
        if (IS_POST)
        {
            $data = I('post.');
            
            M('BaseSetting')->save($data);

            redirect('/basesetting');

            // 如果需要操作提示
            // redirect('/basesetting',1,'Success !');
        }

        $info = M('BaseSetting')->find(1);
        $this->info = $info;
        $this->display();
    }
}
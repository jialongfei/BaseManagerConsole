<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/16
 * Time: 15:32
 */

namespace Manage\Model;

use Think\Model;

class PremissionModel extends Model
{

    /**
     * middle 层
     * 获取子权限的媒介
     */
    public function childid($priid)
    {
        $data = $this->select();
        return $this->getchildid($data, $priid);
    }

    /**
     * 获取子权限
     */
    public function getchildid($data, $parentid)
    {
        static $ret = array();
        foreach ($data as $k => $v) {
            if ($v['pid'] == $parentid) {
                $ret[] = $v['id'];
                $this->getchildid($data, $v['id']);
            }
        }
        return $ret;
    }

    /**
     * 权限删除的后置动作 删除子权限
     */
    public function _before_delete($options)
    {
        if (is_array($options['where']['id'])) {    //if where下面的id是数组的话那么执行的是批量删除，否则是单个删除
            $arr = explode(',', $options['where']['id'][1]);      //把字符串变成数组
            $soncates = array();
            foreach ($arr as $k => $v) {
                $soncates2 = $this->childid($v);
                $soncates = array_merge($soncates, $soncates2);            //合并数组
            }
            $soncates = array_unique($soncates);        //移除数组中相同的部分
            $childrenids = implode(',', $soncates);
        } else {
            $childrenids = $this->childid($options['where']['id']);
            $childrenids = implode(',', $childrenids);
        }
        if ($childrenids) {
            $this->execute("delete from `premission` where id in($childrenids)");
        }
    }
}
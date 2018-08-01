<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/7/17
 * Time: 18:37
 */

/**
 * 格式化权限树
 * @param $data
 * @return array
 */
function getPreTree($data)
{
    $refer = array();
    $tree = array();
    foreach($data as $k => $v){
        $refer[$v['id']] = & $data[$k]; //创建主键的数组引用
    }
    foreach($data as $k => $v){
        $pid = $v['pid'];  //获取当前分类的父级id
        if($pid == 0){
            $tree[] = & $data[$k];  //顶级栏目
        }else{
            if(isset($refer[$pid])){
                $refer[$pid]['children'][] = & $data[$k]; //如果存在父级栏目，则添加进父级栏目的子栏目数组中
            }
        }
    }
    return $tree;
}

/**
 * 判断当前登录用户是否有操作某个uri的权限
 * @param $uri
 * @return bool
 */
function hasPre($uri)
{
//    session_start();// 开启session

    // 当前用户已有权限
    $current_user_has_pre = session('_manager')['has_pre'];

    // 如果当前用户拥有想要操作的uri的权限
    if (in_array($uri,$current_user_has_pre))
        return true;

    return false;// 否则无权限
}

/**
 * 判断手机号是否合法
 * @param $phone
 * @return bool
 */
function is_phone($phone)
{
    return strlen(trim($phone)) == 11 && preg_match("/^1[3|4|5|6|7|8|9][0-9]{9}$/i", trim($phone));
}

/**
 * 按指定字段排序数组
 * @param $array
 * @param $field
 * @param bool $desc
 */
function sortArrByField(&$array, $field, $desc = false){
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc == false ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $sort, $array);
}
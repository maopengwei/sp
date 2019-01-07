<?php
namespace app\admin\model;

use think\Model;

/**
 *
 */
class Rule extends Model
{
    // 权限列表
    public static function ruleList()
    {
        $list = self::where('type', 2)->order('id asc')->select();
        foreach ($list as $key => $value) {
            # code...
            $keyword = explode('/', $value['name']);
            $map['type'] = 1;
            $map['name'] = ['like', 'Admin/' . $keyword[1] . '%'];
            $list[$key]['child'] = self::where($map)->select();
        }

        return $list;
    }

}

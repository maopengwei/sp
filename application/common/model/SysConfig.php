<?php
namespace app\common\model;

use Cache;
use think\Model;

/**
 *
 */
class SysConfig extends Model
{
    public function getInfo()
    {
        $list = $this->field('key,value')->select();
        foreach ($list as $k => $v) {
            $listinfo[$v['key']] = $v['value'];
        }
        return $listinfo;
    }

    public function xiugai($data)
    {
        foreach ($data as $k => $v) {
            self::where('key', $k)->update(['value' => $v]);
        }
        Cache::clear();
        return true;
    }
}

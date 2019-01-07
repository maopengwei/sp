<?php
namespace app\common\model;

use think\Model;
use think\Db;
/**
 *
 */
class InRec extends Model {


	//
	public function user() {
		return $this->hasOne('User', 'id', 'us_id');
	}

	//详情
	public function detail($where, $field = "*") {
		return $this->with('user')->where('id',$where)->field($field)->find();
	}

	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		return $this->with('user')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
	}
	
	
}

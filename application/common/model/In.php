<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\model\concern\SoftDelete;
/**
 *
 */
class In extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	public function user() {
		return $this->hasOne('User', 'id', 'us_id');
	}
	public function prod() {
		return $this->hasOne('StoProd', 'id', 'prod_id');
	}
	
	/*--状态*/
	public function getZtTextAttr($value, $data) {
		$array = [
			0 => '正常',
			1 => '已选厂',
			2 => '已报道',
			3 => '已入职',
			4 => '已满工',
			5 => '已淘汰',
		];
		return $array[$data['in_zt']];
	}
	/*获取*/
	public function getProdTextAttr($value,$data){
		$v = Db::name('sto_prod')->where('id',$data['prod_id'])->value('prod_name');
		return $v;
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

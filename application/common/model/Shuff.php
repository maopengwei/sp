<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class Shuff extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($arr) {
		
		$arr['shuff_add_time'] = date('Y-m-d H:i:s');

		$rel = $this->insertGetId($arr);
		
		return ['code'=>1,'msg'=>'添加成功'];
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		return $this->save($data, $where);
	}

	//用户账号
	public function getLocaTextAttr($value, $data) {
		$arr = [
			0 => '普通商品',
			1 => '积分商城',
			2 => '拍卖',
			3 => '团购',
			4 => '预售',
			10 => '首页',
		];
		return $arr[$data['shuff_loca']];
	}
	//真实姓名
	public function getUsNameAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_real_name');
		return $name;
	}
}

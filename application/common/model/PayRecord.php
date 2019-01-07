<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\Db;
/**
 * 会员卡
 */
class PayRecord extends Model {
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
	public function tianjia($pay_type, $us_id, $pay_num, $pay_lei, $note,$for) {
		$array = array(
			'pay_type' => $pay_type,
			'us_id' => $us_id,
			'pay_num' => $pay_num,
			'pay_lei' => $pay_lei,
			'pay_note' => $note,
			'pay_for' => $for,
			'pay_add_time' => date('Y-m-d H:i:s'),
		);
		$rel = $this->insertGetId($array);
		if($rel){
			Db::name('user')->where('id',$us_id)->setfield('us_ling',$for); 
		}
		return $rel;
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
	public function getUsTextAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_account');
		return $name;
	}
	//真实姓名
	public function getUsNameAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_real_name');
		return $name;
	}
	//真实姓名
	public function getTypeTextAttr($value, $data) {
		$arr = [
			1 => '微信',
			2 => '支付宝',
			3 => '银行卡',
			4 => '会员卡',
			5 => '线下',
		];

		return $arr[$data['pay_type']];
	}
}

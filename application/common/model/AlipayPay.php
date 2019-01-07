<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class AlipayPay extends Model {
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
	// public function tianjia($us_id, $wec_number, $wec_money, $wec_type, $wec_relevance) {
	public function tianjia($us_id, $ali_number, $ali_money,$ali_type, $ali_relevance) {
		$arr = [
			1=>'交押金',
		];
		$array = [
			'us_id' => $us_id,
			'ali_number' => $ali_number,
			'ali_money' => $ali_money,
			'ali_number' => $ali_number,
			'ali_relevance' => $ali_relevance,
			'ali_type' => $ali_type,
			'ali_note' => $arr[$ali_type],
			'ali_add_time' => date('Y-m-d H:i:s'),
		];
		$rel = $this->insertGetId($array);
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
	//提现姓名
	public function getUsNameAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_real_name');
		return $name;
	}
	//支付类型
	public function getPaWeTypeTextAttr($value, $data) {
		if ($data['pa_we_type'] == 0) {
			return '微信充值';
		}
		$number = Order::where('or_number', $data['pa_we_type'])->value('or_number');
		return $number;
	}
	//提现状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '未支付',
			1 => '支付成功',
		];
		return $array[$data['pa_we_status']];
	}

	/**
	 * 异步成功 修改状态
	 * @param  [type] $number [description]

	 * @return [type]         [description]
	 */
	public function back_success($number) {
		$info = $this->where('ali_number', $number)->find();
		if (!$info) {
			return false;
		}
		//毫无意义的判断
		if ($info['ali_status'] == 1 || $info['ali_status'] == 2) {
			return false;
		}
		$rel = $this->where('id', $info['id'])->setfield('ali_status', 1);
		if ($rel) {

			/*
				'ali_relevance' => $ali_relevance,
				'ali_type' => $ali_type,
				'ali_note' => $arr[$ali_type],

			 */
			model("PayRecord")
			->tianjia(2, $info['us_id'], $info['ali_money'], $info['ali_type'], $info['ali_note'],$info['ali_relevance']);
		}
		return $rel;
	}
}

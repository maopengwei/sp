<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class WechatPay extends Model {
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
	public function tianjia($us_id, $wec_number, $wec_money, $wec_type, $wec_relevance) {
		$arr = [
			1=>'交押金',
		];
		$array = array(
			'us_id' => $us_id,
			'wec_number' => $wec_number,
			'wec_money' => $wec_money,
			'wec_type' => $wec_type,
			'wec_note' => $arr[$wec_type],
			'wec_relevance' => $wec_relevance,
			'wec_add_time' => date('Y-m-d H:i:s'),
		);
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
		$info = $this->where('wec_number', $number)->find();
		if (!$info) {
			return;
		}
		//毫无意义的判断
		if ($info['wec_status'] == 1 || $info['wec_status'] == 2) {
			return;
		}
		$rel = $this->where('id', $info['id'])->setfield('wec_status', 1);
		if ($rel) {
			model("PayRecord")
				->tianjia(1, $info['us_id'], $info['wec_money'], $info['wec_type'], $info['wec_note'],$info['wec_relevance']);
		}
		return $rel;
	}
}

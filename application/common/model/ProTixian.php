<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class ProTixian extends Model {
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
	
		// $arr['tx_recharge'] = cache('setting')['cal_tx']."%";
		// $arr['tx_shidao'] = $arr['tx_num']-$arr['tx_num']*cache('setting')['cal_tx']/100;
		$arr['tx_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetId($arr);
		if ($rel) {
			User::usWalChange($arr['us_id'],$arr['tx_num'],8);
			return [
				'code' => 1,
				'msg' => '提现成功,等待后台审核',
			];
		} else {
			return [
				'code' => 0,
				'msg' => '提现失败',
			];
		}
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
	//提现类型
	public function getTypeTextAttr($value, $data) {
		$array = [
			1 => '银行卡',
			2 => '支付宝',
			3 => '微信',
		];
		return $array[$data['tx_type']];
	}
	//提现状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '待审核',
			1 => '审核通过',
			2 => '已驳回',
		];
		return $array[$data['tx_status']];
	}
}

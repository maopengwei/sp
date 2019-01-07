<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 */
class StoApply extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';


	//关联用户表
	public function user() {
		return $this->hasOne('User', 'id', 'us_id');
	}

	//详情
	public function detail($where, $field = "*") {
		return $this->with('user')->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('user')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}

	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {
		$data['apply_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetid($data);
		return ['code' => 1,'msg' => '添加成功'];
	}



	//状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '未审核',
			1 => '已通过',
			2 => '被驳回',
		];
		return $array[$data['apply_status']];
	}

}

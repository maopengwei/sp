<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 */
class StoAttr extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';


	//关联父属性名
	public function par() {
		return $this->hasOne('StoAttr', 'id', 'attr_pid');
	}
	//关联类型
	public function cate(){
		return $this->hasOne('StoCate','id','cate_id');
	}
	//详情
	public function detail($where, $field = "*") {
		return $this->with('par,cate')->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('par,cate')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}

	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {
		$data['attr_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetid($data);
		return ['code' => 1,'msg' => '添加成功'];
	}


	//状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '未上线',
			1 => '使用中',
		];
		return $array[$data['cate_status']];
	}

}

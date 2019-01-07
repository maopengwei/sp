<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 */
class StoProd extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	

	public function cate() {
		return $this->hasOne('StoCate', 'id', 'cate_id');
	}


	
	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('cate')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {


		
		$data['pd_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetid($data);
		return ['code' => 1,'msg' => '添加成功'];
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		$rel = $this->save($data, $where);
		return [
			'code' => 1,
			'msg' => '修改成功',
			'data' => $rel,
		];
	}

	public function getMerTextAttr($value, $data) {
		if ($data['mer_id'] == 0) {
			return '自营';
		}
		return model('StoMer')->where('id', $data['mer_id'])->value('mer_name');
	}
	public function getZoneTextAttr($value, $data) {
		$arr = [
			0 => '普通商品',
			1 => '积分商城',
			2 => '拍卖',
			3 => '团购',
			4 => '预售',
		];
		return $arr[$data['prod_zone']];
	}
	public function getCateTextAttr($value, $data) {
		return model('StoCate')->where('id', $data['cate_id'])->value('cate_name');
	}
	public function getPicTextAttr($value, $data) {
		$arr = explode(',',$data['prod_pic']);
		return $arr;
	}
}

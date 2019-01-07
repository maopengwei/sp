<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *
 */
class UserAddr extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		return $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {
		$data['addr_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetId($data);
		return [
			'code' => 1,
			'msg' => '成功',
		];
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		$this->save($data, $where);
		return [
			'code' => 1,
			'msg' => '修改成功',
		];
	}

	public function getAddrProvinceTextAttr($value, $data)
    {
        return db('addr_province')->where('code', $data['addr_province'])->value('name');
    }
    public function getAddrCityTextAttr($value, $data)
    {
        return db('addr_city')->where('code', $data['addr_city'])->value('name');
    }
    public function getAddrAreaTextAttr($value, $data)
    {
        return db('addr_area')->where('code', $data['addr_area'])->value('name');
    }
}

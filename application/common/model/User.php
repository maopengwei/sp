<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\model\concern\SoftDelete;
use func\PassN;
/**
 *
 */
class User extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	public function parent() {
		return $this->hasOne('User', 'id', 'us_pid');
	}



	//父账号
	public function getPtelAttr($value, $data) {
		if ($data['us_pid']) {
			return $this->where('id', $data['us_pid'])->value('us_account');
		} else {
			return '空';
		}
	}
	
	/*--状态*/
	public function getZtTextAttr($value, $data) {
		$array = [
			0 => '未选厂',
			1 => '已选厂',
			2 => '已报道',
			3 => '已入职',
			4 => '已满工',
			5 => '已淘汰',
		];
		return $array[$data['us_zt']];
	}
	/*--状态*/
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '黑名单',
			1 => '正常',
			2 => '星标用户',
		];
		return $array[$data['us_status']];
	}
	/*--区代理*/
	public function getagencyTextAttr($value, $data) {
		$array = [
			0 => '非代理',
			1 => '代理',
		];
		return $array[$data['us_is_agency']];
	}
	/*---会员等级*/
	public function getLevelTextAttr($value, $data) {
		$array = [
			0 => '速聘经理',
			1 => '雅琥总监',
		];
		return $array[$data['us_level']];
	}
	
	//详情
	public function detail($where, $field = "*") {
		return $this->with('parent')->where('id',$where)->field($field)->find();
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

		$count = $this->where('us_tel', $data['us_tel'])->count();
		if ($count) {
			return [
				'code' => 0,
				'msg' => '该手机号已存在',
			];
		}
		$cc = $this->where('us_card_id', $data['us_card_id'])->count();
		if ($cc) {
			return [
				'code' => 0,
				'msg' => '该身份证号已存在',
			];
		}
		$data['us_add_time'] = date('Y-m-d H:i:s');
		$data['us_head_pic'] = '/static/admin/img/toutou.png';
		$data['us_pwd'] = PassN::mine_encrypt($data['us_pwd']);
		if (key_exists('us_safe_pwd', $data)) {
			$data['us_safe_pwd'] = PassN::mine_encrypt($data['us_safe_pwd']);
		}
		// 工号
		$us_account = Db::name('user')->order('id desc')->value('us_account');
		if ($us_account) {
			$bb = substr($us_account, -5);
			$cc = substr($us_account, 0, 2);
			$dd = $bb + 1;
			$data['us_account'] = $cc . $dd;
		} else {
			$data['us_account'] = 'ac10001';
		}

		$rel = $this->insertGetId($data);
		if($rel){
			$brr = [
				'us_id' => $rel,
				'in_add_time' => date('Y-m-d H:i:s'),
			];
			Db::name('in')->insert($brr);
			if($data['us_pid']){
				$ccc = $this->dir_count($data['us_pid']);
				if($ccc>=cache('setting')['cal_dir_count']){
					$this->where('id',$data['us_pid'])->setfiled('us_level',1);
				}
			}
			return [
				'code' => 1,
				'msg' => '注册成功',
				'id' => $rel,
			];
		}else{
			return [
				'code' => 0,
				'msg' => '注册失败',
			];
		}
		
	}
	/**
	 * 后台修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function homeInfo($da) {
		
		if (isset($da['us_pwd'])) {
			$da['us_pwd'] = PassN::mine_encrypt($da['us_pwd']);
		} elseif(key_exists('us_pwd',$da)) {
			unset($da['us_pwd']);
		}

		if (isset($da['us_safe_pwd'])) {
			$da['us_safe_pwd'] = PassN::mine_encrypt($da['us_safe_pwd']);
		} elseif(key_exists('us_safe_pwd',$da)) {
			unset($da['us_safe_pwd']);
		}
		model('User')->update($da);
		return [
			'code' => 1,
			'msg' => '修改成功',
		];

	}

	public function xiugai($data, $where) {
		$rel = $this->save($data, $where);
		if ($rel) {
			return [
				'code' => '1',
				'msg' => '修改成功',
			];
		} else {
			return [
				'code' => 0,
				'msg' => '您没有做出修改',
			];
		}

	}

	//送币
	public function songbi($da){
		if($da['song_type']==1){
			if($da['song_num']>0){
				$type = 1;
			}else{
				$type = 2;
			}
			return self::usWalChange($da['id'],abs($da['song_num']),$type);
		}
	}

	//茶币变动
	static public function usWalChange($us_id,$num,$type,$name=''){
		$note = array(
			1 => '后台充值',
			2 => '后台扣除',
			3 => '入职奖励',
			4 => '获得'.$name.'雅琥奖金',
			5 => '获得'.$name.'高峰奖金',
			6 => '获取'.$name.'区代奖励',
			7 => '团队奖励',
			8 => '提现',
			9 => '提现驳回',
			10 => '资金转出',
			11 => '资金转入',
			12 => '摇一摇',
		);
		
		if (in_array($type, array(1,3,4,5,6,7,9,11,12))) {
			$rel = self::where('id', $us_id)->setInc('us_wal', $num);
		} else{
			$rel = self::where('id', $us_id)->setDec('us_wal', $num);
		}
		if($rel){
			model('ProWal')->tianjia($us_id,$num,$type,$note[$type]);
			return [
				'code' => 1,
				'msg' => '成功',
			];
		}else{
			return [
				'code'=>0,
				'msg' => '失败',
			];
		}
	}
	public function shang($id,$gao,$ru){

		/*
			入职奖励
			父账号  
				雅琥赏金
				高峰赏金
			区代奖励
		 */
		if($ru){
			self::usWalChange($id,$ru,3);
		}
		$info = $this->get($id);
		if($info['us_pid'] > 0){
			$parent = $this->get($info['us_pid']);
			if($parent && $parent['us_level']==0){
				self::usWalChange($parent['id'],cache('setting')['cal_dir_su_gu'],4,$info['us_account']);
				if($gao>0){
					self::usWalChange($parent['id'],$gao,5,$info['us_account']);
				}
			}
			if($parent && $parent['us_level']==1){
				self::usWalChange($parent['id'],cache('setting')['cal_dir_ya_gu'],4,$info['us_account']);
				if($gao>0){
					self::usWalChange($parent['id'],$gao,5,$info['us_account']);
				}
			}
		}
		//区代理
		$qu_id = $info['us_agency'];

		$qu = Db::name('user')->where('id',$qu_id)->find();
		if($qu){
			self::usWalChange($qu['id'],cache('setting')['cal_qu_pro'],6,$info['us_account']);
		}

	}


	public function dir_count($id){
		$count = $this->where('us_pid',$id)->count();
		return $count;
	}

	public function team_count($id,$path){
		$map[] = ['us_path', 'like', $path . "," . $id . "%"];
		$count = $this->where($map)->count();
		return $count;
	}
}

<?php
namespace app\admin\controller;

/**
 * 记录
 */
class Record extends Common {

	public function __construct() {
		parent::__construct();
	}

	public function ru(){
		if(is_post()){
			$d = input('post.');
			halt($d);
			model('RecRu')->tianjia($d['us_id'],$d['ru_number'],$d['ru_num'],1);
		}
		$list = model('RecRu')->chaxun($this->map, $this->order, $this->size);
		$num = model("RecRu")->where($this->map)->sum('ru_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
		return $this->fetch();
	}
	public function chu(){

	}
	public function issue(){

	}
	















	/*--------------------支付------------------------*/
	public function payRecord() {
		if (is_post()) {

			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.pay_type') != "") {
			$this->map[] = ['pay_type', '=', input('get.pay_type')];
		}
		if (input('get.pay_lei') != "") {
			$this->map[] = ['pay_lei', '=', input('get.pay_lei')];
		}
		$list = model('PayRecord')->chaxun($this->map, $this->order, $this->size);
		$num = model("PayRecord")->where($this->map)->sum('pay_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	// public function commission() {
	// 	if (is_post()) {

	// 		$rst = model('Tixian')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
	// 		return $rst;
	// 	}
	// 	if (input('get.keywords')) {
	// 		$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
	// 		if (!$us_id) {
	// 			$us_id = 0;
	// 		}
	// 		$this->map[] = ['us_id', '=', $us_id];
	// 	}
	// 	if (input('get.tx_status') != "") {
	// 		$this->map[] = ['tx_status', '=', input('get.tx_status')];
	// 	}
	// 	$list = model('Tixian')->chaxun($this->map, $this->order, $this->size);
	// 	$num = model("Tixian")->where($this->map)->sum('tx_num');
	// 	$this->assign(array(
	// 		'list' => $list,
	// 		'num'=>$num,
	// 	));
	// 	return $this->fetch();
	// }
	// public function txCheck() {
	// 	$id = input('post.id');
	// 	$info = model('Tixian')->get($id);
	// 	$rst = model('Tixian')->xiugai(['tx_status' => input('post.status')], ['id' => input('post.id')]);
	// 	if ($rst) {
	// 		if (input('post.status') == 2) {
	// 			model("Wallet")->tianjia($info['us_id'], $info['tx_num'], 8);
	// 			$this->success('已驳回');
	// 		}else{
	// 			$this->success('审核通过');
	// 		}
	// 	} else {
	// 		$this->error('操作失败');
	// 	}
	// }

	/*---------------------现金积分----------------------*/
	public function wal() {
		
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.type') != "") {
			$this->map[] = ['wal_type', '=', input('get.type')];
		}
		$list = model('ProWal')->chaxun($this->map, $this->order, $this->size);
		$num = model("ProWal")->where($this->map)->sum('wal_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*---------------------注册积分----------------------*/
	public function reg() {
		
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.type') != "") {
			$this->map[] = ['wal_type', '=', input('get.type')];
		}
		$list = model('ProReg')->chaxun($this->map, $this->order, $this->size);
		$num = model("ProReg")->where($this->map)->sum('reg_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*---------------------奖金----------------------*/
	public function msc() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.type') != "") {
			$this->map[] = ['msc_type', '=', input('get.type')];
		}
		$list = model('ProMsc')->chaxun($this->map, $this->order, $this->size);
		$num = model("ProMsc")->where($this->map)->sum('msc_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*---------------------代金----------------------*/
	public function rec() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.type') != "") {
			$this->map[] = ['msc_type', '=', input('get.type')];
		}
		$list = model('ProRec')->chaxun($this->map, $this->order, $this->size);
		$num = model("ProRec")->where($this->map)->sum('rec_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}

	/*--------------积分-----------------*/
	public function integral() {
		if (is_post()) {
			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.in_type') != "") {
			$this->map[] = ['in_type', '=', input('get.in_type')];
		}
		$list = model('Integral')->chaxun($this->map, $this->order, $this->size);
		$num = model("Integral")->where($this->map)->sum('in_num');
		$this->assign(array(
			'list' => $list,
			'num'=>$num,
		));
		return $this->fetch();
	}
	


	//转账记录
	public function transfer() {
		if (is_post()) {

			$rst = model('Tixian')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			if(input("get.type")==1){
				$this->map[] = ['us_id', '=', $us_id];
			}elseif(input("get.type")==0){
				$this->map[] = ['us_to_id', '=', $us_id];
			}else{
				$this->map[] = ['us_id|us_to_id', '=', $us_id];
			}
			
		}
		if (input('get.wa_type') != "") {
			$this->map[] = ['wa_type', '=', input('get.wa_type')];
		}
		$list = model('Transfer')->chaxun($this->map, $this->order, $this->size);
		$num = model('Transfer')->where($this->map)->sum('tr_num');
		$this->assign(array(
			'list' => $list,
			'num' => $num,
		));
		return $this->fetch();
	}



	/*--------------------提现-------------------------*/
	public function tx() {
		if (is_post()) {
			$rst = model('Tixian')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (input('get.status') != "") {
			$this->map[] = ['tx_status', '=', input('get.status')];
		}
		$list = model('Tixian')->chaxun($this->map, $this->order, $this->size);
		$num = model('Tixian')->where($this->map)->sum('tx_num');
		$this->assign(array(
			'list' => $list,
			'num' => $num,
		));
		return $this->fetch();
	}

	public function txCheck() {
		if(is_post()){
			$da = input('post.');
			$id = input('post.id');
			$info = model('Tixian')->get($id);
			$rst = model('Tixian')->xiugai(['tx_status' => input('post.status')], ['id' => input('post.id')]);
			if ($rst) {
				if (input('post.status') == 2) {
					model("ProWal")->tianjia($info['us_id'], $info['tx_num'], 11);
				}
				$this->success('已审核');
			} else {
				$this->error('操作失败');
			}
		}
		
	}


}

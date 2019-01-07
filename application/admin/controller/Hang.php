<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @todo
 */
class Hang extends Common {

	// ------------------------------------------------------------------------


	/*=--------------------------------------------------股份*/
	//出售
	public function sale(){
		// $this->map[] = ['us_id', '=', session('us_id')];
		// $this->map[] = ['issue_type', '=', 1];
		// 
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if ($us_id) {
				$this->map[] = ['us_id','=',$us_id];
			}else{
				$this->map[] = ['us_id','=',9999];
			}
		}
		$list = model("HangIssue")->chaxun($this->map, $this->order, $this->size);
		$num = model('HangIssue')->where($this->map)->sum('issue_num');
		$this->assign(array(
			'num' => $num,
			'list' => $list,
		));
		return $this->fetch();
	
	}
	//交易中
	public function order(){

		$this->map[] = ['deal_status','in',array(0,1)];
		// $this->map[] = ['deal_type', '=', 1];

		if(input('get.status')!=""){
			$this->map[] = ['deal_status','in',input('get.status')];
		}
		// if(input('get.or_number')!=""){
		// 	$this->map[] = ['or_number','in',input('get.or_number')];
		// }
		//时间
		if (input('get.start')) {
			$this->map[] = ['deal_add_time', '>=', input('get.start')];
		}
		if (input('get.end')) {
			$this->map[] = ['deal_add_time', '<=', input('get.end')];
		}
		
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			if(input('get.type')!=""){
				if(input('get.type')==0){
					$this->map[] = ['us_to_id', '=', $us_id];
				}else{
					$this->map[] = ['us_id', '=', $us_id];
				}
				
			}else{
				$this->map[] = ['us_to_id|us_id', '=', $us_id];
			}
		}

		$list = model("HangDeal")->chaxun($this->map,$this->order,$this->size);
		$num = model('HangDeal')->where($this->map)->sum('deal_num');
		$this->assign(array(
			'list'=>$list,
			'num'=>$num,
		));
		return $this->fetch();

	}
	//交易完成
	public function order_fin(){
		$this->map[] = ['deal_status','=',2];

		if(input('get.status')!=""){
			$this->map[] = ['deal_status','in',input('get.status')];
		}
		//时间
		if (input('get.start')) {
			$this->map[] = ['deal_finish_time', '>=', input('get.start')];
		}
		if (input('get.end')) {
			$this->map[] = ['deal_finish_time', '<=', input('get.end')];
		}

		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_real_name|us_tel', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			if(input('get.type')!=""){
				if(input('get.type')==0){
					$this->map[] = ['us_to_id', '=', $us_id];
				}else{
					$this->map[] = ['us_id', '=', $us_id];
				}
				
			}{
				$this->map[] = ['us_to_id|us_id', '=', $us_id];
			}
		}
		$list = model("HangDeal")->chaxun($this->map,$this->order,$this->size);
		$num = model('HangDeal')->where($this->map)->sum('deal_num');
		$this->assign(array(
			'list'=>$list,
			'num'=>$num,
		));
		return $this->fetch();

	}
	//详情
	public function xq(){
		$order = model("HangDeal")->detail(['id'=>input('get.id')]);
		// $rbm = $order['or_money'] * cache('setting')['dollar_rmb']/100;

		$this->assign(array(
			'info' => $order,
			// 'id'=>session('us_id'),
			// 'rbm' => $rbm,
		));
		return $this->fetch();
	}

}

<?php

namespace app\index\controller;

use think\Request;
use func\PassN;
use think\Db;
/**
 * 玩家个人
 */
class Red extends Base
{
	public function index(){
		$arr = [
			'us_red_time' => $this->user['us_red_time'],
		];
		$this->msg($arr);
	}
	public function yao(){
		$min = cache('setting')['red_min'];
		$max = cache('setting')['red_max'];
		$number = rand($min,$max)/100;
		$arr = [
			'num' => $number,
		];
		$this->msg($arr);
	}
	public function ling(){
		if(input('money')){
			Db::name('user')->where('id',$this->user['id'])->setDec('us_red_time',1);
			$rel = model('User')::usWalChange($this->user['id'],input('money'),12);
			$this->s_msg('领取成功');
		}else{
			$this->e_msg('金额不能是0');
		}
		
	}
}
<?php

namespace app\admin\controller;

use think\Request;
use func\PassN;
use think\Db;
/**
 * 玩家个人
 */
class Red extends Common
{
	public function index(){
		if(is_post()){
			$d = input('post.');

			if($d['time']==''){
				$this->error('添加次数不能为0');
			}

			if($d['type']){
				$info = Db::name("user")->where('us_account',$d['us_account'])->where('us_tel',$d['us_tel'])->find();
				if(!$info){
					$this->error("该用户不存在");
				}
				$rel = Db::name('user')->where('id',$info['id'])->setInc('us_red_time',$d['time']);
			}else{
				$list = model('User')->field('id')->select();
				foreach ($list as $k => $v) {
					$rel = Db::name("user")->where('id',$v['id'])->setInc('us_red_time',$d['time']);
				}
			}
			if($rel){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}


		return $this->fetch();
	} 
}
<?php
namespace app\admin\controller;

use Cache;

/**
 * @todo 配置信息管理
 */
class Setting extends Common {
	public function _initialize() {
		parent::_initialize();
	}
	// --- ---------------------------------------------------------------------
	//
	public function index() {
		if (is_post()) {
			$data = input('post.');
			model('SysConfig')->xiugai($data);
			$this->success('修改成功');
		}
		return $this->fetch();
	}
	//系统参数
	public function system() {

		if(is_post()){
			$data = input('post.');
			if($data['type']==1){
				$rel = db('config_level')->where('id',$data['i'])->setfield($data['key'],$data['val']);
				
			}else{
				$rel = db('config_point')->where('id',$data['i'])->setfield($data['key'],$data['val']);
			}
			if($rel){
				Cache::clear();
			}
		}

		$this->assign(array(
			'level'=> cache('level'),
			'point'=> cache('point'),
		));
		return $this->fetch();
	}
	//修改经销商级别优惠内容
	public function edit() {
		if (is_post()) {
			$data = input('post.');
			$rel = db('config_level')->where('id',$data['id'])->setfield('cal_content',$data['cal_content']);
			if($rel){
				Cache::clear();
			}
			return ['code'=>1,'msg'=>'修改成功'];
		}

		$k = input('id') - 1;
		$this->assign(array(
			'k' => $k,
		));
		return $this->fetch();
	}
	
	/*-------------------轮播图*/
	public function shuff(){
		$list = model('Shuff')->chaxun($this->map,$this->order,$this->size);
		$this->assign(array(
			'list'=>$list,
		));
		return $this->fetch();
	}
	public function shuff_add(){
		if (is_post()) {
			$data = input('post.');
			// halt($data);
			$file = request()->file('file');

			if($file){
				$base = uploads($file);
				if($base['code']){
					$data['shuff_pic'] = $base['path'];
				}else{
					return $base;
				}
			}
			//验证器
			$validate = validate('Admin');
			$res = $validate->scene('addshuff')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Shuff')->tianjia($data);
			return $rel;
		}
		return $this->fetch();
	}
	public function shuff_edit(){
		
		if(is_post()){
			$data = input('post.');

			$file = request()->file('file');

			if($file){
				$base = uploads($file);
				if($base['code']){
					$data['shuff_pic'] = $base['path'];
				}else{
					return $base;
				}
			}
			$rel = model('Shuff')->update($data);
			return ['code'=>1,'msg'=>'修改成功'];
		}
		$info = model("Shuff")->where('id',input('id'))->find();
		$this->assign('info',$info);
		return $this->fetch();
	}

}

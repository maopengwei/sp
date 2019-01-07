<?php
namespace app\admin\controller;

use app\common\controller\Base;

/**
 * 基类
 */
class Common extends Base {

	protected $admin;
	public function __construct() {
		parent::__construct();

		if ($this->is_login()) {
			$this->redirect('login/logout');
		}
		$this->admin = model("Admin")->get(session('ad_id'));
		$this->assign('admin',$this->admin);

		$this->auth();
	}

	//登陆验证
	public function is_login() {

		if (!session('ad_id') && session('ad_id')<=0) {
			return true;
		}
		return false;
	}
	
	//权限验证
	public function auth() {

		$meth_name = strtoupper(explode(".", $this->request->pathinfo())[0]);
		$meth_type = strtoupper($this->request->method());

		$result = $this->check($meth_name, $meth_type);
		if ($result) {
			$this->error('您没有权限访问');
		}
	}

	/**
	 * 权限验证
	 * @param  字符串 $name 方法名
	 * @param  字符串 $meth 请求方式
	 * @return bool       bool值
	 */
	public function check($name, $meth) {

		$info = db('rule')
			->where('name', $name)
			->where('meth', $meth)
			->find();
		if (!$info) {
			return false;
		}
		if (in_array($info['id'], session('rules'))) {
			return false;
		}
		return true;
	}
}

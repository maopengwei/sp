<?php
namespace app\admin\controller;

/**
 * @todo 首页操作
 */
class Index extends Common {
	// ------------------------------------------------------------------------
	public function index() {
		return $this->fetch();
	}

	// ------------------------------------------------------------------------
	public function welcome() {
		// 获取平台账户详情
		$us_count = model("User")->count();
		$us_today = model("User")->whereTime('us_add_time', 'today')->count();
		$url = "http://".$_SERVER['HTTP_HOST'];
		$this->assign(array(
			'us_count' => $us_count,
			'us_today' => $us_today,
			'url' => $url,
		));
		return $this->fetch();
	}
	// ------------------------------------------------------------------------

}

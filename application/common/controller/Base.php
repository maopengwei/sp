<?php
namespace app\common\controller;

use think\Controller;
use think\Request;

class Base extends Controller {
	protected $order;
	protected $size;
	protected $map;
	public function initialize() {
		parent::initialize();
		// !cache('setting') && cache('setting',model('Config')->getInfo());
		// !cache('level') && cache('calcu',db('config_level')->select());
		// !cache('point') && cache('point',db('config_point')->select());
		cache('setting',model('SysConfig')->getInfo());
		$this->order = 'id desc';
		$this->size = '20';
		$this->map = [];
	}
}

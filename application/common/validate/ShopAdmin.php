<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加管理员验证器
 */
class ShopAdmin extends Validate {
	protected $rule = [

		'prod_province'  => 'require',
		'prod_city'   => 'require',
		'prod_area' => 'require',
		'prod_name' => 'require',
		'prod_intro' => 'require',
		'prod_describe' => 'require',
        'prod_pic' => 'require',
	];
	protected $field = [
        'prod_province'  => '省',
		'prod_city'   => '市',
		'prod_area' => '地区',
		'prod_name' => '工厂名称',
		'prod_intro' => '工厂介绍',
		'prod_describe' => '工厂描述',
        'prod_lpic' => '工厂主图',
	];
	protected $message = [
	];
	protected $scene = [
		'addprod' => ['prod_province','prod_city', 'prod_area', 'prod_name', 'prod_intro','prod_describe','prod_pic'], //添加产品
	];

}

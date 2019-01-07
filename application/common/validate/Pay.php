<?php
namespace app\common\validate;

use think\Validate;

/**
 * 金额验证器
 */
class Pay extends Validate {
	protected $rule = [
		'num' => 'require|number|gt:0',
		'us_id' => 'require',
		'relevance' => 'require',
		'type' => 'require',
	];
	// protected $field = [
	// 	'us_id' => '金额',
	// 	'us_safe_pwd' => '交易密码',
	// ];
	
	protected $scene = [
		'pay' => ['us_id','num','relevance','type'], //支付
	];

}

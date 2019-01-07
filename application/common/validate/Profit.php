<?php
namespace app\common\validate;

use think\Validate;

/**
 * 金额验证器
 */
class Profit extends Validate {
	protected $rule = [
        'us_tel'    => 'require|mobile',
        
        'tr_account'    => 'require',
        'tr_tel'    => 'require',
        'tr_num'    => 'require|number|gt:50',
        
        'convert_num'    => 'require|number|gt:0',
		'sode'    => 'require',
		'tx_num' => 'require|number|egt:100',
		'tx_account' => 'require',
		'tx_name' => 'require',
		'tx_addr' => 'require',
		'us_safe_pwd' => 'require',
	];
	protected $field = [
        'us_tel' => '手机号',
		'tr_account' => '对方账号',
		'tr_tel' => '对方手机号',
		'tr_num' => '转让数量',
		
        'convert_num' => '转换数量',
		'sode' => '短信验证码',
		'tx_account' => '银行卡号',
		'tx_name' => '收款人',
		'tx_addr' => '银行卡名称',
		


		'tx_num' => '提现金额',
		'tx_type' => '提现类型',
		'us_safe_pwd' => '交易密码',
	];
	protected $message = [
		
	];
	protected $scene = [
		'trans' => ['tr_account','tr_tel','tr_num','us_safe_pwd'],       //转账
		'convert' => ['convert_num', 'us_safe_pwd'], //转换
		'tx' => ['tx_num','tx_type','us_safe_pwd'], //提现
	];

}

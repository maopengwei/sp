<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加管理员验证器
 */
class User extends Validate {
	protected $rule = [
		'p_acc' 	   => 'require',
		'p_tel' 	   => 'require|regex:/^[1][345678][0-9]{9}$/',
		'us_pid'	   => 'require',
		'us_account'   => 'require|alphaNum',
		'us_nick'      => 'require',
		'us_real_name' => 'require',
		'us_tel'	   => 'require|regex:/^[1][345678][0-9]{9}$/',
		'us_pwd' 	   => 'require',
		'us_safe_pwd'  => 'require',
		'us_qu' 	   => 'require',
		'us_type' 	   => 'require',
		'old_pwd' 	   => 'require|alphaNum',
		'sode' 		   => 'require',
		'us_addr_addr'    => 'require',
		'us_addr_tel'     => 'require',
		'us_addr_person'  => 'require',
		'us_card_id'     => 'require',
		'us_card_zheng'     => 'require',
		'us_card_fan'     => 'require',
	];
	protected $field = [
		'p_acc' 	      => '父账号',
		'p_tel' 	      => '父账号手机号',
		'us_pid'          => '父账号',
		'us_account'      => '帐户名',
		'us_nick'      	  => '昵称',
		'us_real_name'    => '用户真实姓名',
		'us_tel'          => '手机号',
		'us_pwd'          => '用户登录密码',
		'old_pwd'         => '原登录密码',
		'us_safe_pwd'     => '用户安全密码',
		'us_qu'           => '区',
		'old_pwd'         => '原密码',
		'sode'		      => '短信验证码',
		'us_addr_addr'    => '收货地址',
		'us_addr_tel'     => '收货电话',
		'us_addr_person'  => '收货人',
		'us_card_id' => '身份证号',
		'us_card_zheng' => '身份证正面',
		'us_card_fan' => '手持身份证照',
	];
	protected $message = [
		// 'us_tel.regex' => '请填写正确的手机号',
		// 'is_coin.require' => '请选择是否使用购物币',
		// 'is_reservation.require' => '请选择是否使用预定',
		// 'is_courier.require' => '请选择是否需要配送',
	];
	protected $scene = [
		'addUser' => ['us_nick','us_real_name', 'us_tel', 'us_pwd','sode','us_card_id','us_card_zheng','us_card_fan'], //添加用户
		'addr' => ['us_addr_addr','us_addr_tel','us_addr_person'],
		'editUser' => ['us_real_name', 'us_tel'], //修改用户
		'pass' => ['us_pwd', 'old_pwd'], //修改密码
		'safe' => ['us_safe_pwd','us_tel','sode'], 
		'forget' => ['us_pwd','us_tel','sode'], //忘记密码
		'shen'  => ['us_real_name','us_card_id','us_card_zheng','us_card_fan']
	];

}

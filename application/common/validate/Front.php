<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加管理员验证器
 */
class Front extends Validate {
	protected $rule = [

		'addr_name' 	=> 'require',
		'addr_tel' 		=> 'require',
		'addr_province' => 'require',
		'addr_city' 	=> 'require',
		'addr_area' 	=> 'require',
		'addr_stree' 	=> 'require',

		'mer_id'     => 'require',
		'prod_name'  => 'require',
		'prod_pic'   => 'require',
		'prod_price' => 'require',
		'cate_id' 	 => 'require',

		'us_id'    => 'require',
		'mer_name' => 'require',
		'mer_pic'  => 'require',

		'sode' =>'require',

		'us_aid' => 'require',
		'a_acc' => 'require',
		'a_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_pid' => 'require',
		'p_acc' => 'require',
		'p_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_qu' => 'require',
		'us_type' => 'require',
		'us_level' => 'require',
		'us_account' => 'require',
		'us_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_pwd' => 'require',
		'us_safe_pwd' => 'require',
		'pwd' => 'require|confirm:us_pwd',
		'safe_pwd' => 'require|confirm:us_safe_pwd',

		'us_real_name' => 'require',

		'ad_account' => 'require',
		'ad_real_name' => 'require',
		'ad_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'ad_pwd' => 'require',
		
	
		
		// 'tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		// 'realname' => 'require',
		// 'pass' => 'require|min:6|max:16',
		// 'secpwd' => 'require|min:6|max:16',
		// 'old_pass' => 'require|min:6|max:16',
		// 'pass1' => 'require|confirm:pass',
		// 'old_secpwd' => 'require|min:6|max:16',
		// 'secpwd1' => 'require|different:secpwd',
		// 'sex' => 'require',
		// 'num' => 'require|number|>:0',
		// 'alipay' => 'require',
		// 'wechat' => 'require',
		// 'status' => 'require',
		// 'group' => 'require',
		// 'name' => 'require',
	];
	protected $field = [

		'addr_name' => '收货人',
		'addr_tel' => '手机号',
		'addr_province' => '省份',
		'addr_city' => '城市',
		'addr_area' => '县区',
		'addr_stree' => '街道信息',
		
		'us_id' => '用户',
		'mer_name' => '商铺名称',
		'mer_pic' => '商铺主图',


		'mer_id' => '商铺',
		'prod_name' => '产品名称',
		'prod_pic' => '主图',
		'prod_price' => '价格',
		'cate_id' => '分类',

		'us_aid' => '节点人',
		'a_acc' => '节点人账号',
		'a_tel' => '节点人手机号',
		'us_pid' => '父账号',
		'p_acc' => '父账号',
		'p_tel' => '父账号手机号',
		'us_qu' => '区',
		'us_type' => '用户类型',
		'us_account' => '用户账号',
		'us_level' => '用户等级',
		'us_tel' => '用户手机号',
		'us_pwd' => '登陆密码',
		'us_safe_pwd' => '安全密码',
		'pwd' => '确认登陆密码',
		'safe_pwd' => '确认安全密码',

		'sode' => '验证码',

		'ad_account' => '管理员账户',
		'ad_real_name' => '管理员真实姓名',
		'ad_pwd' => '管理员登录密码',
		'ad_tel' => '管理员手机号',
		'ptel' => '父账号手机号',
		'us_account' => '帐户名',
		'us_real_name' => '用户真实姓名',
		'us_pwd' => '用户登录密码',
		'us_safe_pwd' => '用户安全密码',

		'st_name' => '门店名称',
		'st_logo' => '门店logo',
		'st_pic' => '门店主图',
		'st_pwd' => '门店登录密码',
		'st_tel' => '门店手机号',
		'st_label' => '标签',

		'st_longitude' => '经度',
		'st_latitude' => '纬度',

		'st_id' => '门店ID',
		'ca_name' => '分类名称',

		'pd_name' => '商品名称',
		'pd_pic' => '商品主图',
		'pd_price' => '商品价格',
		'ca_id' => '分类ID',

		'co_name' => '配送员姓名',
		'co_tel' => '配送员手机号',

		'number' => '订单',
		'addr_id' => '地址',
		'is_coin' => '购物券',
		'is_reservation' => '预定',
		'is_courier' => '配送',

		// 'jine' => '金额',
		// 'pass' => '登录密码',
		// 'pass1' => '二次登录密码',
		// 'old_pass' => '原登录密码',
		// 'secpwd' => '安全密码',
		// 'old_secpwd' => '原安全密码',
		// 'sex' => '性别',
		// 'num' => '金额',
		// 'wechat' => '微信',
		// 'alipay' => '支付宝',
		// 'status' => '状态',
		// 'group' => '分组',
		// 'name' => '管理员',
	];
	protected $message = [
		'number.require' => '请选择产品',
		'addr_id.require' => '请填写正确的手机号',
		'is_coin.require' => '请选择是否使用购物币',
		'is_reservation.require' => '请选择是否使用预定',
		'is_courier.require' => '请选择是否需要配送',
	];
	protected $scene = [
		
		// 'addr' => ['addr_name','addr_tel','addr_province','addr_city','addr_area','addr_stree'], //地址
		'addr' => ['addr_name','addr_tel','addr_stree'], //地址
		'addprod' => ['mer_id','prod_name', 'prod_pic', 'prod_price', 'cate_id'], //添加产品
		'addmer' => ['us_id','mer_name', 'mer_pic'], //添加商店

		'forget' => ['us_tel','us_pwd','pwd','sode'], //忘记密码


		'addUser' => ['us_aid', 'us_qu','us_level','us_account','us_tel','us_pwd','us_safe_pwd'], //添加用户

		'addAdmin' => ['ad_real_name', 'ad_account', 'ad_pwd', 'ad_tel'], //添加管理员
		'editAdmin' => ['ad_account', 'ad_real_name', 'ad_tel'], //修改管理员
		
		'editUser' => ['us_real_name', 'us_tel'], //修改用户
		'forgetUser' => ['us_tel', 'us_pwd'], //忘记密码
		'addStore' => ['st_name', 'st_logo', 'st_pic', 'st_tel', 'st_pwd', 'st_label'], //添加门店
		'editStore' => ['st_name', 'st_logo', 'st_pic', 'st_tel', 'st_label'], //修改门店
		'editTude' => ['st_longitude', 'st_latitude'], //修改经纬度
		'addCate' => ['ca_name', 'st_id'], //添加分类
		
		'editPd' => ['pd_name', 'pd_pic', 'pd_price', 'ca_id'], //修改产品
		'addCour' => ['co_name', 'co_tel'], //修改配送员
		'addOrder' => ['number', 'addr_id', 'is_coin', 'is_reservation', 'is_courier'], //添加订单

		'exchangeManage' => ['jine'], //添加兑换
		'rechargeManage' => ['jine'], //添加充值

		'jine' => ['jine'], //添加订单
	];

}

<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加管理员验证器
 */
class Verify extends Validate {
	protected $rule = [
		// '__token__' => 'token',
		'ad_account' => 'require',
		'ad_real_name' => 'require',
		'ad_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'ad_pwd' => 'require',
		'ptel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_real_name' => 'require',
		'us_tel' => 'require',
		'us_pwd' => 'require',
		'us_safe_pwd' => 'require',

		'st_name' => 'require',
		'st_logo' => 'require',
		'st_pic' => 'require',
		'st_tel' => 'require',
		'st_pwd' => 'require',
		'st_label' => 'require',

		'st_id' => 'require',
		'ca_name' => 'require',

		'st_latitude' => 'require',
		'st_latitude' => 'require',

		'pd_name' => 'require',
		'pd_pic' => 'require',
		'pd_price' => 'require',
		'ca_id' => 'require',

		'co_name' => 'require',
		'co_tel' => 'require',

		'number' => 'require',
		'addr_id' => 'require',
		'is_coin' => 'require',
		'is_reservation' => 'require',
		'is_courier' => 'require',
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
		'ad_account' => '管理员账户',
		'ad_real_name' => '管理员真实姓名',
		'ad_pwd' => '管理员登录密码',
		'ad_tel' => '管理员手机号',
		'ptel' => '父账号手机号',
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
		// 前台
		'addAdmin' => ['ad_real_name', 'ad_account', 'ad_pwd', 'ad_tel'], //添加管理员
		'editAdmin' => ['ad_account', 'ad_real_name', 'ad_tel'], //修改管理员
		'addUser' => ['us_real_name', 'us_tel', 'us_pwd'], //添加用户
		'editUser' => ['us_real_name', 'us_tel'], //修改用户
		'forgetUser' => ['us_tel', 'us_pwd'], //忘记密码
		'addStore' => ['st_name', 'st_logo', 'st_pic', 'st_tel', 'st_pwd', 'st_label'], //添加门店
		'editStore' => ['st_name', 'st_logo', 'st_pic', 'st_tel', 'st_label'], //修改门店
		'editTude' => ['st_longitude', 'st_latitude'], //修改经纬度
		'addCate' => ['ca_name', 'st_id'], //添加分类
		'addPd' => ['pd_name', 'pd_pic', 'pd_price', 'st_id', 'ca_id'], //添加产品
		'editPd' => ['pd_name', 'pd_pic', 'pd_price', 'ca_id'], //修改产品
		'addCour' => ['co_name', 'co_tel'], //修改配送员
		'addOrder' => ['number', 'addr_id', 'is_coin', 'is_reservation', 'is_courier'], //添加订单

		'exchangeManage' => ['jine'], //添加兑换
		'rechargeManage' => ['jine'], //添加充值

		'jine' => ['jine'], //添加订单
	];

}

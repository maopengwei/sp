<?php

namespace app\index\controller;

use think\Request;
use func\PassN;
/**
 * 玩家个人
 */
class User extends Base
{

    // 登录
    public function login()
    {   
        $this->msg($this->user);
    }
    
    //首页
    public function index(){
        $arr = [
            'pic'       => $this->user['us_head_pic'],
            'tel'       => $this->user['us_tel'],
            'real'      => $this->user['us_real_name'],
            'level'     => $this->user['us_level']?'速聘经理':'雅琥总监',
            'yue'       => $this->user['us_wal'],
            'income'    => 0,
            'qualified' => 1,
        ];
        $this->msg($arr);
    }
    
    // 返回 玩家列表
    // public function index()
    // {
    //     $map = [];
    //     $map['status'] = 1;
    //     if (is_numeric(input('param.status'))) {
    //         $map['status'] = input('param.status');
    //     }
    //     if (input('param.pid')) {
    //         $map['pid'] = input('param.pid');
    //     }
    //     if (input('param.keywords')) {
    //         $map['name|tel'] = input('param.keywords');
    //     }
    //     if (is_numeric(input('param.is_agent'))) {
    //         $map['is_agent'] = input('param.is_agent');
    //     }
    //     if (is_numeric(input('param.is_shop'))) {
    //         $map['is_shop'] = input('param.is_shop');
    //     }
    //     if (input('param.p_code')) {
    //         $map['province'] = input('param.p_code');
    //     }
    //     if (input('param.c_code')) {
    //         $map['city'] = input('param.c_code');
    //     }

    //     $list = db('user')->where($map)->paginate(input('param.size'));
    //     $this->result($list);
    // }

    // 获取玩家信息
    public function info()
    {
        if (input('get.us_tel')) {
            $this->map = ['us_tel'=>input('get.us_tel')]; 
        }
        if (input('get.id')) {
            $this->map = ['id'=>input('get.id')]; 
        }
        if($this->map){
            $info = model('User')->detail($this->map);
            if($info){
                $this->msg($info);
            }else{
                $this->e_msg('用户不存在');
            }
        }else{
            $this->msg($this->user);
        }
    }
    
    // 会员信息修改
    public function edit()
    {
        $post = input('post.');
        if ($post) {
            // $post['us_edu'] = $post['us_edu'][0];
            if(is_array($post['us_sex'])){
                $post['us_sex'] = $post['us_sex'][0];
            }
            if(is_array($post['us_edu'])){
                $post['us_edu'] = $post['us_edu'][0];
            }


            $post['id'] = $this->user['id'];
            $rst  = model('User')->homeInfo($post);
            $this->s_msg($rst);
        }else{
            $this->e_msg();
        }
        
    }
    
    public function pass(){
        $post = input('post.');
        if ($post) {

            $validate = validate('User');
            $res = $validate->scene('pass')->check($post);
            if (!$res) {
                $this->e_msg($validate->getError());
            }
            if(PassN::mine_encrypt($post['old_pwd']) != $this->user['us_pwd']){
                $this->e_msg('用户原登录密码错误');
            }
            $arr = [
                'us_pwd' => $post['us_pwd'],
                'id' => $this->user['id'],
            ];
            $rst  = model('User')->homeInfo($arr);
            $this->s_msg($rst);

        }else{
            $this->e_msg();
        }
    }
    
    public function safe(){
        $post = input('post.');
        if ($post) {
            $validate = validate('User');
            $res = $validate->scene('safe')->check($post);
            if (!$res) {
                $this->e_msg($validate->getError());
            }
            if($post['us_tel'] != $this->user['us_tel']){
                $this->e_msg('手机号不一致');
            }

            // $code_info = cache($post['us_tel'] . 'code') ?: "";
            // if (!$code_info) {
            //     $this->e_msg('请重新发送验证码');
            // } elseif ($post['sode'] != $code_info) {
            //     $this->e_msg('验证码不正确');
            // }
            $arr = array_merge($post,['id'=>$this->user['id']]);
            $rst  = model('User')->editInfo($arr);
            $this->s_msg($rst);

        }else{
            $this->e_msg();
        }
    }

    // 团队
    public function team(){
        $info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->field('id,us_path,us_pid,us_account,us_tel')->find();
        if (!$info) {
            $this->e_msg('查无此人');
        }
        $base = array(
            'id' => $info['id'],
            'pId' => $info['us_pid'],
            'name' => $info['us_account'] . "," . $info['us_tel'],
        );
        $znote[] = $base;
        $where[] = array('us_path', 'like', $info['us_path'] . "," . $info['id'] . "%");
        $list = Model('User')->where($where)->field('id,us_pid,us_account,us_tel')->select();
        foreach ($list as $k => $v) {
            $base = array(
                'id' => $v['id'],
                'pId' => $v['us_pid'],
                'name' => $v['us_account'] . "," . $v['us_tel'],
            );
            $znote[] = $base;
        }
        $this->msg($znote);
    }
      
    //添加
    public function add() {
        $data = input('post.');
        
        $rel = model('User')->tianjia($data);

        $this->msg($rel);
        if($rel['code']){
            $this->s_msg($rel);
        }else{
            $this->e_msg($rel);
        }
    }
    
    //激活
    public function active(){
		if(is_post()){
            $data = input('post.');
            if(!$data){
                $this->e_msg('请填写信息');
            }
            $validate = validate('User');
            $res = $validate->scene('addr')->check($data);
            if (!$res) {
                return [
                    'code'  =>  0,
                    'msg'	=>  $validate->getError(),
                ];
            }

            if($this->user['us_status']!=0){
                $this->e_msg('该用户状态不是未激活状态');
            }
            if($this->user['us_wal']<cache('setting')['cal_bd']){
                $this->e_msg('茶币不足');
            }

            $data['us_status'] = 1;
            $data['us_active_time'] = date('Y-m-d H:i:s');
            $arr = array_merge($data,['id'=>$this->user['id']]);
            $rel  = model('User')->editInfo($arr);

			if($rel){
            // if(true){
                model("User")->usWalChange($this->user['id'],cache('setting')['cal_bd'],3);
				// 直推奖
				model('User')->direct_pro($this->user['id']);	
				// 层碰奖励 对碰奖励
				model('User')->ceng_peng_pro($this->user['id']);
                $this->s_msg('报单成功');
			}else{
                $this->s_msg('报单失败');
            }
            
		}
	}

    public function relation()
    {
        if (is_post()) {
            $request = input('post.');
            if ($request['me_content'] == "") {
                $this->e_msg('内容不能为空');
            }
            $data = array(
                'me_title' => '反馈问题',
                'me_content' => $request['me_content'],
                'us_id' => $this->user['id'],
                'me_type' => 2,
            );
            $rel = model('Message')->tianjia($data);
            if ($rel) {
                $this->s_msg('反馈成功');
            } else {
                $this->e_msg('反馈失败');
            }
        }
    }

    /**
	 * 86400 / 24 3600/60    120 两分钟
	 * 修改
	 * @return [type] [description]
	 */
	public function send() {
        $mobile = input('post.us_tel');
        if($mobile){
            if($mobile != $this->user['us_tel']){
                $this->e_msg('手机号不一致');
            }
            if (cache($mobile . 'code')) {
                $this->e_msg('每次发送间隔120秒');
            }else{
                cache($mobile . 'code', 123456,120);
                $this->s_msg('发送成功,现在的验证码是123456');
            }
            $random = mt_rand(100000, 999999);
            $xxx = note_code($mobile, $random);
            $rel = $this->object_array($xxx);
            if ($rel['returnstatus'] == "Faild") {
                $this->e_msg($rel['message']);
            } else {
                cache($mobile . 'code', $random,120);
                $this->s_msg('发送成功');
            }
        }else{
            $this->e_msg("手机号为空");
        }
    }

   
    
}

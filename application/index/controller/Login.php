<?php
namespace app\index\controller;
use app\common\controller\Api;
use think\Db;

class Login extends Api
{
	//注册
    public function reg(){
        if (is_post()) {
			$da = input('post.');
			//验证器
			$validate = validate('User');
			$res = $validate->scene('addUser')->check($da);
			if (!$res) {
				$this->e_msg($validate->getError());
			}
            $code_info = cache($da['us_tel'] . 'code') ?: "";
            if (!$code_info) {
                $this->e_msg('请重新发送验证码');
            } elseif ($da['sode'] != $code_info) {
                $this->e_msg('验证码不正确');
            }
            //父账号
			if ($da['p_acc'] != '') {
				$pinfo = model("User")->where('us_account', $da['p_acc'])->find();
				// halt($pinfo);
				if ($pinfo) {
					$da['us_pid'] = $pinfo['id'];
					$da['us_path'] = $pinfo['us_path'] . ',' . $pinfo['id'];
					$da['us_path_long'] = $pinfo['us_path_long'] + 1;
				} else {
					$this->e_msg('推荐人不存在');
				}
			} else {
				$da['us_pid'] = 0;
				$da['us_path'] = 0;
				$da['us_path_long'] = 0;
			}
			$rel = model('User')->tianjia($da);
			$this->msg($rel);
		}
	}
	
	//忘记密码
    public function forget(){
		
        if (is_post()) {
			$post = input('post.');
            $validate = validate('User');
            $res = $validate->scene('forget')->check($post);
            if (!$res) {
                $this->e_msg($validate->getError());
            }

			$code_info = cache($post['us_tel'] . 'code') ?: "";
			if (!$code_info) {
                $this->e_msg('请重新发送验证码');
            } elseif ($post['sode'] != $code_info) {
                $this->e_msg('验证码不正确');
            }
			$info = Db::name('user')->where('us_tel',$post['us_tel'])->find();
			if(!$info){
				$this->e_msg('该手机号未注册');
			}
			$arr = array_merge($post,['id'=>$info['id']]);
            $rst  = model('User')->homeInfo($arr);
            $this->msg($rst);

        }else{
            $this->e_msg('get');
        }
	}

}

<?php
namespace app\index\controller;
use app\common\controller\Api;
use think\Db;
class Total extends Api
{

    //注册协议
    public function ruzhu(){
        $info =  model("Message")->where('me_type',6)->find();
        $info['me_content'] = html_entity_decode($info['me_content']); 
        $this->msg($info);
    }

    public function user(){
        $id = input('id');
        $info = model('User')->where('id|us_account',$id)->field('id,us_account,us_head_pic,us_person_pic')->find();
        $this->msg($info);
    }
    
   /**
	 * 86400 / 24 3600/60    120 两分钟
	 * 验证码
	 * @return [type] [description]
	 */
	public function send() {
        $mobile = input('post.us_tel');
        $type   = input('post.type');
        if(!$type){
            $this->e_msg('请填入短信类型');
        }
        if($mobile){
            if(db('user')->where('us_tel', $mobile)->count()){
                if ('reg' === $type) {
                    $this->e_msg('该手机号已注册');
                }
            }else{
                // 忘记密码/登陆  获取验证码
                if ('fg' == $type) {
                    $this->e_msg('该手机号未注册账户');
                }
            }
            if (cache($mobile . 'code')) {
                $this->e_msg('每次发送间隔120秒');
            }else{
                cache($mobile . 'code', 123456,120);
                $this->s_msg('发送成功,现在的验证码是123456');
            }
            $random = mt_rand(100000, 999999);
            $xxx = $this->note_code($mobile, $random);
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


    public function note_code($mobile, $content) {
        header('Content-Type:text/html;charset=utf8');
        $sms = config('sms');
        $sms['password'] = ucfirst(md5($sms['password']));
        $sms['content'] = $sms['content'] . $content;
        // $sms['content'] = urlencode($sms['content']);
        $sms['mobile'] = $mobile;
        $query_str = http_build_query($sms);
        $gateway = "http://114.113.154.5/sms.aspx?action=send&" . $query_str;
        // dump($gateway);
        // echo "<br />";
        // $gateway = "http://114.113.154.5/sms.aspx?action=send&userid={$sms['userid']}&account={$sms['account']}&password={$sms['password']}&mobile={$mobile}&content={$sms['content']}&sendTime=";
        // dump($gateway);
        // $gateway = "= "http://114.113.154.5/sms.aspx?action=send&".$q".$query_str;
        // $result = file_get_contents($gateway);
        $url = preg_replace("/ /", "%20", $gateway);
        $result = file_get_contents($url);
        return $xml = simplexml_load_string($result);
        //  $this->object_array($xml);
    }

    /**
	 * 上传图片
	 * @return [type] [description]
	 */
	public function uploads() {
        // dump($_FILES['imgaaaa']);halt(123);
        // halt(input('post.imgaaaa'));
		// try {
		// 	$rel = base64_upload(input('post.img'));
		// } catch (\Exception $e) {
		// 	$this->error($e->getMessage());
		// }
		// if ($rel) {
        //     $arr = [
        //         'code'=>1,
        //         'msg' => "成功",
        //         'data' => $rel,
        //     ];
        //     $this->result($arr);
		// } else {
		// 	$this->e_msg('失败');
        // }
        $bb = env('ROOT_PATH');
        $file = request()->file('imgaaaa');
        if($file){
            $info = $file->validate(['size' => '4096000'])
            ->move($bb . 'public/uploads/');
            if ($info) {
                $path = '/uploads/' . $info->getsavename();
                $path = str_replace('\\', '/', $path);
                $data = array(
                    'code' => 1,
                    'msg' => '上传成功',
                    'data' => $path,
                );
            } else {
                $data = array(
                    'msg' => $file->getError(),
                    'code' => 0,
                );
            }
            $this->msg($data);
        }else{
            $this->e_msg('请传入图片');
        }
        
    }

     /**
     * 上传图片
     * @return [type] [description]
     */
    public function upload() {
        // dump($_FILES['imgaaaa']);halt(123);
        // halt(input(''));
        try {

         $rel = base64_upload(input('post.imgaaaa'));
        } catch (\Exception $e) {
         $this->error($e->getMessage());
        }
        if ($rel) {
            $arr = [
                'code'=>1,
                'msg' => "成功",
                'data' => $rel,
            ];
            $this->msg($arr);
        } else {
         $this->e_msg('失败');
        }
        // $bb = env('ROOT_PATH');
        // $file = request()->file('imgaaaa');
        // if($file){
        //     $info = $file->validate(['size' => '4096000'])
        //     ->move($bb . 'public/uploads/');
        //     if ($info) {
        //         $path = '/uploads/' . $info->getsavename();
        //         $path = str_replace('\\', '/', $path);
        //         $data = array(
        //             'code' => 1,
        //             'msg' => '上传成功',
        //             'data' => $path,
        //         );
        //     } else {
        //         $data = array(
        //             'msg' => $file->getError(),
        //             'code' => 0,
        //         );
        //     }
        //     $this->msg($data);
        // }else{
        //     $this->e_msg('请传入图片');
        // }
        
    }


      public function bgpic(){

       
        $file = request()->file('imgaaaa');
        $id = input('id');
        if($file){
            $bb = env('ROOT_PATH');
            $info = $file->validate(['size' => '4096000'])
            ->move($bb . 'public/uploads/');
            if ($info) {
                $path = '/uploads/' . $info->getsavename();
                $path = str_replace('\\', '/', $path);

                $rel = Db::name('user')->where("id",$id)->setfield('us_person_pic',$path);
                if($rel){
                    $data = array(
                        'code' => 1,
                        'msg' => '上传成功',
                        'data' => $path,
                    );
                }else{
                    $this->e_msg('上传失败');
                }
            } else {
                $data = array(
                    'msg' => $file->getError(),
                    'code' => 0,
                );
            }
            $this->msg($data);
        }else{
            $this->e_msg('请传入图片');
        }

    }

}

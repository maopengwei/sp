<?php
namespace app\index\controller;
use app\common\controller\Api;
use func\PassO;

class Base extends Api
{
      protected $user;

      public function initialize() {
      		parent::initialize();
      		/*获取头部信息*/
      		$header = $this->request->header();
      		$authToken = null;
      		if (key_exists('authtoken', $header)) {
      			   $authToken = $header['authtoken'];
      		}
      		if ($authToken) {
                  $authToken = explode(':', $authToken);
                  $this->user = model('User')->where("us_tel", $authToken[0])->find();
      		} else {
      			   $this->e_msg("token不存在");
          }

            if (empty($this->user)) {
        			$this->e_msg("账号不存在");
        		}

            if (!cache('setting')['web_status']) {
    			     $this->e_msg("网站维护");
            }

            $password = $this->user['us_pwd'];

            $dataStr = PassO::jsDecrypt($authToken[1], $password);

            // $dataStr  = $this->jsDecrypt($authToken[1], $password);

            $dataStr = explode(':', $dataStr);

            if (empty($dataStr)) {
                $this->e_msg('no access');
            }
           
            if ($dataStr[0] != $_SERVER['REQUEST_URI']) {
                $this->e_msg('密码错误');
            }
        }
        
    // public function initialize(){
    //     parent::initialize();
    //     $this->user = model('User')->where("us_tel",13000000000)->find();
    // }
    // private function jsDecrypt($encryptedData, $privateKey, $iv = "O2%=!ExPCuY6SKX(")
    // {
    //     $encryptedData = base64_decode($encryptedData);
    //     // mcrypt_decrypt php7.1以后，不建议用
    //     $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);

    //     $decrypted = rtrim($decrypted, "\0");

    //     return $decrypted;
    // }
    
}

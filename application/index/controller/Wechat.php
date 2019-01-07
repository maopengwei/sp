<?php

namespace app\index\Controller;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use think\Facade\Log as lllog;
use func\PassN;
use think\Db;
class Wechat extends Common
{
    //速聘配置
    protected $config = [
        // 'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb20718414ac499a5', // 公众号 APPID
        // 'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
        'mch_id' => '1521707081',
        // 'key' => 'qwertyuiopLKJHGFDSA654321ZXCvbnm',
        'key' => 'tW6RwlwdbuHVklMY4HoWd70RpfT1Bqm1',
        'app_secret' => 'e38924c7e9e49f742d25262f3c4a9d10',
        'notify_url' => 'http://admin.yahoosp.cn/index/wechat/notify',
        'return_url' => 'http://www.yahoosp.cn/Personal',
        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'debug'
        ],
        // 'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ];

    // public function bb(){
    //     return $this->fetch();
    // }

    public function index()
    {
        $d = input('post.');
        // halt($d);
        $validate = validate('Pay');
        $res = $validate->scene('pay')->check($d);
        if (!$res) {
            $this->e_msg('非法操作');
        }
        // halt($d);
        // $openid = $this->GetOpenid();


        /*
            必要参数   
                用户id   $us_id
                订单编号   $orderid
                金额       $num
                类型       $type (订单类型) 1领取礼包
                作用       $relevance         (礼包id) 
         */
        
        $us_id = $d['us_id'];
        $orderid = "sp" . date("YmdHis") . rand(100, 999);
        $num = $d['num'];
        $type = $d['type'];
        $relevance = $d['relevance'];

        $uu = Db::name('user')->where('id',$us_id)->field('id,us_account')->find();
        if(!$uu){
            $this->error('该用户不存在');
        }
     
        $rel = model('WechatPay')->tianjia($us_id, $orderid, $num, $type, $relevance);
       
        /*
            支付代码

         */

        // $order = [
        //     'out_trade_no' => time(),
        //     'total_amount' => '0.01',
        //     'subject'      => '手机支付',
        // ];

        $order = [
            'out_trade_no' =>$orderid,
            'body' => '公众号支付',
            'total_fee'      => '1',
            'openid' => $d['openid'],
        ];
        $result = Pay::wechat($this->config)->mp($order);
        // $bb = Pay::wechat($this->config)->mp($order);
        $brr = [
            'code'=>1,
            'data' => $result,
        ];
        $this->msg($brr);

        // if ($rel) {
        //     $rst = model('WechatPay')->back_success($orderid);
        //     if ($rst) {
        //         $this->s_msg('支付成功');
        //     }
        // }
        //扫码支付
        // $result =  $wechat->scan($order);
        // $url = $result->code_url;

        // $this->assign('url2',$url);
        // return $this->fetch();
    }
    public function scan(){

        $d = input('post.');
        $validate = validate('Pay');
        $res = $validate->scene('pay')->check($d);
        if (!$res) {
            $this->e_msg('非法操作');
        }

        /*
            必要参数   
                用户id   $us_id
                订单编号   $orderid
                金额       $num
                类型       $type (订单类型) 1领取礼包
                作用       $relevance         (礼包id) 
         */
        
        $us_id = $d['us_id'];
        $orderid = "sp" . date("YmdHis") . rand(100, 999);
        $num = $d['num'];
        $type = $d['type'];
        $relevance = $d['relevance'];

        $uu = Db::name('user')->where('id',$us_id)->field('id,us_account')->find();
        if(!$uu){
            $this->error('该用户不存在');
        }
     
        $rel = model('WechatPay')->tianjia($us_id, $orderid, $num, $type, $relevance);

        $order = [
            'out_trade_no' => $orderid,
            'body' => '扫码支付',
            'total_fee'      => '1',
        ];
        // 扫码支付使用 模式二
        $result = Pay::wechat($this->config)->scan($order);
        $this->msg($result['code_url']);
    }
    public function notify()
    {
        lllog::write(1234,'notice');
        $pay = Pay::wechat($this->config);
        lllog::write($pay,'notice');
        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            $arr = $data->all();
            lllog::write($arr,'notice');
            if($arr['result_code']=='SUCCESS'){
                 model('WechatPay')->back_success($arr['out_trade_no']);
            }
            Log::debug('Wechat notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }

    public function GetOpenid()
    {
        //通过code获得openid
        /*if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);

            $url = $this->_CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }*/
        $code = input('code');
        $openid = $this->getOpenidFromMp($code);
        $arr = [
            'code' => 1,
            'openid' => $openid,
        ];
        $this->msg($arr);
        return $openid;
    }
    private function _CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->config['app_id'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        return $buff;
    }

    public function getOpenidFromMp($code)
    {
        $url = $this->__createOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // if (WxPayConfig::$CURL_PROXY_HOST != "0.0.0.0"
        //     && WxPayConfig::$CURL_PROXY_PORT != 0
        // ) {
        //     curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::$CURL_PROXY_HOST);
        //     curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::$CURL_PROXY_PORT);
        // }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;

        $openid = $data['openid'];
        return $openid;
    }

    private function __createOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->config['app_id'];
        $urlObj["secret"] = $this->config['app_secret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }


    public function index1()
    {
    	// if(is_post()){
		$d = input('post.');
		// halt($d);
		// $validate = validate('Pay');
        //       $res = $validate->scene('pay')->check($d);
        //       if (!$res) {
        //           $this->e_msg($validate->getError());
        //       }
  		$us_id = 1;
  		$orderid = "sp" . date("YmdHis") . rand(100, 999);
  		$num = 100;
  		$type = 1;
        $relevance = 1;
        // halt($d);

        $uu = Db::name('user')->where('id',$us_id)->field('id,us_account,us_safe_pwd')->find();
        if(!$uu){
        	$this->error('该用户不存在');
        }
        // halt($d['us_safe_pwd']);

        // if(PassN::mine_encrypt($d['us_safe_pwd']) != $uu['us_safe_pwd']){
        //     $this->e_msg('安全密码不正确');
        // }
        // $rel = model('WechatPay')->tianjia($us_id, $orderid, $num, $type, $relevance);

         $order = [
            'out_trade_no' => $orderid,
            // 'total_fee' => 1, // **单位：分**
            'total_fee' => $num*100, // **单位：分**
            'body' => '移动网页支付',
        ];
        $wechat = Pay::wechat($this->config);   
        return $wechat->wap($order)->send();


        if ($rel) {
     		$rst = model('WechatPay')->back_success($orderid);
     		if ($rst) {
         		$this->s_msg('支付成功');
     		}
        }
    	
    }


    
    
}
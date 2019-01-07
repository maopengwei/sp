<?php
namespace app\index\controller;

use think\Facade\Log;
use app\common\controller\Base;
use func\Http;
/**
 * 微信登录控制器
 */
class Wec extends Base
{
    
    protected static $appid = 'wxb20718414ac499a5';
    protected static $appsecret = 'e38924c7e9e49f742d25262f3c4a9d10';
    /*protected $callback_url;
    public function initialize(){
        parent::initialize();
        $this->appid = 'wxb20718414ac499a5';
        $this->appsecret = 'e38924c7e9e49f742d25262f3c4a9d10';
        // $this->callback_url = 'http://admin.yahood.cn/index/authwec/token';
    }*/



    public static function accessToken()
    {
        // if(!cache('access')){
        //         $response = Http::get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$appid . '&secret=' . self::$appsecret);
        //         cache('access',$response,7200);
        // }else{
        //     $response = cache('access');
        // }
        $response = Http::get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$appid . '&secret=' . self::$appsecret);
        
        return $response;
        


       /* $cache = Cache::get('access_token');
        if (empty($cache) || $cache->expires_timestamp < time()) {
            $response = Http::get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$appid . '&secret=' . self::$secret);
            $response->expires_timestamp = time() + ($response->expires_in - 30);
            Cache::put('access_token', $response);
            $data = $response;
        } else {
            $data = $cache;
        }
        return $data;*/
    }



    /**
     * 获取 jsapi_ticket
     * @author 刘健 <59208859@qq.com>
     * @return stdClass {"errcode":0,"errmsg":"ok","ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKdvsdshFKA","expires_in":7200,"expires_timestamp":1479797789}
     */
    public static function jsapiTicket()
    {
        $wechat = self::accessToken();
        $response = Http::get('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $wechat->access_token . '&type=jsapi');
        $response->expires_timestamp = time() + ($response->expires_in - 30);
        return $response;

       /* $cache = Cache::get('jsapi_ticket');
        if (empty($cache) || $cache->expires_timestamp < time()) {
            $wechat = self::accessToken();
            $response = Http::get('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $wechat->access_token . '&type=jsapi');
            $response->expires_timestamp = time() + ($response->expires_in - 30);
            Cache::put('jsapi_ticket', $response);
            $data = $response;
        } else {
            $data = $cache;
        }
        return $data;*/
    }


    // get openid
    /*public function index()
    {
        if($this->is_weixin()){
            $wechat = db("user_wechat")->where('openid',session('openid'))->find();
            if($wechat){
                $info = model("User")->detail($wechat['us_id']);
                session('user',$info);
                $this->redirect('user/index');
            }else{
                $this->redirect('login/choose');
            }
        }else{
            $params = array();
            $params['appid'] = $this->appid;
            $params['redirect_uri'] = $this->callback_url;
            $params['response_type'] = 'code';
            $params['scope'] ='snsapi_login';
            $params['state'] = 123;
            $url =  "https://open.weixin.qq.com/connect/qrconnect?" . http_build_query($params);
            header("location: $url");
        }
        $redirect_uri = 'http://als.jugekeji.cn/index/authwec/token';
        $redirect = urlEncode($redirect_uri);
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->appid."&redirect_uri=".$redirect."&response_type=code&scope= snsapi_login&state=123#wechat_redirect";

        $url = urlencode($url);

        $params = array();
        $params['appid'] = $this->appid;
        $params['redirect_uri'] = $this->callback_url;
        $params['response_type'] = 'code';
        $params['scope'] ='snsapi_login';
        $params['state'] = 123;
        $url =  "https://open.weixin.qq.com/connect/qrconnect?" . http_build_query($params);
        header("location: $url");

        halt($url);

        halt($url); //https%3A%2F%2Fopen.weixin.qq.com%2Fconnect%2Fqrconnect%3Fappid%3Dwx02ef51efedaffde6%26redirect_uri%3Dhttp%253A%252F%252Fals.jugekeji.cn%252Findex%252Fauthwec%252Ftoken%26response_type%3Dcode%26scope%3D+snsapi_login%26state%3D123%23wechat_redirect"
        
        
        下面插件生产的二维码地址
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=wx02ef51efedaffde6&redirect_uri=http%3A%2F%2Fals.jugekeji.cn%2Findex%2Fauthwec%2Ftoken&response_type=code&scope=snsapi_login";
        


        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->appid."&redirect_uri=".$redirect."&response_type=code&scope= snsapi_base&state=123#wechat_redirect";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$redirect."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
        // $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$this->callback_url."&response_type=code&scope=snsapi_base&state=".session('url')."#wechat_redirect";

          
            /*点击微信登陆   
            如果在微信浏览器中   
                搜索openid  搜到直接登陆
                    未搜索到   判断  已有账户绑定    没有账户注册
            没有在微信浏览器中
            出现一个二维码*/ 
        /*header("location: $url");
    }*/

    //获取openid
    /**
     *
     * @param  [type] $code [code值]
     * @return [type]       [description]
     */
    /*public function token() {
        $arr = input('');
        Log::write($arr,'notice');
        session('us_id',input('state'));
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $arr['code'] . "&grant_type=authorization_code";

        $weixin = file_get_contents($url); //通过code换取网页授权access_token
        $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
        $array = get_object_vars($jsondecode); //转换成数组
        $wechat = db("user_wechat")->where('openid',$array['openid'])->find();
        if($wechat){
            $info = model("User")->detail($wechat['us_id']);
            session('user',$info);
            $this->redirect('user/index');
        }else{
            session('token',$array);
            session('openid',$array['openid']);
            $this->redirect('login/choose');
        }

        session('openid',$array['openid']);
        $this->redirect(input('state'));
    }*/

    /*public function index1(){
        $oauth = new \Henter\WeChat\OAuth($this->appid, $this->appsecret);
        $url = $oauth->getAuthorizeURL($this->callback_url);
        halt($url);
        $url = $oauth->getWeChatAuthorizeURL($this->callback_url);
        // halt($url);
        header("location: $url");
        // halt($url);
        // $url = $oauth->getWeChatAuthorizeURL($this->redirect_uri);
        // 
        // string(182) "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx02ef51efedaffde6&redirect_uri=http%3A%2F%2Fals.jugekeji.cn%2Findex%2Fauthwec%2Ftoken&response_type=code&scope=snsapi_login"
        // 
        //生成二维码 string(175) "https://open.weixin.qq.com/connect/qrconnect?appid=wx02ef51efedaffde6&redirect_uri=http%3A%2F%2Fals.jugekeji.cn%2Findex%2Fauthwec%2Ftoken&response_type=code&scope=snsapi_login"
    }*/
}

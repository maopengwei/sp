<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function wechat(){

        /*$time = time();
        $str = GetRandStr(16);
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/index/index/wechat';

        $url = request()->url(true);

        if(!cache('ticket1')){
           $ticket = (Wec::jsapiTicket())->ticket;
           cache('ticket1', $ticket, 3600);  
        }else{
            $ticket = cache('ticket1');
        }

        $urlArr = array(
            'jsapi_ticket=' . $ticket,
            'noncestr=' . $str,
            'timestamp=' . $time,
            'url=' . $url,
        );
        $sss = implode('&', $urlArr);
        $signature = sha1($sss);
        $arr = [
            'appId' => 'wxb20718414ac499a5',
            'timestamp' => $time,
            'nonceStr' => $str,
            'signature' => $signature,
            'url' => $url,
            //timestamp: , // 必填，生成签名的时间戳
            //nonceStr: '', // 必填，生成签名的随机串
            //signature: '',// 必填，签名
        ];
        // halt($arr);
        $this->assign($arr);*/
        if(!cache('ticket')){
           $ticket = (Wec::jsapiTicket())->ticket;
           cache('ticket', $ticket, 3600);  
        }else{
            $ticket = cache('ticket1');
        }
        $url = request()->url(true);
        $this->assign([
            'url' => $url,
            'ticket' => $ticket,
        ]);

    	return $this->fetch();
    }
    
}

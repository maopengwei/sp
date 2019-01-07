<?php

namespace app\index\Controller;

use Yansongda\Pay\Pay as ppp;
use Yansongda\Pay\Log;
use think\Facade\Log as lllog;
use func\PassN;
class Pay extends Common
{

    public function index(){
        $info['money'] = 133;
        $info['type'] = 1;
        $this->assign('info',$info);
        return $this->fetch();
    }

}
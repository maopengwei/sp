<?php
namespace app\index\controller;
use func\PassN;
use think\Db;
class Profit extends Base 
{
    public function wal(){
        $this->map[] = ['us_id', '=', $this->user['id']];
        if(input('size')){
            $this->size = input('size');
        }
        if(input('type')==1){
            $this->map[] = ['wal_type','in',[3,4,5,6,7]];
        }
        $list = model('ProWal')->chaxun($this->map, $this->order, $this->size);
        if(input('type')!=1){
            foreach ($list as $k => $v) {
                $list[$k]['wal_add_time'] = substr($v['wal_add_time'],0,10);
            }
        }
        $this->msg($list);
    }
    // 奖励明细
    public function msc(){
        $this->map[] = ['us_id', '=', $this->user['id']];
        if(input('size')){
            $this->size = input('size');
        }
        $list = model('ProMsc')->chaxun($this->map, $this->order, $this->size);
        $this->msg($list);
    }

    // 转账转账
    public function trans(){
        if(is_post()){
            $d = input('post.');

            $validate = validate('Profit');
            $res = $validate->scene('trans')->check($d);
            if (!$res) {
                $this->e_msg($validate->getError());
            }


            $info = Db::name('user')->where('us_tel',$d['tr_tel'])->where('us_account',$d['tr_account'])->find();
            if(!$info){
                 $this->e_msg('对方账号和手机号不匹配');
            }

            if($this->user['id']==$info['id']){
                $this->e_msg('您不能转给自己');
            }

            if(PassN::mine_encrypt($d['us_safe_pwd']) != $this->user['us_safe_pwd']){
                $this->e_msg('安全密码不正确');
            }else{
                unset($d['us_safe_pwd']);
            }
           
            if($this->user['us_wal']<$d['tr_num']){
                $this->e_msg('您的金额不足');
            }
            $rel = model('ProTransfer')->tianjia($d,$this->user['id'],$info['id']);
            $this->msg($rel);

        }else{
            
           $this->e_msg('get');
        
        }
    }
    public function transfer(){
        if(is_post()){
             $this->map[] = ['us_id|us_to_id', '=', $this->user['id']];
            $list = model('ProTransfer')->chaxun($this->map, $this->order, $this->size);
            foreach ($list as $k => $v) {
                if($v['us_id']==$this->user['id']){
                    $list[$k]['tr_num'] = '-'.$v['tr_num'];
                }
            }
            $this->msg($list);
        }else{
            $this->e_msg('get');
        }
    }
   
    public function tx(){
        $d = input('post.');
        $validate = validate('Profit');
        $res = $validate->scene('tx')->check($d);
        if (!$res) {
            $this->e_msg($validate->getError());
        }
        if(PassN::mine_encrypt($d['us_safe_pwd']) != $this->user['us_safe_pwd']){
            $this->e_msg('交易密码不正确');
        }else{
            unset($d['us_safe_pwd']);
        }
        if($d['tx_num'] < cache('setting')['tx_money']){
            $this->e_msg('提现金额必须大于'.cache('setting')['tx_money']);
        }

        // $code_info = cache($d['us_tel'] . 'code') ?: "";
        // if (!$code_info) {
        //     $this->e_msg('请重新发送验证码');
        // } elseif ($d['sode'] != $code_info) {
        //     $this->e_msg('验证码不正确');
        // }
        if($this->user['us_wal']<$d['tx_num']){
            $this->e_msg('您的奖励分不足');
        }
        if($d['tx_type']==1){
                if(!$this->user['us_bank'] || !$this->user['us_bank_person'] || !$this->user['us_bank_number']){
                $this->e_msg('请到个人中心完善银行信息');
            }
            $d['tx_account'] = $this->user['us_bank_number'];
            $d['tx_addr'] = $this->user['us_bank'];
            $d['tx_name'] = $this->user['us_bank_person'];
        }elseif($d['tx_type']==2){
            if(!$this->user['us_alipay']){
                $this->e_msg('请到个人中心完善支付宝信息');
            }
            $d['tx_account'] = $this->user['us_alipay'];
        }elseif($d['tx_type']==3){
            if(!$this->user[ 'us_wechat']){
                $this->e_msg('请到个人中心完善微信信息');
            }
            $d['tx_account'] = $this->user['us_wechat'];
        }
        $d['us_id'] = $this->user['id'];
        // halt($d); 
        $rel = model("ProTixian")->tianjia($d);
        $this->s_msg('提现成功,等待后台审核');

    }
    
    public function tx_list(){
        if(is_post()){
            $this->map[] = ['us_id', '=', $this->user['id']];
            if(input('size')){
                $this->size = input('size');
            }
            $list = model("ProTixian")->chaxun($this->map, $this->order, $this->size);
            $this->msg($list);
        }
        return $this->fetch();
    }
}

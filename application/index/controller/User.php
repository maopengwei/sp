<?php

namespace app\index\controller;

use think\Request;
use func\PassN;
use think\Db;
/**
 * 玩家个人
 */
class User extends Base
{

    /* 

        身份认证审核

    */
    public function card(){
        $info = Db::name('User')->where('id',$this->user['id'])->field('id,us_real_name,us_is_shen,us_card_id,us_card_zheng,us_card_fan')->find();
        $this->msg($info);
    }
    public function shen(){
        if(is_post()){
            $d = input('post.');

            $validate = validate('User');
            $res = $validate->scene('shen')->check($d);
            if (!$res) {
                $this->e_msg($validate->getError());
            }
            $d = [
                'us_real_name'  => $d['us_real_name'],
                'us_card_id'    => $d['us_card_id'],
                'us_card_zheng' => $d['us_card_zheng'],
                'us_card_fan'   => $d['us_card_fan'],
                'us_is_shen'    => 0,
            ];
            $rel = Db::name('user')->where('id',$this->user['id'])->update($d);
            if($rel){
                $this->s_msg('认证成功，等待后台审核');
            }else{
                $this->e_msg('认证失败');
            }
        }
    }

    /*
        礼包

     */
    //已领取礼包
    public function gift(){
        if(is_post()){
           $info = model('InGift')->where('id',input('id'))->find(); 
            $this->msg($info);
        }else{
            $this->error('get');
        }
    }
    
    //领取礼包协议
    public function xieyi(){
        $info =  model("Message")->where('me_type',5)->find();
        $info['me_content'] = html_entity_decode($info['me_content']); 
        $this->msg($info);
    }
    

    //礼包列表
    public function inGift(){
        if(is_post()){

           $list = model('InGift')->where('cate_pid',0)->field('id,cate_name,cate_pic,cate_detail')->select();

            $this->msg($list);
        }else{
            $this->error('get');
        }  
    }



    
    /*
        团队

     */

    public function team(){

        $map1[] = ['us_level','=',0];
        $map1[] = ['us_path','like',$this->user['us_path'].','.$this->user['id']."%"];
        $map1[] = ['us_path_long','<=',$this->user['us_path_long']+2];

        $map2[] = ['us_level','=',1];
        $map2[] = ['us_path','like',$this->user['us_path'].','.$this->user['id']."%"];
        $map2[] = ['us_path_long','<=',$this->user['us_path_long']+2];

        $dir =  model("User")->where('us_pid',$this->user['id'])->count();

        $man = model("User")->where('us_pid',$this->user['id'])->where('us_sex','男')->count();
        $women  = model("User")->where('us_pid',$this->user['id'])->where('us_sex','女')->count();

        $er = model("User")->where('us_pid',$this->user['id'])->whereTime('us_birthday','>',strtotime('-20 year'))->count();
        $san = model("User")->where('us_pid',$this->user['id'])->whereTime('us_birthday','between',[strtotime('-30 year'),strtotime('-20 year')])->count();
        $si = model("User")->where('us_pid',$this->user['id'])->whereTime('us_birthday','between',[strtotime('-40 year'),strtotime('-30 year')])->count();
        $wu = model("User")->where('us_pid',$this->user['id'])->whereTime('us_birthday','<',strtotime('-40 year'))->count();
        
        $arr = [
            'sp'       => model("User")->where($map1)->count(),
            'yh'       => model("User")->where($map2)->count(),
            'man'       => $man,
            'women'       => $women,
            'er' => $er,
            'san' => $san,
            'si' => $si,
            'wu' => $wu,
        ];
        $this->msg($arr);
    }

    public function dir_sp(){
        // $this->map[] = ['us_pid','=',$this->user['id']];
        $this->map[] = ['us_level','=',0];
        $this->map[] = ['us_path','like',$this->user['us_path'].','.$this->user['id']."%"];
        $this->map[] = ['us_path_long','<=',$this->user['us_path_long']+2];
        if(input('name')){
            $this->map[] = ['us_account|us_real_name|us_tel','=',input('name')];
        }
        $list = model("User")->where($this->map)->select();
        foreach ($list as $k => $v) {
            $list[$k]['team'] = model("User")->where('us_pid',$v['id'])->count();
            $in = model('In')->where('us_id',$v['id'])->find();
            if($in['prod_id']){
                $in['prod_name'] = Db::name('sto_prod')->where('id',$in['prod_id'])->value('prod_name');
            }else{
                $in['prod_name'] = 0;
            }
            $list[$k]['in'] = $in;
        }
        $this->msg($list);
    }
    public function dir_yh(){
         // $this->map[] = ['us_pid','=',$this->user['id']];
        $this->map[] = ['us_level','=',1];
        $this->map[] = ['us_path','like',$this->user['us_path'].','.$this->user['id']."%"];
        $this->map[] = ['us_path_long','<=',$this->user['us_path_long']+2];
        if(input('name')){
            $this->map[] = ['us_account|us_real_name|us_tel','=',input('name')];
        }
        $list = model("User")->where($this->map)->select();
        foreach ($list as $k => $v) {
            $list[$k]['team'] = model("User")->where('us_pid',$v['id'])->count();
            $in = model('In')->where('us_id',$v['id'])->find();
            if($in['prod_id']){
                $in['prod_name'] = Db::name('sto_prod')->where('id',$in['prod_id'])->value('prod_name');
            }else{
                $in['prod_name'] = 0;
            }
            $list[$k]['in'] = $in;
        }
        $this->msg($list);
    }


    /*
        
        门店输送

     */

    public function mensong(){
        if(is_post()){
            // dump($this->user['id']);
            $this->map[] = ['us_agency','=',$this->user['id']];
            $mount = input('bd_time');
            $this->size = 1; 

            if(input('bd_time')){
                $time = strtotime(input('bd_time'));
                $this->map[] = ['in_bd_time','between',[$time,$time_86400]];
            }else{
                $ll = Db::name('user')
                ->alias('a')
                ->join('in w','a.id = w.us_id')
                ->field('a.id,a.us_account,w.in_bd_time,w.in_zt')
                ->where($this->map)
                ->paginate($this->size, false, ['query' => request()->param()]);
                $this->msg($ll); 
            }
        }else{
            $this->e_msg('get');
        }
    }
    

    public function pic_cha(){
       
        $file = request()->file('imgaaaa');

        if($file){
            $bb = env('ROOT_PATH');
            $info = $file->validate(['size' => '4096000'])
            ->move($bb . 'public/uploads/');
            if ($info) {
                $path = '/uploads/' . $info->getsavename();
                $path = str_replace('\\', '/', $path);

                $rel = Db::name('user')->where("id",$this->user['id'])->setfield('us_person_pic',$path);
                if($rel){
                    $data = array(
                        'code' => 1,
                        'msg' => '上传成功',
                        'data' => $path,
                    );
                    $this->msg($data);
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
    
    //区代
    public function qu(){
        if(is_post()){
            $info = Db::name('agency')->where('us_id',$this->user['id'])->find();
            $this->msg($info);
        }else{
            $this->e_msg('get');
        }
    }




    // 登录
    public function login()
    {   
        $this->msg($this->user);
    }
    
    //首页
    public function index(){
        $arr = [
            'id' => $this->user['id'],
            'web_phone' => cache('setting')['web_phone'],
            'pic'       => $this->user['us_head_pic'],
            'tel'       => $this->user['us_tel'],
            'real'      => $this->user['us_real_name'],
            'level'     => $this->user['us_level']?'速聘总监':'速聘经理',
            'yue'       => $this->user['us_wal'],
            'us_is_agency' => $this->user['us_is_agency'], 
            'us_ling' => $this->user['us_ling'], 
            'income'    => Db::name('pro_wal')->where('us_id',$this->user['id'])->where('wal_type','in',[3,4,5,6,7])->whereTime('wal_add_time','month')->sum('wal_num'),
            'qualified' => Db::name('user')->where('us_pid',$this->user['id'])->where('us_man_time','month')->count(),
        ];
        $this->msg($arr);
    }
    
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

    /*
        
        根据提交的信息 
        在工厂中添加用户id  联系方式
        用户in表中添加 工厂id   选厂时间
        做入职记录  选择工厂
 
     */
    public function inJob(){
        if(is_post()){
            $prod_id = input('post.id');
            if(!$prod_id){
                $this->e_msg("未传入工厂id");
            }
            $gong = Db::name('sto_prod')->where("id",$prod_id)->field('id,prod_name,prod_gao,prod_ru')->find();
            if(!$gong){
                $this->e_msg("该工厂不存在");                                 
            }
            // dump($this->user['id']);
            $info = model("In")->with('user')->where('us_id',$this->user['id'])->find();
            if(!$info){
                $this->e_msg('该账号不存在');
            }
            if($this->user['us_is_shen']!=1){
                $this->e_msg("身份认证未通过");
            }
            // halt($info);
            if($info['in_zt']!=0){
                $this->e_msg('您已经选过工厂了');
            }
            $arr = [
                'prod_id' => $prod_id,
                'in_zt' => 1,   
                'in_ij_time' => date("Y-m-d H:i:s"),
                'in_gao' => $gong['prod_ru'],
                'in_ru' => $gong['prod_gao'],
            ];
            $brr = [
                'us_id' => $this->user['id'],
                'note'  => "选择工厂".$gong['prod_name'],
                'rec_add_time' => date('Y-m-d H:i:s'),
            ];

            Db::name('in')->where('id',$info['id'])->update($arr); 
            Db::name('user')->where('id',$this->user['id'])->setfield('us_status',1);
            Db::name('in_rec')->insert($brr);
            $this->s_msg('选择工厂成功,请尽快报道');

        }else{
            $this->e_msg('get');
        }
    }

    //入职记录
    public function ru(){
        if(is_post()){
            $this->map[]=['us_id','=',$this->user['id']];
            if(input('size')){
                $this->size = input('size');
            }
            $list = model("InRec")->chaxun($this->map,$this->order,$this->size);
            $this->msg($list);
        }else{
            $this->e_msg('get');
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

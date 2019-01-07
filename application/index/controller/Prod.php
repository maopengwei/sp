<?php

namespace app\index\controller;

use think\Request;
use func\PassN;
use think\Db;
/**
 * 玩家个人
 */
class Prod extends Common
{

    // 城市
    public function area()
    {  
        $list[0] = Db::name('sto_cate')->select(); 
        $list[1] = cache('setting');
        $list[2] = Db::name('sto_gong')->select();
        $list[3] = model('Message')->order('id desc')->find();
        $this->msg($list);
    }
    
    //视频
    // public function video(){
    //     $data['video'] = cache('setting')['vedo_path'];
    //     $this->msg($data);
    // }


    //首页
    public function index(){

        /*
            搜索
            工厂地域
            工厂名称 
            小时工分类
            热销
        */
        $arr = [
            0 => 'prod_sort desc,id desc',
            1 => 'sort_yi desc,id desc',
            2 => 'sort_er desc,id desc',
            3 => 'sort_san desc,id desc',
        ];
        $this->size = 8;
        if(input('prod_name')!=""){
            $this->map[] = ['prod_name','like','%'.input('prod_name').'%'];
        }
        if (is_numeric(input('post.gong'))) {
            $this->map[] = ['prod_gong', 'like', "%".input('gong')."%"];
            $this->order = $arr[input('gong')];
        }else{
            $this->order = $arr[0];
        }
        if (input('post.prod_area')) {
            $this->map[] = ['cate_id', '=', input('prod_area')];
        }
        if(input('is_hot')){
            $this->map[] = ['prod_is_hot','=',1];
        }

        $this->order = 'prod_sort desc,id desc';
        $list = model('StoProd')->chaxun($this->map,$this->order,$this->size);
        $this->msg($list);

    }
    

    public function detail(){
        $id = input('id');
        if($id){
            $info = model("StoProd")->detail(['id'=>input('id')]);
            $info['pic'] = explode(',',$info['prod_pic']);
            $info['prod_describe'] = html_entity_decode($info['prod_describe']);
            // str_replace("world","Shanghai","Hello world!");
        }else{
            $this->e_msg('没传id');
        }
        $this->msg($info);
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
}

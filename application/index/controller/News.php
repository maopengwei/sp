<?php

namespace app\index\controller;
use app\common\controller\Api;
use think\Route;

class News extends Api
{


    public function index() {
        
        $this->map[] = ['me_type','=',1];
        if(input('size')){
            $this->size = input('size');
        }
        $list = model('Message')->where($this->map)->order($this->order)->paginate($this->size);
        $this->msg($list);
    }
    // 修改
    public function xq() {
        $id = input('id');
        $info = model("Message")->get($id);
        $info['me_content'] = html_entity_decode($info['me_content']); 
        $this->msg($info);
    }

    // 公告列表
    public function noticeList()
    {
        // 获取最新公告
    	$list = db('notice')->where('status', 1)->order('create_time DESC')->find();

        $list['n_message'] = html_entity_decode($list['n_message']);

    	return $this->result($list);
    }
     // 公告列表
    public function noticeLists()
    {
        // 获取最新公告
        $list = db('notice')->where('status', 1)->order('create_time DESC')->select();
        foreach ($list as $k => $v) {
            $list[$k]['n_message'] = html_entity_decode($v['n_message']);
        }
        // $list['n_message'] = html_entity_decode($list['n_message']);
        return $this->result($list);
    }

    // 公告info 
    public function getNotice()
    {
    	$id = input('param.id');
    	$info = db('notice')->where('id', $id)->find();
        $info['n_message'] = html_entity_decode($info['n_message']);
    	return $this->result($info);
    }

    public function miss()
    {
    	$this->error('no access');
    }



}
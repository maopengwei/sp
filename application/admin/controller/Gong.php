<?php
namespace app\admin\controller;

/**
 * 不同工种
 */
class Gong extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    // 分类列表
    public function index()
    {
       
        $list = model('StoGong')->select();
        // foreach ($list as $k => $v) {
        //     $list[$k]['son'] = model('StoCate')->where('cate_pid', $v['id'])->select();
        // }
        $count = count($list);
        $this->assign(array(
            'list'=> $list,
            'count'=> $count,
        ));
        return $this->fetch();
    }
    //编辑分类
    public function edit()
    {
        if (is_post()) {
            
            $data = input('post.');
            model('StoGong')->update($data);
            $this->success('修改成功');

        } else {
            $info = model("StoGong")->detail(['id'=>input('id')]);
            $this->assign(array(
                'info' => $info,
            ));
            return $this->fetch();
        }
    }
    //删除分类
    public function del()
    {
        if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('非法操作');
        }
        $info = model('StoCate')->detail(['id'=>$id]);
       
        if ($info) {
            if (model('StoCate')->where('cate_pid', $info['id'])->find()) {
                $this->error('该分类下面有子分类所以不能删除');
            }
            $rel = db('sto_cate')->where('id', $id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('非法操作');
        }
    }
}
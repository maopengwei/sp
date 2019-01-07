<?php
namespace app\admin\controller;

/**
 * 产品分类
 */
class Cate extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    // 分类列表
    public function index()
    {
       
        $list = model('StoCate')->order('cate_sort desc')->select();
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
    //添加分类
    public function add()
    {
        if (is_post()) {
            $data = input('post.');

            /*$file = request()->file('file');
            if($file){
                $base = uploads($file);
                if($base['code']){
                    $data['cate_pic'] = $base['path'];
                }else{
                    return $base;
                }
            }else{
                $this->error('请上传分类图标');
            }*/


            if (!input('cate_name') || model('StoCate')->where('cate_name', input('cate_name'))->count() > 0) {
                $this->error('分类名为空或已有此分类');
            }
           
            $rst = model('StoCate')->tianjia($data);
           return $rst;
        } else {
            $cate = model('StoCate')->where('cate_pid', 0)->select();
            $this->assign('cate', $cate);
            return $this->fetch();
        }
    }
    //编辑分类
    public function edit()
    {
        if (is_post()) {
            
            $data = input('post.');

            $file = request()->file('file');
            if($file){
                $base = uploads($file);
                if($base['code']){
                    $data['cate_pic'] = $base['path'];
                }else{
                    return $base;
                }
            }

            if (!$data['cate_name'] || model('StoCate')->where('cate_name', input('cate_name'))->count() > 1) {
                $this->error('分类名为空或已有此分类');
            }
            if(!is_numeric($data['cate_sort'])){
                 $this->error('排序字段不能为空');
            }
            model('StoCate')->update($data);
            $this->success('修改成功');

        } else {
            $cate = model('StoCate')->where('cate_pid', 0)->select();
            $info = model("StoCate")->detail(['id'=>input('id')]);
            $this->assign(array(
                'cate' => $cate,
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
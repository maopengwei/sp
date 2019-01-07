<?php
namespace app\admin\controller;

/**
 * 属性列表
 */
class Attr extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    //属性列表
    public function index()
    {
        
        if (is_post()) {
            $data = input('post.');

            $rst = model('StoAttr')->where('id',$data['id'])->setfield($data['key'],$data['val']);
            if ($rst) {
                return ['code'=>1];
            }
            return ['code'=>0,'msg'=>'修改失败'];
        }
        $where = [
            'cate_pid' => 0,
        ];
        $cate = model('StoCate')->where($where)->select();
        foreach ($cate as $k => $v) {
            $cate[$k]['son'] = model('StoCate')->where('cate_pid',$v['id'])->select();
        }

        if (input('attr_name')) {
            $this->map[] = ['attr_name','=',input('attr_name')];
        }else{
            $this->map[] = ['attr_pid','=',0];
        }
        
        if (is_numeric(input('cate_id'))) {

            $list = model('StoCate')->where('cate_pid', input('cate_id'))->field('id')->select();
            $arr = [(int) input('get.cate_id')];
            foreach ($list as $k => $v) {
                array_push($arr, $v["id"]);
            }
            $this->map[] =['cate_id','in',$arr];
        }
        
        // if(!$this->map){
        //    $this->map[] = ['attr_pid','=',0];
        // }
        $list = model('StoAttr')->chaxun($this->map,$this->order,$this->size);
        foreach ($list as $k => $v) {
            $list[$k]['son'] = model('StoAttr')->where('attr_pid', $v['id'])->select();
        }
        $this->assign(array(
            'list' => $list,
            'cate' => $cate,
        ));
        return $this->fetch();
    }
    //添加属性
    public function add()
    {
        if (is_post()) {
            $data = input('post.');

            //属性分类
            if ($data['cate_id'] == 0) {
                $this->error('请选择商品分类');
            }
            //属性名
            if ($data['attr_name'] == '') {
                $this->error('属性名不能为空');
            }

            if (model('StoAttr')->where($data)->count() > 0) {
                $this->error('此分类下已有该属性名');
            }
            $rst = model('StoAttr')->tianjia($data);
                
            $this->success('添加成功');

        } else {
            // $map = array(
            //     'pid' => 0,
            // );
            $cate = model('StoCate')->where('cate_pid',0)->select();
            foreach ($cate as $k => $v) {
                $cate[$k]['son'] = model('StoCate')->where('cate_pid', $v['id'])->select();
            }
            if (input('cate_id')) {
                $this->map[] = ['cate_id','=',input('cate_id')];
            }
            $this->map[] = ['attr_pid','=', 0];
            $attr = model('StoAttr')->where($this->map)->select();
            $this->assign(array(
                'cate' => $cate,
                'attr' => $attr,
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
        $info = model('StoAttr')->where('id', $id)->find();
        
        // $product = model('product')->where('cate_id', $info['id'])->find();
        // if ($product) {
        //     $this->error('该分类下面有产品,请修改产品分类后再删除');
        // }
        if ($info) {
            if (model('StoAttr')->where('attr_pid', $info['id'])->find()) {
                $this->error('该属性名下面有属性值所以不能删除');
            }
            $rel = db('StoAttr')->where('id', $id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('该数据不存在');
        }
    }
}
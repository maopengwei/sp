<?php
namespace app\admin\controller;

use think\Container;

/**
 * 商家
 */
class Mer extends Common {





	/*-----------------申请*/

	public function apply(){

		if(is_post()){
			$data = input('post.');
			$info = model('StoApply')->detail(['id'=>$data['id']]);
			model("StoApply")->update($data);
			if($data['apply_status']==1){
				model("User")->where('id',$info['us_id'])->setfield('us_is_mer',1);
				$data  = [
					'us_id'=>$info['us_id'],
					'apply_name'=>$info['apply_name'],
				];
				model('StoMer')->tianjia($data);
				$this->success('审核通过');
			}else{
				$this->success('已被驳回');
			}
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel|us_real_name', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (is_numeric(input('get.status'))) {
			$this->map[] = ['apply_status', '=', input('get.status')];
		}
		$list = model('StoApply')->chaxun($this->map, $this->order, $this->size);

		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();


	}
	public function apply_edit() {
		
		$info = model('StoApply')->detail(['id' => input('get.id')]);
		
		$this->assign(array(
			
			'info' => $info,
		
		));
		return $this->fetch();
	
	}
	public function apply_del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoApply')->get($id);
        if ($info) {
            $rel = model('StoApply')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}


	/*--------------------------商家*/
	public function index() {
		if (is_post()) {

			$rst = model('Store')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;

		}

		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel|us_real_name', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}

		if (is_numeric(input('get.status'))) {
			$this->map[] = ['mer_status', '=', input('get.status')];
		}
		if (input('get.mer_name')) {
			$this->map[] = ['mer_name', 'like', '%'.input('get.mer_name')."%"];
		}


		$list = model('StoMer')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();

	}

	public function add() {
		if (is_post()) {
			$data = input('post.');

			$validate = validate('Front');
			$res = $validate->scene('addmer')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$user = Model("User")->get($data['us_id']);
			if(!$user || $user['us_is_mer']){
				return ['code'=>0,'msg'=>'该用户不存在或已经是商家了'];
			}
			$rel = model('StoMer')->tianjia($data);
			if($rel['code']){
				model("User")->where('id',$data['us_id'])->setfield('us_is_mer',1);
			}
			return $rel;
		}
		return $this->fetch();
	}

	public function edit() {

		if (is_post()) {
			$data = input('post.');
			$validate = validate('front');
			$rst = $validate->scene('addmer')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			
			$rel = model('StoMer')->update($data);
			return ['code'=>1,'msg'=>'修改成功'];
		}
		$info = model('StoMer')->detail(['id'=>input('get.id')]);
		
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}

	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoMer')->get($id);
        if ($info) {
        	model("User")->where('id',$info['us_id'])->setfield('us_is_mer',0);
            $rel = model('StoMer')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}

	//门店定位
	public function positioning() {
		if (is_post()) {
			$data = input("post.");
			$validate = validate('Verify');
			$rst = $validate->scene('editTude')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model('Store')->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}
	public function position() {

	}
	

	//分类列表
	public function cate() {
		if (is_post()) {
			$rst = model('Cate')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
		}
		if (input('get.keywords')) {
			$id = model('Store')->where('st_serial_number|st_name', trim(input('get.keywords')))->value('id');
			if (!$id) {
				$id = 0;
			}
			$this->map[] = ['st_id', '=', $id];
		}
		if (input('get.ca_name')) {
			$this->map[] = ['ca_name', '=', trim(input('get.ca_name'))];
		}
		if (is_numeric(input('get.status'))) {
			$this->map[] = ['ca_status', '=', input('get.status')];
		}
		$list = model('Cate')->chaxun($this->map, $this->order, $this->size);
		foreach ($list as $k => $v) {
			$info = model("Store")->get($v['st_id']);
			$list[$k]['st_serial_number'] = $info['st_serial_number'];
			$list[$k]['st_name'] = $info['st_name'];
		}
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
	//添加分类
	public function cate_add() {
		if (is_post()) {
			$data = input('post.');
			$validate = validate('Verify');
			$res = $validate->scene('addCate')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Cate')->tianjia($data);
			if ($rel) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
		}
		return $this->fetch();
	}
	//获取店铺信息
	public function get_store() {
		$info = model("Store")->where('st_serial_number', input('post.st_serial_number'))->find();
		if ($info) {
			return $data = [
				'code' => 1,
				'data' => $info,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}
	public function get_cate() {
		$list = model('Cate')->where('st_id', input('post.id'))->select();

		if (count($list)) {
			return $data = [
				'code' => 1,
				'data' => $list,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}


	/*--查询用户*/
	public function get_us(){
		$info = model("User")->where('us_account',input('us_account'))->find();

		if($info){
			if($info['us_is_mer']){
				return ['code'=>2,'msg'=>'该用户已经是商家了'];
			}
			return ['code'=>1,'data'=>$info];
		}else{
			return ['code'=>0];
		}
	}
}

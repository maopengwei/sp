<?php
namespace app\admin\controller;

use think\Container;

/**
 * @todo
 */
class Store extends Common {

	// ------------------------------------------------------------------------
	// 门店列表
	public function index() {
		if (is_post()) {

			$rst = model('Store')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$this->map[] = ['us_tel', 'like', '%' . input('get.keywords') . '%'];
		}
		if (is_numeric(input('get.ad_status'))) {
			$this->map[] = ['us_status', '=', input('get.us_status')];
		}
		$list = model('Store')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	//门店添加
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$bb = input('?post.st_label');
			if (!$bb) {
				$data['st_label'] = "";
			}
			array_shift($data['st_pic']);
			$validate = validate('Verify');
			$res = $validate->scene('addStore')->check($data);

			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Store')->tianjia($data);
			return $rel;
		}
		$label = unserialize(cache('setting')['label']);
		$label = array_column($label, 'name');
		$this->assign('label', $label);
		return $this->fetch();
	}

	//门店修改
	public function edit() {
		if (is_post()) {
			$data = input('post.');
			unset($data['id']);
			$validate = validate('Verify');
			$rst = $validate->scene('editStore')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			if ($data['st_pwd']) {
				$data['st_pwd'] = encrypt($data['st_pwd']);
			} else {
				unset($data['st_pwd']);
			}
			if ($data['st_safe_pwd']) {
				$data['st_safe_pwd'] = encrypt($data['st_safe_pwd']);
			} else {
				unset($data['st_safe_pwd']);
			}

			$data['st_pic'] = implode(",", $data['st_pic']);
			$data['st_label'] = implode(",", $data['st_label']);
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
			return $rel;
		}
		$info = model('Store')->get(input('get.id'));
		$info['st_pic'] = explode(',', $info['st_pic']);
		$label = unserialize(cache('setting')['label']);
		$label = array_column($label, 'name');
		$this->assign(array(
			'info' => $info,
			'label' => $label,
		));
		return $this->fetch();
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
	//上传图片
	public function upload() {

		$bb = Container::get('env')->get('ROOT_PATH');
		$file = request()->file('file');
		$info = $file->validate(['size' => '4096000'])
			->move($bb . 'public/uploads/');
		if ($info) {
			$path = '/uploads/' . $info->getsavename();
			$path = str_replace('\\', '/', $path);
			return $data = array(
				'code' => 1,
				'msg' => '上传成功',
				'data' => $path,
			);
		} else {
			return $data = array(
				'msg' => $file->getError(),
				'code' => 0,
			);
		}
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
}

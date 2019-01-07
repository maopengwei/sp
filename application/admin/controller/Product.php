<?php
namespace app\admin\controller;

/**
 * 商品
 */
class Product extends Common {

	public function __construct() {
		parent::__construct();
	}
	// 商品列表
	public function index() {
		if (is_post()) {
			$rst = model('Product')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error("修改失败");
			}
		}
		if (input('get.keywords')) {
			$id = model("Store")->where('st_serial_number|st_name', trim(input('get.keywords')))->value('id');
			if ($id) {
				$this->map[] = ['st_id', '=', $id];
			}
		}
		if (is_numeric(input('get.pd_status'))) {
			$this->map[] = ['pd_status', '=', input('get.pd_status')];
		}
		if (input('get.pd_name') != "") {
			$this->map[] = ['pd_name', 'like', "%" . trim(input('get.pd_name')) . "%"];
		}
		$list = model('Product')->chaxun($this->map, $this->order, $this->size);
		foreach ($list as $k => $v) {
			$store = model("Store")->get($v['st_id']);
			$cate = model("Cate")->get($v['ca_id']);
			$list[$k]['st_name'] = $store['st_name'];
			$list[$k]['ca_name'] = $cate['ca_name'];
		}
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
	//添加商品
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$validate = validate('Verify');
			$res = $validate->scene('addPd')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Product')->tianjia($data);
			if ($rel) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
		}
		$this->assign(array(
			'list' => model("Store")->select(),
		));
		return $this->fetch();
	}
	public function edit() {
		$info = model("Product")->get(input('id'));
		if (is_post()) {
			$data = input('post.');
			$id = $data['id'];
			unset($data['id']);
			$validate = validate('Verify');
			$res = $validate->scene('editPd')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Product')->xiugai($data, ['id' => $id]);
			if ($rel) {
				$this->success('保存成功');
			} else {
				$this->error('您并没有做出修改');
			}
		}
		$list = model("Cate")->where('st_id', $info['st_id'])->select();
		$name = model("Store")->where('id', $info['st_id'])->value('st_name');
		$this->assign(array(
			'info' => $info,
			'list' => $list,
			'name' => $name,
		));
		return $this->fetch();
	}
}

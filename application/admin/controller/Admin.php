<?php
namespace app\admin\controller;

/**
 * @todo 管理员、角色、权限管理
 */
class Admin extends Common {
	// 管理员列表
	public function index() {
		if (is_post()) {
			$rst = model('Admin')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			// $this->map[] = ['ad_tel|ad_account|ad_work_number', 'like', '%' . input('get.keywords') . '%'];
			$this->map[] = ['ad_tel|ad_account|ad_work_number', '=', input('get.keywords')];
		}
		if (is_numeric(input('get.ad_status'))) {
			$this->map[] = ['ad_status', '=', input('get.ad_status')];
		}
		$list = model('Admin')->chaxun($this->map, $this->order, $this->size);

		$this->assign(array(
			'ro_list' => db('role')->select(),
			'list' => $list,
		));
		return $this->fetch();
	}
	//添加
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$validate = validate('Verify');
			$res = $validate->scene('addAdmin')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('Admin')->tianjia($data);
			return $rel;
		}
		$this->assign('ro_list', db('role')->select());
		return $this->fetch();
	}
	public function edit() {
		if (is_post()) {
			$validate = validate('Verify');
			$res = $validate->scene('editAdmin')->check(input('post.'));
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('admin')->xiugai(input('post.'), ['id' => input('post.id')]);
			return $rel;
		}
		$this->assign(array(
			'info' => model('Admin')->get(input('get.id')),
			'ro_list' => db('role')->select(),
		));
		return $this->fetch();
	}

	//角色
	public function roleIndex() {
		if (is_post()) {
			$data = input('post.');
			sort($data['rules']);
			$data['ro_rules'] = implode(',', array_unique($data['rules']));
			unset($data['rules']);
			if ($data['id']) {
				$rst = db('Role')->update($data);
			} else {
				$rst = db('Role')->insert($data);
			}
			if ($rst) {
				$this->success('操作完成');
			}
			$this->error('操作失败，稍后重试');
		} else {
			$this->assign('list', db('role')->select());
			return $this->fetch();
		}
	}
	// 添加角色
	public function roleAdd() {
		$list = db('rule')->where('pid', 0)->select();
		foreach ($list as $k => $v) {
			$list[$k]['child'] = db('rule')->where('pid', $v['id'])->select();
		}
		$this->assign('ru_list', $list);
		if (input('get.id')) {
			$this->assign('info', db('role')->where('id', input('get.id'))->find());
		} else {
			$this->assign('info', ['ro_rules' => '1,2,3,4']);
		}
		return $this->fetch();

	}

	// 权限节点列表
	// public function authRule()
	// {
	//     if (is_post()) {
	//         if (db('adminRule')->insert(input('post.'))) {
	//             $this->success('添加成功');
	//         }
	//         $this->error('操作失败，请稍后重试');
	//     }
	//     $this->assign('list', AdminRule::ruleList());
	//     return $this->fetch();
	// }
	// public function person()
	// {
	//     $info = model('Admin')->get(session('mid'));
	//     $this->assign('info', $info);
	//     return $this->fetch();
	// }
}

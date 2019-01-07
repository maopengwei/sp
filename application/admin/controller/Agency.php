<?php
namespace app\admin\controller;

/**
 * @todo 区域代理列表
 */
class Agency extends Common {

	// ------------------------------------------------------------------------
	//区代理列表
	public function index() {
		if (is_post()) {
			$rst = model('User')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$this->map[] = ['us_tel|us_account|us_real_name', '=', input('get.keywords')];
		}
		if (is_numeric(input('get.us_status'))) {
			$this->map[] = ['us_status', '=', input('get.us_status')];
		}
		if (input('get.a') == 1) {
			$list = model("User")->where($this->map)->select();
			// $url = action('Excel/user', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\user.xlsx";
			if (file_exists($bb)) {
				$aa = unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1', '账户名')
				->setCellValue('B1', '真实姓名')
				->setCellValue('C1', '电话号码')
				->setCellValue('D1', '购物币')
				->setCellValue('E1', '佣金')
				->setCellValue('F1', '添加时间');
			$i = 2;
			foreach ($list as $k => $v) {
				$sheet->setCellValue('A' . $i, $v['us_account'])
					->setCellValue('B' . $i, $v['us_real_name'])
					->setCellValue('C' . $i, $v['us_tel'])
					->setCellValue('D' . $i, $v['us_wallet'])
					->setCellValue('E' . $i, $v['us_msc'])
					->setCellValue('F' . $i, $v['us_add_time']);
				$i++;
			}
			$writer = new Xlsx($spreadsheet);
			$writer->save('user.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/user.xlsx";
		}

		$list = model('Agency')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	//添加
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$validate = validate('Verify');
			$res = $validate->scene('addUser')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$pinf = model("User")->where('us_tel', $data['ptel'])->find();
			if (count($pinf)) {
				$data['us_pid'] = $pinf['id'];
				$data['us_path'] = $pinf['us_path'] . ',' . $pinf['id'];
				$data['us_path_long'] = $pinf['us_path_long'] + 1;
			} else {
				$data['us_pid'] = 0;
				$data['us_path'] = 0;
				$data['us_path_long'] = 0;
			}
			$rel = model('User')->tianjia($data);
			return $rel;
		}
		return $this->fetch();
	}

	//节点图
	public function tupu() {
		if (is_post()) {
			$info = model('User')->where('us_account', input('post.us_account'))->field('id,us_path,us_pid,us_account,us_tel')->find();
			if (!$info) {
				return [
					'code' => 0,
					'msg' => "查无此人",
				];
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
			return [
				'code' => 1,
				'data' => $znote,
			];
		}
		return $this->fetch();
	}
}

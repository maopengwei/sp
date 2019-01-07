<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
/**
 * @todo 会员管理 查看，状态变更，密码重置
 */
class User extends Common {

	// ------------------------------------------------------------------------
	//用户列表
	public function index() {
		if ($this->request->isPost()) {
			$rst = model('User')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$this->map[] = ['us_tel|us_account|us_real_name', '=', input('get.keywords')];
		}
		if (is_numeric(input('get.us_status'))) {
			$this->map[] = ['us_status', '=', input('get.us_status')];
		}
		if (is_numeric(input('get.us_is_jing'))) {
			$this->map[] = ['us_is_jing', '=', input('get.us_is_jing')];
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

		$list = model('User')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'yuming' => $_SERVER['HTTP_HOST'],
			'list' => $list,
		));

		return $this->fetch();
	}
	//添加
	public function add() {
		if (is_post()) {
			$da = input('post.');

			//验证器
			$validate = validate('Admin');
			$res = $validate->scene('addUser')->check($da);
			if (!$res) {
				$this->error($validate->getError());
			}
			//父账号
			if ($da['p_acc']) {
				$pinfo = model("User")->where('us_account', $da['p_acc'])->find();
				if (count($pinfo)) {
					$da['us_pid'] = $pinfo['id'];
					$da['us_path'] = $pinfo['us_path'] . ',' . $pinfo['id'];
					$da['us_path_long'] = $pinfo['us_path_long'] + 1;
				} else {
					$this->error('推荐人不存在');
				}
			} else {
				$da['us_pid'] = 0;
				$da['us_path'] = 0;
				$da['us_path_long'] = 0;
			}
			
			$rel = model('User')->tianjia($da);
			return $rel;

		}
		return $this->fetch();

	}
	//看
	public function kan(){
		$this->assign('info', model('User')->get(input('id')));
		return $this->fetch();
	}
	//修改
	public function edit() {
		if (is_post()) {
			$da = input('post.');
			if(isset($da['p_acc']) && $da['p_acc']!=''){
				$info = Db::name('user')->where('us_account',$da['p_acc'])->find();
				if($info){
					if($info['id']== $da['id']){
						$this->error('父账号不能是您自己');
					}

					$da['us_pid'] = $info['id'];
					$da['us_path'] = $info['us_path'].','.$info['id'];
					$da['us_path_long'] = $info['us_path_long']+1;


				}else{
					$this->error('父账号不存在');
				}
			}
			$rel = model('User')->homeInfo($da);
			return $rel;
		}else{
			$this->assign('info', model('User')->get(input('id')));
			return $this->fetch();
		}
	}
	//添加代理
	public function agency(){
		if(is_post()){
			$d = input('post.');

			$info = Db::name("user")->where('us_account',$d['us_account'])->where('us_tel',$d['us_tel'])->find();
			if(!$info){
				$this->error('该用户不存在');
			}	
			if($info['us_is_agency']==1){
				$this->error('该用户已经是区域代理了');
			}
			if($info['us_status']==0){
				$this->error('该用户已经被禁用了');
			}
			$arr = [
				'us_id' => $info['id'],
				'age_area' => $d['age_area'],
				'age_tel' => $d['age_tel'],
				'age_name' => $d['age_name'],
				'age_pic' => $d['age_pic'],
				'age_add_time' => date('Y-m-d H:i:s'),
			];
			$rel = Db::name('agency')->insert($arr);
			if($rel){
				Db::name('user')->where('id',$info['id'])->setfield('us_is_agency',1);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}

		}else{
			return $this->fetch();
		}
	}
	
	//修改代理
	public function agen(){
		if(is_post()){
			$d = input('post.');

			Db::name('agency')->update($d);

			$this->success('修改成功');

		}else{

			$info = model('Agency')->with('user')->where('us_id',input('id'))->find();
			
			$this->assign(array(
				'info' => $info,
			));

			return $this->fetch();
		}
	}

	//代理删除
	public function age_del() {
		if (input('post.id')) {
			$id = input('post.id');
		} else {
			$this->error('id不存在');
		}
		$info = Db::name('agency')->where('id',$id)->get($id);
		if ($info) {
			$rel = Db::name('agency')->where('id',$id)->delete();
			if ($rel) {
				Db::name('user')->where('id',$info['us_id'])->setfield('us_is_agency',0);
				$this->success('删除成功');
			} else {

				$this->error('请联系网站管理员');
			}
		} else {
			$this->error('数据不存在');
		}
	}

	/*---------送币*/
	public function song(){
		if(is_post()){
			$da = input('post.');
			$rel = model("User")->songbi($da);
			return $rel;
		}else{
			$this->assign('info', model('User')->get(input('id')));
			return $this->fetch();
		}
	}

	public function shen(){
		if(is_post()){
			$d = input('');
			$rel = Db::name('user')->where('id',$d['id'])->setfield('us_is_shen',$d['us_is_shen']);
			if($rel){
				$this->success('修改成功');	
			} else {
				$this->error('修改失败');
			}
		}
	}
	
	//用户删除
	public function del() {
		if (input('post.id')) {
			$id = input('post.id');
		} else {
			$this->error('id不存在');
		}
		$info = model('User')->get($id);
		if ($info) {

			$time = unixtime('day',-1);
			$day = date('Y-m-d H:i:s',$time);
				//  

			// $child1 = model("User")->where('us_pid',$info['id'])->find();
			// if(count($child1)){
			// 	$this->error('该用户已推荐别的会员');
			// }
			
			$rel = db('user')->where('id',$id)->delete();
			if ($rel) {
				$this->success('删除成功');
			} else {

				$this->error('请联系网站管理员');
			}
		} else {
			$this->error('数据不存在');
		}
	}

	//入职记录
	public function ru(){
		$this->map[]=['us_id','=',input('id')];
       
        $list = model("InRec")->chaxun($this->map,$this->order,$this->size);
        $this->assign('list',$list);
		return $this->fetch();

	}

	//团队
	public function team() {
		if (is_post()) {
			$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->field('id,us_path,us_pid,us_account,us_tel')->find();
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
		if(input('get.id')){
			$this->assign('us_account',input('get.id'));
		}
		return $this->fetch();
	}

	//地址列表
	public function addr() {
		if (is_post()) {
			$rst = model('User_addr')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
			return $rst;
		}
		if (input('get.id')) {
			$this->map[] = ['us_id', '=', input('get.id')];
		} else {
			$this->error("非法操作");
		}
		$list = model('User_addr')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
			'name' => model('User')->where('id', input('get.id'))->value('us_account'),
		));
		return $this->fetch();
	}

	//地址修改
	public function addr_edit() {
		if (is_post()) {
			$data = input("post.");
			$validate = validate('Verify');
			$rst = $validate->scene('editAddr')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			unset($data['id']);
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model("User_addr")->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}

	public function tupu() {
		if (is_post()) {
			if(input('post.us_account') ==1){
				$info = model("User")->detail(1);
			}else{
				$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->find();
				if (!$info) {
					return [
						'code' => 0,
						'msg' => '该用户不存在',
					];
				}
			}
			$level = input('level');

			$znote = [];
			$this->map[] = ['us_path', 'like', $info['us_path'] . "," . $info['id'] . "%"];
			$this->map[] = ['us_path_long', '<=', $info['us_path_long'] + 2];
			$list = db('user')->where($this->map)->select();
			array_push($list, $info);
			foreach ($list as $k => $v) {
				$bb = model('User')->dir_count($v['id']);
				$cc = model('User')->team_count($v['id'],$v['us_path']);
				$znote[$k]['name'] = $v['us_account'];
				$znote[$k]['tel'] = $v['us_tel'] . "(" . $v['us_real_name'] . ")";
				$znote[$k]['zuo'] = "CRC:".$v['us_wal'];
				$znote[$k]['you'] = "直推人数:".$bb.',团队人数:'.$cc;
				// $znote[$k]['level'] ="123456";
				$znote[$k]['key'] = $v['id'];
				$znote[$k]['parent'] = $v['us_pid'];
				$znote[$k]['source'] = $v['us_head_pic'];
			}
			return [
				'code' => 1,
				'data' => $znote,
				'ptel' =>$info['ptel'],
			];
		} else {
			$this->assign(array(
				'us_account' => model('User')->where('id',input('id')?:0)->value('us_account')?:1,
			));
			return $this->fetch();
		}
	}

	

	
	protected function scerweima($url = '', $logo = '') {
		require_once __DIR__ . '\qrcode.php';
		$value = $url; //二维码内容
		$errorCorrectionLevel = 'H'; //容错级别
		$matrixPointSize = 7; //生成图片大小
		//生成二维码图片
		$path = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		$filename = $_SERVER['DOCUMENT_ROOT'] . $path;
		\QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		// $logo = $_SERVER['DOCUMENT_ROOT'] . '/static/admin/img/tou.jpg'; //准备好的logo图片
		$QR = $filename; //已经生成的原始二维码图
		if (file_exists($logo)) {
			$QR = imagecreatefromstring(file_get_contents($QR)); //目标图象连接资源。
			$logo = imagecreatefromstring(file_get_contents($logo)); //源图象连接资源。
			$QR_width = imagesx($QR); //二维码图片宽度
			$QR_height = imagesy($QR); //二维码图片高度
			$logo_width = imagesx($logo); //logo图片宽度
			$logo_height = imagesx($logo); //logo图片高度
			$logo_qr_width = $QR_width / 4; //组合之后logo的宽度(占二维码的1/5)
			$scale = $logo_width / $logo_qr_width; //logo的宽度缩放比(本身宽度/组合后的宽度)
			$logo_qr_height = $logo_height / $scale; //组合之后logo的高度
			$from_width = ($QR_width - $logo_qr_width) / 2; //组合之后logo左上角所在坐标点
			//重新组合图片并调整大小
			/*
	         *  imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
			*/
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		// header('Content-Type: image/png');
		//输出图片
		$path1 = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		imagepng($QR, $_SERVER['DOCUMENT_ROOT'] . $path1);
		imagedestroy($QR);
		imagedestroy($logo);
		return $path1;
	}
}

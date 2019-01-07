<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
/**
 * 商品
 */
class Prod extends Common {

	public function __construct() {
		parent::__construct();
		$gong = Db::name('sto_gong')->select();
		$cate = Db::name('sto_cate')->select();
		$this->assign(array(
			'cate' => $cate,
			'gong' => $gong,
		));


	}
	/*------------------商品*/
	public function index() {
		if (is_post()) {
			$rst = model('StoProd')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error("修改失败");
			}
		}
		
		if (is_numeric(input('get.status'))) {
			$this->map[] = ['prod_status', '=', input('get.status')];
		}

		if (is_numeric(input('get.gong'))) {
			$this->map[] = ['prod_gong', 'like', "%".input('get.gong')."%"];
		}
		if (is_numeric(input('get.area'))) {
			$this->map[] = ['cate_id', '=', input('get.area')];
		}
		
		if (input('get.prod_name') != "") {
			$this->map[] = ['prod_name', 'like', "%" . trim(input('get.prod_name')) . "%"];
		}
		if (input('get.order') != "") {
			$this->order = input('get.order').' desc,id desc';
		}else{
			$this->order = 'prod_sort desc,id desc';
		}

		
		$list = model('StoProd')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
			'cate' => model('StoCate')->select(),
		));
		return $this->fetch();
	}
	
	public function add() {
		
		if (is_post()) {
			$d = input('post.');
			$file = request()->file('file');
			if($file){
				$base = uploads($file);
				if($base['code']){
					$d['prod_lpic'] = $base['path'];
				}else{
					return $base;
				}
			}
			//验证器
			$validate = validate('Hou');
			$res = $validate->scene('addprod')->check($d);
			if (!$res) {
				$this->error($validate->getError());
			}
			$rel = model('StoProd')->tianjia($d);
			return $rel;
		}else{
			$cate = model('StoCate')->select();
			// foreach ($cate as $k => $v) {
			// 	$cate[$k]['son'] = model('StoCate')->where('cate_pid', $v['id'])->select();
			// }
			
			$this->assign(array(
				'cate' => $cate,
				// 'province' => db('addr_province')->select(),
				// 'city' => db('addr_city')->select(),
				// 'area' => db('addr_area')->select(),
			));
			return $this->fetch();
		}
	}
	public function edit() {
		
		if (is_post()) {
			$data = input('post.');
			if(!key_exists('prod_pic',$data)){
				$this->error('至少上传一张图片');
			}else{
				$data['prod_pic'] = implode(',',$data['prod_pic']);
			}
			if(!key_exists('prod_gong',$data)){
				$this->error('至少招收一个工种');
			}else{
				$data['prod_gong'] = implode(',',$data['prod_gong']);
			}

			// $validate = validate('Admin');
			// $res = $validate->scene('editprod')->check($data);
			// if (!$res) {
			// 	$this->error($validate->getError());
			// }
			$rel = model('StoProd')->update($data);
			if ($rel) {
				$this->success('保存成功');
			} else {
				$this->error('您并没有做出修改');
			}
		}else{
			$info = model("StoProd")->get(input('id'));
			$info['cate'] = Db::name('sto_cate')->where('id',$info['cate_id'])->find();
			
			$info['prod_pic'] = explode(',', $info['prod_pic']);
			$this->assign(array(
				'info' => $info,
			));
			return $this->fetch();
		}
	}
	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoProd')->get($id);
        if ($info) {
            $rel = model('StoProd')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}

	/*----------------------商品属性*/
	public function attr(){
		if(is_post()){
			$data = input('post.');
			if(!$data['attr_id']){
				$this->error('请选择属性名');
			}
			$attr = model("StoAttr")->detail(['id'=>$data['attr_id']]);
			$arr = [
				'attr_pid' => $attr['attr_pid'],
				'attr_id' => $data['attr_id'],
				'prod_id' => $data['prod_id'],
			];
			$rel = model('StoProdAttr')->tianjia($arr);
			return $rel;
		}
		$id = input('get.id');
		$prod = model('StoProd')->get($id);
		$attr = model('StoAttr')->where('cate_id',$prod['cate_id'])->where('attr_pid',0)->select();
		foreach ($attr as $k => $v) {
			$attr[$k]['son'] = model('StoAttr')->where('attr_pid',$v['id'])->select();
		}
		$this->order = 'attr_pid';
		$list = model('StoProdAttr')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'attr' => $attr,
			'list' => $list,
			'prod_id' => $id,
		));

		return $this->fetch();
	
	}

	//
	public function attr_del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoProdAttr')->get($id);
        if ($info) {
            $rel = db('sto_prod_attr')->where('id',$id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}

	public function getcity()
    {
        $province = input('post.code');
        $list = db('addr_city')->where('provincecode', $province)->select();
        if ($list) {
            return $list;
        }
    }
    public function getarea()
    {
        $city = input('post.code');
        $list = db('addr_area')->where('citycode', $city)->select();
        if ($list) {
            return $list;
        }
	}

	public function worker(){
		$this->map[] = ['prod_id','=',input('get.id')];

		if(input('zt')!=''){
			$this->map[] = ['in_zt','=',input('zt')];
		}

		if (input('get.a') == 1) {
			$list = model("In")->where($this->map)->select();
			// $url = action('Excel/user', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\in.xlsx";
			if (file_exists($bb)) {
				$aa = unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', '账户名')
				->setCellValue('B1', '真实姓名')
				->setCellValue('C1', '手机号')
				->setCellValue('D1', '身份状态')
				->setCellValue('E1', '注册时间')
				->setCellValue('F1', '选厂时间')
				->setCellValue('G1', '报道时间')
				->setCellValue('H1', '入职时间')
				->setCellValue('I1', '满工时间');
			$i = 2;
			foreach ($list as $k => $v) {


				$sheet->setCellValue('A' . $i, $v->user['us_account'])
					->setCellValue('B' . $i, $v->user['us_real_name'])
					->setCellValue('C' . $i, $v->user['us_tel'])
					->setCellValue('D' . $i, $v['zt_text'])
					->setCellValue('E' . $i, $v['in_add_time'])
					->setCellValue('F' . $i, $v['in_ij_time'])
					->setCellValue('G' . $i, $v['in_bd_time'])
					->setCellValue('H' . $i, $v['in_ru_time'])
					->setCellValue('I' . $i, $v['in_man_time']);
				$i++;
			}
			$writer = new Xlsx($spreadsheet);
			$writer->save('in.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/in.xlsx";
		}

		// dump(input('id'));
		// $field = 'id,us_account,us_status,us_real_name,us_tel,us_zt';
		$list = model('In')->chaxun($this->map, $this->order, $this->size);




		// dump($list);
		$this->assign(array(
			'prod_id' => input('get.id'),
			'list' => $list,
		));

		return $this->fetch();
	}
	//报到检测
	public function is_bao(){
		$d = input('dd');
		if(!$d){
			$this->error("请选择用户");
		}
		$arr = explode(',',$d);
		foreach($arr as $v){
			$info = model('In')->with('user')->where('id',$v)->find();
			if(!$info){
				$this->error('有会员不存在');
			}
			if($info['in_zt']!=1){
				$this->error($info->user['us_account'].'用户不是未报道状态');
			}

		}
		$this->success('没问题');
	}
	//报到
	public function bao(){

		if(is_post()){

			$d = input('post.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);
				$brr = [
					'in_zt' => 2,
					'in_bd_time' => date('Y-m-d H:i:s'),
				];
				$crr = [
					'us_zt' => 2,
				];
				if($d['agen']){
					$crr['us_agency'] = $d['agen'];
				}
				$drr = [
					'note'  => "工厂报道了",
                	'rec_add_time' => date('Y-m-d H:i:s'),
				];
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					$brr['id'] = $v;
					$crr['id'] = $inf->user['id'];
					$drr['us_id'] = $inf->user['id'];
					Db::name('in')->update($brr);
					Db::name('user')->update($crr);
					Db::name('in_rec')->insert($drr);
				}
				$this->success('报道成功');
			}else{
				$this->error('请传入用户');
			}
		}else{

			$d = input('get.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					array_push($list,$inf);
				}
				$agen = Db::name('user')->where('us_is_agency',1)->where('us_status',1)->field('id,us_account')->select();
				$this->assign(array(
					'list' => $list,
					'agen' => $agen,
					'id' => $d['id'],
				));
				return $this->fetch();
			}else{
				$this->error('请选择用户');
			}

		}
	}
	//入职
	public function is_ru(){

		if(is_post()){

			$d = input('post.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);

				foreach($arr as $v){
					$info = model('In')->with('user')->where('id',$v)->find();
					if(!$info){
						$this->error('有会员不存在');
					}
					if($info['in_zt']!=2){
						$this->error($info->user['us_account'].'用户不是未入职状态');
					}
				}

				
				$brr = [
					'in_zt' => 3,
					'in_ru_time' => date('Y-m-d H:i:s'),
				];
				$crr = [
					'us_zt' => 3,
				];
				$drr = [
					'note'  => "已经入职了",
                	'rec_add_time' => date('Y-m-d H:i:s'),
				];
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					$brr['id'] = $v;
					$crr['id'] = $inf->user['id'];
					$drr['us_id'] = $inf->user['id'];
					Db::name('in')->update($brr);
					Db::name('user')->update($crr);
					Db::name('in_rec')->insert($drr);
				}
				$this->success('入职成功');
			}else{
				$this->error('请传入用户');
			}
		}else{
			$this->error('get');
		}
	}
	//淘汰
	public function is_out(){
		if(is_post()){
			$d = input('post.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);
				foreach($arr as $v){
					$info = model('In')->with('user')->where('id',$v)->find();
					if(!$info){
						$this->error('有会员不存在');
					}
					if($info['in_zt']==4){
						$this->error($info->user['us_account'].'用户已满工');
					}
				}
				$brr = [
					'in_zt' => 5,
					'in_out_time' => date('Y-m-d H:i:s'),
				];
				$crr = [
					'us_zt' => 5,
				];
				$drr = [
					'note'  => "被工厂".$info['prod_text'].'淘汰了',
                	'rec_add_time' => date('Y-m-d H:i:s'),
				];
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					$brr['id'] = $v;
					$crr['id'] = $inf->user['id'];
					$drr['us_id'] = $inf->user['id'];
					Db::name('in')->update($brr);
					Db::name('user')->update($crr);
					Db::name('in_rec')->insert($drr);
				}
				$this->success('淘汰了');
			}else{
				$this->error('请传入用户');
			}
		}else{
			$this->error('get');
		}
	}
	//换厂检测
	public function is_cha(){
		if(is_post()){

			$d = input('post.');
			if($d['id']){
				$arr = explode(',',$d['id']);
				foreach ($arr as $v) {
					$info = model('In')->with('user')->where('id',$v)->find();
					if(!$info){
						$this->error('有会员不存在');
					}
					if($info['in_zt']!=5){
						$this->error($info->user['us_account'].'用户不是被淘汰状态');
					}
				}
				$this->success('成功');
			}else{
				$this->error('请传入用户');
			}
		}else{
			$this->error('get');
		}
	}
	//换厂
	public function cha(){
		if(is_post()){
			$d = input('post.');

			if(!$d['prod_id']){
				$this->error('请选择工厂');
			}
			$prod = Db::name('sto_prod')->where('id',$d['prod_id'])->field('id,prod_name')->find();
			if(!$prod){
				$this->error('该工厂不存在');
			}

			if($d['id']){

				$list = [];	
				$arr = explode(',',$d['id']);

				foreach($arr as $v){
					$info = model('In')->with('user')->where('id',$v)->find();
					if(!$info){
						$this->error('有会员不存在');
					}
					if($info['in_zt']!=5){
						$this->error($info->user['us_account'].'用户不是被淘汰状态');
					}
				}
				$brr = [
					'in_zt' => 1,
					'in_ru_time' => date('Y-m-d H:i:s'),
					'prod_id'   => $d['prod_id'],
					'in_gao' => $prod['prod_ru'],
					'in_ru' => $prod['prod_gao'],
				];
				$crr = [
					'us_zt' => 1,
				];
				$drr = [
					'note'  => "后台帮助选择工厂".$prod['prod_name'],
                	'rec_add_time' => date('Y-m-d H:i:s'),
				];
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					$brr['id'] = $v;
					$crr['id'] = $inf->user['id'];
					$drr['us_id'] = $inf->user['id'];
					Db::name('in')->update($brr);
					Db::name('user')->update($crr);
					Db::name('in_rec')->insert($drr);
				}
				$this->success('选厂成功');
			}else{
				$this->error('请传入用户');
			}
		}else{
			$d = input('get.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);
				foreach ($arr as $v) {
					$inf =  model('In')->with('user')->where('id',$v)->find();
					array_push($list,$inf);
				}

				$prod = Db::name('sto_prod')->where('prod_status',1)->where('delete_time',null)->field('id,prod_name')->select();
				$this->assign(array(
					'list' => $list,
					'prod' => $prod,
					'id' => $d['id'],
				));
				return $this->fetch();
			}else{
				$this->error('请选择用户');
			}
		}
	}
	//满工
	public function is_man(){

		if(is_post()){

			$d = input('post.');
			if($d['id']){
				$list = [];	
				$arr = explode(',',$d['id']);

				foreach($arr as $v){
					$info = model('In')->with('user')->where('id',$v)->find();
					if(!$info){
						$this->error('有会员不存在');
					}
					if($info['in_zt']!=3){
						$this->error($info->user['us_account'].'用户不是已入职状态,不能满工');
					}
				}
				$brr = [
					'in_zt' => 4,
					'in_man_time' => date('Y-m-d H:i:s'),
				];
				$crr = [
					'us_zt' => 4,
					'us_man_time' => date('Y-m-d H:i:s'),
				];
				$drr = [
					'note'  => "已满工",
                	'rec_add_time' => date('Y-m-d H:i:s'),
				];

				foreach ($arr as $v) {
					// $inf =  model('In')->with('user')->where('id',$v)->find();
					$inf = Db::name('in')->where('id',$v)->find();
					$brr['id'] = $v;
					$crr['id'] = $inf['us_id'];
					$drr['us_id'] = $inf['us_id'];
					Db::name('in')->update($brr);
					Db::name('user')->update($crr);
					Db::name('in_rec')->insert($drr);
					model('User')->shang($inf['us_id'],$inf['in_gao'],$inf['in_ru']);
				}
				$this->success('满工成功');
			}else{
				$this->error('请传入用户');
			}
		}else{
			$this->error('get');
		}
	}
}

<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @todo
 */
class Order extends Common {

	// ------------------------------------------------------------------------
	// 订单列表
	public function index() {
		if (is_post()) {

			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel', input('get.keywords'))->value('id');
			if ($us_id) {
				$us_id = 0;
				$array = model('StoOrder')->where('us_id',$us_id)->field('order_number')->select()->toArray();
				$arr = array_column($array,'order_number');
				$this->map[] = ['order_number', 'in', $arr];
			}
		}

		if (input('get.status') != "") {
			$this->map[] = ['detail_status', '=', input('get.status')];
		}

		if (input('get.prod_name') != "") {
			$this->map[] = ['prod_name', 'like', "%".input('get.prod_name')."%"];
		}
		if (input('get.mer_name') != "") {
			if(input('get.mer_name')=="自营"){
				$mer_id = 0;
			}else{
				$mer = model("StoMer")->where('mer_name', input('get.mer_name'))->find();
				if($mer){
					$mer_id = $mer['id'];
				}else{
					$mer_id = 999999;
				}
			}
			$this->map[] = ['mer_id', '=', $mer_id];
			
		}

		if (input('get.order_number') != "") {
			$this->map[] = ['order_number', '=', input('get.order_number')];
		}
		if (input('get.start')) {
			$this->map[] = ['detail_add_time', '>=', input('get.start')];
		}
		if (input('get.end')) {
			$this->map[] = ['detail_add_time', '<=', input('get.end')];
		}


		$list = model('StoOrderDetail')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	public function detail() {

		
		$id = input('id');
		$info = model('StoOrderDetail')->detail(['id'=>$id]);
		if (is_post()) {
			$da  = input('post.');
			if($info['detail_status']<1 || $info['detail_status']>3){
				return ['code'=>0,'msg'=>'该订单状态不支持发货'];
			}
			$da['detail_status'] = 2;
			$da['detail_delive_time'] = date('Y-m-d H:i:s');
			$res = model("StoOrderDetail")->update($da);
			
			return ['code'=>1,'msg'=>'成功'];
		}
		$id = input('get.id');
		$info = model('StoOrderDetail')->detail(['id'=>$id]);
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}

	public function finish(){
		if(is_post()){
			$id = input('post.id');
			$info = model('StoOrderDetail')->detail(['id'=>$id]);
			$time = unixtime('day',-10);
			$ten = date('Y-m-d H:i:s',$time);
			if($info['detail_status']!=2 || $info['detail_delive_time']<$ten ){
				return ['code'=>0,'msg'=>'该订单不是待收货状态或发货时间小于10天'];
			}
		}else{
			$this->error('非法操作');
		}
	}	

	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoOrderDetail')->get($id);
        if ($info) {
            $rel = model('StoOrderDetail')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}
}

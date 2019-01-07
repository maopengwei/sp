<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Db;

/**
 * 乱七八糟控制器
 */
class Cron extends Base {


	/*
	速聘经理绑定团队累计入职满工时人数50人，奖励当季业绩1000元，以4/8/12月最后一天结单
	速聘经理绑定团队累计入职满工时人数100人，奖励当季业绩2000元，以4/8/12月最后一天结单
	雅琥总监绑定团队累计入职满工时人数200人，奖励当季业绩3000元，以4/8/12月最后一天结单
	 */
	public function team(){

		$ddd = date('m-d');
		if($ddd == '4-30' || $ddd == '08-31' || $ddd =='12-31' || $ddd == '11-12'){
			
			$time = unixtime('day',-120);
			$aa = date('Y-m-d H:i:s',$time);
			$list = Db::name('user')->where('us_status',1)->where('delete_time',null)->select();
			foreach ($list as $k => $v) {
				$count = Db::name('user')
					->where('us_pid',$v['id'])
					->whereTime('us_man_time','>',$time)
					->where('us_zt',4)
					->where('delete_time',null)->count();
				$cal = Db::name('sys_team')->where('level','<=',$v['us_level'])->where('num','<=',$count)->order('id desc')->value('jine');
				if($cal){
					model('User')::usWalChange($v['id'],$cal,7);
				}
			}
		}
	}

	public function ce(){
		// halt(123);
        $list = model('InGift')->where('cate_pid',0)->field('id')->select();
        
        $b = '';
        $c = [];
        foreach ($list as $k => $v) {
            $b = model('InGift')->where('cate_pid',$v['id'])->value('cate_name').'、'.$b;
            $d = model('InGift')->where('cate_pid',$v['id'])->order('id desc')->value('cate_pic');
            array_push($c,$d);
            $list[$k]['name'] = $b;
            $list[$k]['pic'] = $c;
        }

	        // foreach ($list as $k => $v) {
	        //     $list[$k]['son'] = model('InGift')->where('cate_pid',$v['id'])->select();
	        // }

	        /*
	         $list[$k]['name'] = $list[$k]['name'].','.model('InGift')->where('cate_pid',$v['id'])->value('cate_name');
	            $list[$k]['pic'][] = array_push(model('InGift')->where('cate_pid',$v['id'])->value('cate_pic'),$list[$k]['pic']);
	         */
        halt($list);         
        $this->msg($list);
	}


}
<?php
namespace app\admin\controller;
use think\Db;
use think\Container;

/**
 * 消息控制器
 */
class News extends Common {
	protected $order;
	public function __construct() {
		parent::__construct();
		$this->order = 'id desc';
	}
	//新闻列表
	public function index() {
		if(input('get.keywords')){
			$this->map[] = ['me_title','like','%'.input('get.keywords').'%'];
		}
		$this->map[] = ['me_type','=',1];
		$list = model('Message')->where($this->map)->order($this->order)->paginate($this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	//发送列表
	public function fasong() {
		if(input('get.keywords')){
			$this->map[] = ['me_title','like','%'.input('get.keywords').'%'];
		}
		$this->map[] = ['me_type','=',3];
		$list = model('Message')->where($this->map)->order($this->order)->paginate($this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
	//消息列表
	public function message() {
		if(input('get.keywords')){
			$this->map[] = ['me_title','like','%'.input('get.keywords').'%'];
		}
		$this->map[] = ['me_type','=',2];
		$list = model('Message')->where($this->map)->order($this->order)->paginate($this->size);
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}
	//添加
	public function add() {	
		if (is_Post()) {
			$d = input('post.');
			if ($d['me_title'] == "" || $d['me_content'] == "") {
				$this->error('标题和内容不能为空');
			}
			$data = array(
				'me_add_time' => date('Y-m-d H:i:s'),
				'me_title' => $d['me_title'],
				'me_intro' => $d['me_intro'],
				'me_content' => $d['me_content'],
				'me_type' => $d['me_type'],
			);
			if($d['me_type']==3){
				if($d['us_id']){
					$data['us_id'] = $d['us_id'];
				}else{
					$this->error('请填写用户名称');
				}
			}

			$rel = model('Message')->insertGetid($data);
			if ($rel) {
				$this->success('添加成功');
			}
		}
		return $this->fetch();
	}
	// 修改
	public function edit() {
		$id = input('id');
		if (is_post()) {
			$data = input('post.');
			model('Message')->update($data);
			$this->success('修改成功');
			
		}
		$this->assign('info',  model("Message")->get($id));
		return $this->fetch();
	}


	//用户
	
	public function get_user(){
		if(is_post()){
			$d = input('post.');
			$info = model('User')->where('us_account',$d['us_account'])->find();
			if(count($info)){
				return [
					'code'=>1,
					'us_tel' => $info['us_tel'],
					'us_id' => $info['id'],
				];
			}else{
				return ['code'=>0];
			}
		}
	}



	public function shipin(){
		if(is_post()){
			$bb = $_FILES['file'];
			
			$cc = env('ROOT_PATH');

			$vedo_name = rand(100, 999) . time() . '.mp4';
			$path = "/uploads/" . date("Ymd") . '/' . $vedo_name;
			$vedo_file = env('ROOT_PATH') . 'public/' . $path;
			if(!is_dir(dirname($vedo_file))){
				mkdir(dirname($vedo_file), 0755, true);
			}

			$dd = move_uploaded_file($bb['tmp_name'], $vedo_file);
			if($dd){
				Db::name('sys_config')->where('id',8)->setfield('value',$path);
				// if(input('pic')){
				// 	$dd = base64_upload(input('pic'));
				// 	if($dd){
				// 		Db::name('sys_config')->where('id',14)->setfield('value',$dd);
				// 	}
				// }
				$this->success('成功');
			}else{
				$this->error('失败');
			}

		}
		return $this->fetch();
	}

	//上传图片
	public function vedoPic() {

		$bb = Container::get('env')->get('ROOT_PATH');
		$file = $this->request->file('file');
		$info = $file->validate(['size' => '4096000'])
			->move($bb . 'public/uploads/');
		if ($info) {
			$path = '/uploads/' . $info->getsavename();
			$path = str_replace('\\', '/', $path);
			Db::name('sys_config')->where('id',14)->setfield('value',$path);
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

	public function other(){

		return $this->fetch();
	}

	public function upload(){
        global $_W;
        global $_GPC;
        $fileArr = $_FILES['mf'];
        $title = input('title');
        //设置预览目录,上传成功的路径
        $previewPath = Container::get('env')->get('ROOT_PATH').'public/uploads/';
        $ext = pathinfo($fileArr['name'], PATHINFO_EXTENSION);//获取当前上传文件扩展名
        $arrExt = array('3gp','rmvb','flv','wmv','avi','mkv','mp4','mp3','wav',);
        if(!in_array($ext,$arrExt)) {
              exit(json_encode(-1,JSON_UNESCAPED_UNICODE));//视/音频或采用了不合适的扩展名！
        } else {
                //文件上传到预览目录
                $previewName = 'pre_'.md5(mt_rand(1000,9999)).time().'.'.$ext; //文件重命名

                $previewSrc = $previewPath.$previewName;
                $previewSrc = str_replace("\\", "/", $previewSrc);
                if(move_uploaded_file($fileArr['tmp_name'],$previewSrc)){//上传文件操作，上传失败的操作
                        $path =  '/uploads/'.$previewName;
                        exit($path);
                    // $this->success($previewSrc);

                } else {
                    //上传成功的失败的操作
                    exit(json_encode(0,JSON_UNESCAPED_UNICODE));
                }
        }
    }



     public function xieyi(){
        $id = input('id');
        if (is_post()) {
            $data = input('post.');
            model('Message')->update($data);
            $this->success('修改成功');
        }

        $this->assign('info',  model("Message")->where('me_type',5)->find());
        return $this->fetch();
    }
    public function ruzhu(){
        $id = input('id');
        if (is_post()) {
            $data = input('post.');
            model('Message')->update($data);
            $this->success('修改成功');
        }

        $this->assign('info',  model("Message")->where('me_type',6)->find());
        return $this->fetch();
    }



}

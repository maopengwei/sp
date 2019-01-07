<?php
namespace app\common\controller;
use think\Facade\Request;
use think\Response;
use think\exception\HttpResponseException;
class Api extends Base {
	public function initialize() {
		parent::initialize();
		$this->size = 10;
		/*允许跨域*/
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
		header('Access-Control-Allow-Origin:' . $origin);
		header('Access-Control-Allow-Credentials: true');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authToken");
		if (is_options()) {
			$this->result("1", 402, "option请求", "json");
		}
	}

	

	protected function msg($data, $code = 200, $msg = '成功', $type = 'json', array $header = [])
    {
        if(is_array($data) && key_exists('code',$data)){
            $result = $data;
            $result['time'] = date('Y-m-d H:i:s', Request::instance()->server('REQUEST_TIME'));
        }else{
            $result = [
                'code' => 1,
                'msg'  => $msg,
                'data' => $data,
                'time' => date('Y-m-d H:i:s', Request::instance()->server('REQUEST_TIME')),
            ];
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 自定义信息
     * 
     * @param  string $msg [description]
     * @return [type]           [description]
     */
    public function s_msg($msg = '成功', $code = 200, $type = 'json')
    {
        if (is_array($msg)) {
            $result = $msg;
        } else {
            $result = [
                'code' => 1,
                'msg'  => $msg,
                'data' => '',
            ];
        }
        
        $response = Response::create($result, $type, $code);

        throw new HttpResponseException($response);
    }

    /**
     * 错误信息
     * 
     * @param  string $errorMsg [description]
     * @return [type]           [description]
     */
    public function e_msg($errorMsg = '失败', $code = 200, $type = 'json')
    {
        
        if (is_array($errorMsg)) {
            $result = $errorMsg;
        } else{
            $result = [
                'code' => 0,
                'msg'  => $errorMsg,
                'data' => '',
            ];
        }
        
        $response = Response::create($result, $type, $code);

        throw new HttpResponseException($response);
    }
	protected function object_array($array)
    {
        if (is_object($array)) {
            $array = (array) $array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
}

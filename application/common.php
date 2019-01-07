<?php

function GetRandStr($len) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", 
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
     * 获取一个基于时间偏移的Unix时间戳
     *
     * @param string $type 时间类型，默认为day，可选minute,hour,day,week,month,quarter,year
     * @param int $offset 时间偏移量 默认为0，正数表示当前type之后，负数表示当前type之前
     * @param string $position 时间的开始或结束，默认为begin，可选前(begin,start,first,front)，end
     * @param int $year 基准年，默认为null，即以当前年为基准
     * @param int $month 基准月，默认为null，即以当前月为基准
     * @param int $day 基准天，默认为null，即以当前天为基准
     * @param int $hour 基准小时，默认为null，即以当前年小时基准
     * @param int $minute 基准分钟，默认为null，即以当前分钟为基准
     * @return int 处理后的Unix时间戳
     */
    function unixtime($type = 'day', $offset = 0, $position = 'begin', $year = null, $month = null, $day = null, $hour = null, $minute = null)
    {
        $year = is_null($year) ? date('Y') : $year;
        $month = is_null($month) ? date('m') : $month;
        $day = is_null($day) ? date('d') : $day;
        $hour = is_null($hour) ? date('H') : $hour;
        $minute = is_null($minute) ? date('i') : $minute;
        $position = in_array($position, array('begin', 'start', 'first', 'front'));

        switch ($type)
        {
            case 'minute':
                $time = $position ? mktime($hour, $minute + $offset, 0, $month, $day, $year) : mktime($hour, $minute + $offset, 59, $month, $day, $year);
                break;
            case 'hour':
                $time = $position ? mktime($hour + $offset, 0, 0, $month, $day, $year) : mktime($hour + $offset, 59, 59, $month, $day, $year);
                break;
            case 'day':
                $time = $position ? mktime(0, 0, 0, $month, $day + $offset, $year) : mktime(23, 59, 59, $month, $day + $offset, $year);
                break;
            case 'week':
                $time = $position ?
                        mktime(0, 0, 0, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)) + 1 - 7 * (-$offset), $year) :
                        mktime(23, 59, 59, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)) + 7 - 7 * (-$offset), $year);
                break;
            case 'month':
                $time = $position ? mktime(0, 0, 0, $month + $offset, 1, $year) : mktime(23, 59, 59, $month + $offset, cal_days_in_month(CAL_GREGORIAN, $month + $offset, $year), $year);
                break;
            case 'quarter':
                $time = $position ?
                        mktime(0, 0, 0, 1 + ((ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) - 1) * 3, 1, $year) :
                        mktime(23, 59, 59, (ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) * 3, cal_days_in_month(CAL_GREGORIAN, (ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) * 3, $year), $year);
                break;
            case 'year':
                $time = $position ? mktime(0, 0, 0, 1, 1, $year + $offset) : mktime(23, 59, 59, 12, 31, $year + $offset);
                break;
            default:
                $time = mktime($hour, $minute, 0, $month, $day, $year);
                break;
        }
        return $time;
    }






//文件对象 上传图片
function uploads($file){

	$info = $file->validate(['size' => '4096000'])
		->move(env('ROOT_PATH') . 'public/uploads/');
	if ($info) {
		$path = '/uploads/' . $info->getsavename();
		$path = str_replace('\\', '/', $path);
		return [
			'code' => 1,
			'path' => $path,
		];
	} else {
		return [				
			'msg' => $file->getError(),
			'code' => 0,
		];
	}
}
//base64 上传图片
function base64_upload($base64) {
	$base64_image = str_replace(' ', '+', $base64);
	//post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
	if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
		$image_name = rand(100, 999) . time() . '.png';
		$path = "/uploads/" . date("Ymd") . '/' . $image_name;

		$image_file = env('ROOT_PATH') . 'public/' . $path;

		if(!is_dir(dirname($image_file))){
			mkdir(dirname($image_file), 0755, true);
		}
		//服务器文件存储路径
		if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))) {
			return $path;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function is_options() {
	return app('request')->isOptions();
}
function is_post() {
	return app('request')->isPost();
}
function is_get() {
	return app('request')->isGet();
}



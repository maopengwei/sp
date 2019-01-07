<?php
namespace app\admin\controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 *  下载excel
 */
use think\Controller;

class Excel extends Controller
{
    public function user($list)
    {
        $bb = env('ROOT_PATH') . "public\user.xlsx";
        if (file_exists($bb)) {
            unlink($bb);
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
}

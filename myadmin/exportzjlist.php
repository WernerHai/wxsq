<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once('biaoqing.php');
require_once('../common/db.class.php');
$flag_m=new M('view_zjlist');
$plugs_m=new M('plugs');
$plugs=$plugs_m->select('choujiang = 1');
foreach($plugs as $k=>$v){
	$plugs[$v['name']]=$v['title'];
}

$data=$flag_m->select('1');
// $newdata=array();
require_once '../library/phpexcel/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("微赢科技")
							 ->setLastModifiedBy("微赢科技")
							 ->setTitle("Office 2007 XLSX 现场活动大屏幕系统签到用户列表")
							 ->setSubject("Office 2007 XLSX 现场活动大屏幕系统签到用户列表")
							 ->setDescription("现场活动大屏幕系统签到用户列表.")
							 ->setKeywords("现场活动大屏幕系统签到用户列表")
							 ->setCategory("微赢科技程序导出文件");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '昵称')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '手机号')
            ->setCellValue('D1', '头像路径')
            ->setCellValue('E1', '中奖时间')
            ->setCellValue('F1', '发奖时间')
            ->setCellValue('G1', '验证码')
            ->setCellValue('H1', '内定状态')
            ->setCellValue('I1', '中奖状态')
            ->setCellValue('J1', '活动类型')
            ->setCellValue('K1', '奖品名称');
$i=2;
// echo var_export($data);exit();
foreach($data as $q){
    $q['nickname'] = pack('H*', $q['nickname']);
    $q = emoji_unified_to_html(emoji_softbank_to_unified($q));
    $statustext='';
    if($q['status']==1){
    	$statustext='未中';
    }
    if($q['status']==2){
    	$statustext='未发';
    }
    if($q['status']==3){
    	$statustext='已发';
    }
    $designatedtext='';
//     if($q['designated']==1){
//     	$designatedtext='';
//     }
    if($q['designated']==2){
    	$designatedtext='必中';
    }
    if($q['designated']==3){
    	$designatedtext='不中';
    }
//     echo var_export($plugs);
//     echo $q['fromplug']
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $q['nickname'])
            ->setCellValue('B'.$i, $q['signname'])
            ->setCellValue('C'.$i, $q['phone'])
            ->setCellValue('D'.$i, $q['avatar'])
            ->setCellValue('E'.$i, empty($q['zjdatetime'])?'':date('Y-m-d H:i:s',$q['zjdatetime']))
            ->setCellValue('F'.$i, empty($q['fjdatetime'])?'':date('Y-m-d H:i:s',$q['fjdatetime']))
            ->setCellValue('G'.$i, $q['verifycode'])
            ->setCellValue('H'.$i, $designatedtext)
            ->setCellValue('I'.$i, $statustext)
            ->setCellValue('J'.$i, $plugs[$q['fromplug']])
            ->setCellValue('K'.$i, $q['awardname']);
    $i++;
    // $newdata[]=$q;
}
// exit();
$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('A'.$i, '总人数：'.($i-2).'人');

// Miscellaneous glyphs, UTF-8
// $objPHPExcel->setActiveSheetIndex(0)
//             ->setCellValue('A4', 'Miscellaneous glyphs')
//             ->setCellValue('A5', '');

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('现场活动大屏幕系统中奖用户列表');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// $objPHPExcel->setActiveSheetIndex(0);

// $time_str=strval(date('Y_m_d'，time()));
// $filename='现场活动大屏幕系统签到用户列表_导出时间_'.$time_str.'.xlsx';
// Redirect output to a client鈥檚 web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="现场活动大屏幕系统签到用户列表_导出时间.xlsx"');
header('Content-Disposition: attachment;filename="现场活动大屏幕系统中奖用户列表.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;



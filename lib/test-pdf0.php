<?php
use setasign\Fpdi\Fpdi;
require('fpdf/FPDF_Thai.php');
require_once('fpdi2/src/autoload.php');
error_reporting(E_ERROR | E_PARSE);


$pdf = new Fpdi();
$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
$pdf->AddFont('THSarabunNew','B','THSarabunNew Bold.php');
$pdf->setSourceFile('../forms/pnd53.pdf');
// $pdf->setSourceFile('pnd53-0.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->AddPage();

$pdf->useTemplate($tplIdx);

$pdf->SetFillColor(255,255,255);
// $pdf->SetFillColor(255,0,0);
$pdf->Rect(60.3, 154.5, 15, 4, "F");

$pdf->SetFont('THSarabunNew','',11.5);
$pdf->SetXY(59.5, 151.5);
$pdf->Write(10, iconv('UTF-8','cp874' ,'ได้นิติบุคคล'));

/*
// For clear data of downloaded pfd file
// document used message
$pdf->SetFillColor(255,255,255);
// $pdf->SetFillColor(255,0,0);
$pdf->Rect(30, 32, 150, 7, "F");

// amount in words
$pdf->Rect(114, 209, 72, 5, "F");

// Amount numbers
$pdf->Rect(138.5, 202.5, 23.5, 5, "F");
$pdf->Rect(163, 202.5, 23.5, 5, "F");

// 6 Other
$pdf->Rect(40, 194, 40, 7, "F");
*/

/*
$pdf->SetFont('THSarabunNew','',12);

if($pageNo == 1) {
  $pdf->SetXY(61, 32);
  $pdf->Write(10, iconv('UTF-8','cp874' ,'ฉบับที่ 1 (สําหรับผู้ถูกหักภาษีณที่จ่ายใช้แนบพร้อมกับแบบแสดงรายการภาษี)'));
} else {
  $pdf->SetXY(72, 32);
  $pdf->Write(10, iconv('UTF-8','cp874' ,'ฉบับที่ 2 (สําหรับผู้ถูกหักภาษีณที่จ่ายเก็บไว้เป็นหลักฐาน)'));
}

// Ploy pp address
$pdf->SetXY(23, 62.7);
$pdf->Write(10, iconv('UTF-8','cp874' ,'ห้างหุ้นส่วน จำกัด พลอย พีพี'));

$pdf->SetXY(23, 70.5);
$pdf->drawTextBox(iconv('UTF-8','cp874' ,'351/75 หมู่ 6 หมู่บ้าน คาซ่า เลเจ้นด์ ต.สุรศักดิ์ อ.ศรีราชา จ.ชลบุรี 20110'), 81, 14.5, 'L', 'T', false);
// $pdf->SetFillColor(255,0,0);
// $pdf->Rect(23, 70.5, 81, 10, "F"); //23, 65
//
// $pdf->Rect(105, 70.5, 81, 10, "F"); //23, 65

$pdf->SetXY(23, 77);
$pdf->Write(10, iconv('UTF-8','cp874' ,'เลขประจำตัวผู้เสียภาษี: 0203553005571'));

// Vendor address
$pdf->SetXY(105, 77);
$pdf->Write(10, iconv('UTF-8','cp874' ,'เลขประจำตัวผู้เสียภาษี: 1234567890123'));

// $pdf->SetXY(45, 192.5);
// $pdf->Write(10, iconv('UTF-8','cp874' ,'ค่าขนส่ง'));
$pdf->SetLineWidth(0.5);
$pdf->Line(71.5, 188, 79.5, 188);

define ('K_PATH_IMAGES', dirname(__FILE__).'/../images/');
$image_file = K_PATH_IMAGES.'ploypp_stamp.png';
$pdf->Image($image_file, 155, 235, 27, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);


*/

$pdfData = $pdf->Output("", "S");
echo "data:application/pdf;base64," . base64_encode($pdfData);
// $res = base64_encode($pdfData);
//
// $result["data"]["refNo"]  = $refNo;
// $result["data"]["base64"] = $res;
// return $result;

?>

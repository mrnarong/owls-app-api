<?php
// $path = substr(__FILE__, 0, strpos(__FILE__, 'v1/')+3);
// use setasign\Fpdi\Fpdi;
// require_once ('../inc/tcpdf_include.php');
// require_once ('inc/bahteng.inc.php');
// require_once ('inc/date.inc.php');
require_once('../inc/pdf_doc_config.php');
require_once('tcpdf/tcpdf.php');
require_once ('fpdf/fpdf_thai.php');
require_once ('fpdi2/src/autoload.php');
// require_once ($path.'lib/pnd/pnd.inc.php');
// require_once ($path.'lib/pipe/pipe.inc.php');
// require_once ($path.'lib/config-lib/config.inc.php');
error_reporting (E_ERROR | E_PARSE);


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
  $pdf->setPrintFooter(false);
  $pdf->SetMargins(20, 10, 20);
  $pdf->AddPage('P', 'A4');

  // $pndBody($legalEntity, $data["accNo"]);

  $pdf->SetFont('thsarabunnew','N',14);
  $pdf->SetXY(150, 20);
  $pdf->Cell(40, 5, "1234", 0, 0, 'R');

  $pdf->SetFont('thsarabunnew','N',12);
  $pdf->Text(20, 32, "IT-108");
  $pdf->SetXY(20, 37);
  $pdf->MultiCell(85, 15, "ทดสอบภาษาไทยว่าได้แสดงถูกต้องทั้งหมดหรือไม่", 0, 'L');
  $pdf->Text(48, 51, "IT-108 taxid");

  $pdf->Text(105, 32, "IT-108 payee");
  $pdf->SetXY(105, 37);
  $pdf->MultiCell(85, 15, "payee addr", 0, 'L');
  // $pdf->Text(($legalEntity === 'legal') ? 133 : 129.5, 51, $payeeTaxId);


  $pdfData = $pdf->Output("", "S");
  $base64 = base64_encode($pdfData);
  echo $base64;

?>

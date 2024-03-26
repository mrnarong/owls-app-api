<?php
// header("Content-type: application/pdf");
// $path = substr(__FILE__, 0, strpos(__FILE__, 'v1/')+3);
// use setasign\Fpdi\Fpdi;
// require_once ('../inc/tcpdf_include.php');
// require_once ('inc/bahteng.inc.php');
// require_once ('inc/date.inc.php');
require_once('../../inc/pdf_doc_config.php');
require_once('../../lib/tcpdf/tcpdf.php');
require_once('../../lib/fpdf/fpdf_thai.php');
require_once('../../lib/fpdi2/src/autoload.php');
require_once ('../../inc/date.utils.inc.php');
// require_once ($path.'lib/pipe/pipe.inc.php');
// require_once ($path.'lib/config-lib/config.inc.php');

error_reporting(E_ERROR | E_PARSE);

define("ROW_SIZE", 6.3);
define("TOP_MARGIN", ROW_SIZE * 3);
define("LEFT_MARGIN", 20);
define("HEADER_SIZE", ROW_SIZE * 5);
define("AMOUNT_COL_SIZE", 20);
define("LEFT_SEC_WIDTH", 130);
define("RIGHT_SEC_WIDTH", 45);
define("LOGO_WIDTH", 35);
define("STAMPER_X", 105);
define("STAMPER_Y", 85);
define("STAMPER_WIDTH", 25);


class PDFPayrollSlip {
    protected $pdf;
    public function __construct() {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(LEFT_MARGIN, TOP_MARGIN, 20);
    }
    public function render() {
        $pdfData = $this->pdf->Output("", "S");
        $base64 = base64_encode($pdfData);
        // echo "data:application/pdf;base64,".$pdfData;
        return $base64;
    }

    function addPage($data, $top=true) {
        // print_r($data);
        $topMargin = TOP_MARGIN;
        if($top) {
            $this->pdf->AddPage('P', 'A4');
        } else {
            // $this->pdf->AddPage('P', 'A4'); // test
            $topMargin += 140;
        }

        $this->pdf->SetFont('thsarabunnew', 'N', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetXY(LEFT_MARGIN, $topMargin);
        $this->pdf->Cell(LEFT_SEC_WIDTH, HEADER_SIZE, "", 1, 0, 'R');

        $this->pdf->Image(__DIR__."/../../../app_data/api/images/payroll-head-bg.png", LEFT_MARGIN, $topMargin, LEFT_SEC_WIDTH, HEADER_SIZE);
        
        $filename = __DIR__."/../../../app_data/api/images/".$data["companyInfo"]["logo"];
        if($data["companyInfo"]["logo"] && file_exists($filename)) {
            $this->pdf->Image($filename, LEFT_MARGIN+2.5, $topMargin+2.5, LOGO_WIDTH-5, 0, 'PNG', '', '', false, 300, '', false, false, 0, true, false, false);
        }

        $this->pdf->SetFillColor(20, 20, 150);
        $this->pdf->SetXY(LEFT_MARGIN, $topMargin + HEADER_SIZE);
        $this->pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', true);
    
        for ($i = 1; $i <= 9; $i++) {
            $this->pdf->SetXY(LEFT_MARGIN, $topMargin + HEADER_SIZE + ($i * ROW_SIZE));
            $this->pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', false);
        }
    
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetXY(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 10));
        $this->pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', true);
    
        $this->pdf->SetXY(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 11));
        $this->pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 2, "", 1, 0, 'R', false);
    
        // Right section
        $this->pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin);
        $this->pdf->Cell(RIGHT_SEC_WIDTH, HEADER_SIZE, "", 1, 0, 'R');
    
        $this->pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE);
        $this->pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 4, "", 1, 0, 'R');
    
        $this->pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 3));
        $this->pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 3, "", 1, 0, 'R');
    
        $this->pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 6));
        $this->pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 6, "", 1, 0, 'R');
    
        $this->pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 11));
        $this->pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 2, "", 1, 0, 'R');

        $this->pdf->SetFont('thsarabunnew', 'N', 10);
        $this->pdf->SetTextColor(255, 255, 255);
        // $this->pdf->Text(LEFT_MARGIN + 15, $topMargin + HEADER_SIZE + (ROW_SIZE * 0) + 1.5, "รายได้ (Income)");
        // $this->pdf->Text(LEFT_MARGIN + 55 + 23, $topMargin + HEADER_SIZE + (ROW_SIZE * 0) + 1.5, "รายการหัก (Deduction)");

        $this->pdf->MultiCell(LEFT_SEC_WIDTH/2, ROW_SIZE, 
        "รายได้ (Income)",
        0, 'C', false, 1, LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 0) + 1.5);

        $this->pdf->MultiCell(LEFT_SEC_WIDTH/2, ROW_SIZE, 
        "รายการหัก (Deduction)",
        0, 'C', false, 1, LEFT_MARGIN+(LEFT_SEC_WIDTH/2), $topMargin + HEADER_SIZE + (ROW_SIZE * 0) + 1.5);


        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5, "เงินเดือนค่าจ้าง (Salary)");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH/2, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5, "ภาษีหัก ณ ที่จ่าย");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5, "ค่าคอมมิชชั่น (Commission)");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH/2, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5, "ประกันสังคม");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 3) + 1.5, "ค่าทำงานนอกสถานที่");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 4) + 1.5, "ค่าปิดจ๊อบ (Job Incentive)");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 5) + 1.5, "Overachieved Sales Target");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 6) + 1.5, "ค่าล่วงเวลา (O.T.) ");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 7) + 1.5, "ค่าโทรศัพท์");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 8) + 1.5, "ค่าเดินทาง (Transportation)");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 9) + 1.5, "เงินโบนัส (Bonus)");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5, "ภาษีสะสม");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH/2, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5, "ประกันสังคมสะสม");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5, "ลาพักร้อน");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH/2, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5, "ลากิจ");
        $this->pdf->Text(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 12) + 1.5, "ลาป่วย");


        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5, "รวมเงินได้");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5, "รวมเงินหัก");

        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 4) + 1.5, "เงินได้สุทธิ");
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 5) + 1.5, "(Net Income)");

        $this->pdf->MultiCell(RIGHT_SEC_WIDTH, ROW_SIZE, 
        "ผู้มีอำนาจลงนาม",
        0, 'C', false, 1, LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5);

        $this->pdf->MultiCell(RIGHT_SEC_WIDTH, ROW_SIZE, 
        "นายสุภัทร ปานปุย",
        0, 'C', false, 1, LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 12) + 1.5);

    
        // Vertical lines
        $centerLeft = LEFT_MARGIN + (LEFT_SEC_WIDTH / 2);
        $this->pdf->Line($centerLeft, $topMargin + HEADER_SIZE, $centerLeft,  $topMargin + HEADER_SIZE + (ROW_SIZE * 13));
        $this->pdf->Line(LEFT_MARGIN + (LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH) - AMOUNT_COL_SIZE, $topMargin + HEADER_SIZE + (ROW_SIZE * 4), LEFT_MARGIN + (LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH) - AMOUNT_COL_SIZE, $topMargin + HEADER_SIZE + (ROW_SIZE * 6));
    
        // Vertical dasah-line
        $style = array('width' => 0.3, 'dash' => '2,2,2,2', 'phase' => 0, 'color' => array(128, 128, 128));
        $this->pdf->Line($centerLeft - AMOUNT_COL_SIZE, $topMargin + HEADER_SIZE + ROW_SIZE, $centerLeft - AMOUNT_COL_SIZE,  $topMargin + HEADER_SIZE + (ROW_SIZE * 13), $style);
        $this->pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH - AMOUNT_COL_SIZE, $topMargin + HEADER_SIZE + ROW_SIZE, LEFT_MARGIN + LEFT_SEC_WIDTH - AMOUNT_COL_SIZE,  $topMargin + HEADER_SIZE + (ROW_SIZE * 13), $style);
    
        // Horizontal dasah-line
        $this->pdf->Line(LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 12), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  $topMargin + HEADER_SIZE + (ROW_SIZE * 12), $style);
        $this->pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 2), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  $topMargin + HEADER_SIZE + (ROW_SIZE * 2), $style);
        $this->pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 3), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  $topMargin + HEADER_SIZE + (ROW_SIZE * 3), $style);
    
        // Confidential Remark
        $this->pdf->SetFont('thsarabunnew', 'N', 8);
        $this->pdf->SetTextColor(20, 20, 120);    
        $this->pdf->MultiCell(LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH, ROW_SIZE, 
        "ข้อมูลเงินเดือนและค่าจ้างเป็นข้อมูลส่วนตัว ห้ามเปิดเผยโดยเด็ดขาด และเอกสารนี้จะสมบูรณ์เมื่อมีลายเซ็นผู้มีอํานาจลงนามและตราประทับเท่านั้น",
        0, 'C', false, 1, LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 13) + 1.5);

        $this->pdf->MultiCell(LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH, ROW_SIZE, 
        "Salary and wages are confidential information. Disclosure is strictly prohibited. This document is only valid with an authorized signature and company stamp.",
        0, 'C', false, 1, LEFT_MARGIN, $topMargin + HEADER_SIZE + (ROW_SIZE * 13) + (ROW_SIZE / 2) + 1.5);


        // print_r($data["companyInfo"]);
        $this->pdf->SetFont('thsarabunnew', 'N', 13);
        $this->pdf->Text(LEFT_MARGIN+LOGO_WIDTH, $topMargin+(ROW_SIZE*0) + 1.5, $data["companyInfo"]["value"]);
        $this->pdf->SetXY(LEFT_MARGIN+LOGO_WIDTH, $topMargin+(ROW_SIZE*1) + 1.5);
        $this->pdf->MultiCell(LEFT_SEC_WIDTH-LOGO_WIDTH-1, HEADER_SIZE-(ROW_SIZE*1.5), 
            $data["companyInfo"]["address"].
            "\nเลขผู้เสียภาษี: ".$data["companyInfo"]["taxId"],
            // "\nTel: ".$data["companyInfo"]["contactPhone"].
            // "/ Email: ".$data["companyInfo"]["email"],
        0, 'L');

        $this->pdf->SetFont('thsarabunnew', 'N', 11);
        $this->pdf->SetXY(LEFT_MARGIN+LOGO_WIDTH, $topMargin+(ROW_SIZE*3));
        $this->pdf->MultiCell(LEFT_SEC_WIDTH-LOGO_WIDTH-1, HEADER_SIZE-(ROW_SIZE*1.5), 
            "\nTel: ".$data["companyInfo"]["contactPhone"].
            " / Email: ".$data["companyInfo"]["email"],
        0, 'L');


        // $this->pdf->Text(LEFT_MARGIN+LEFT_SEC_WIDTH/2-10, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5, $data["leaves"]["ANNUAL"]["total_days"]."/".$data["leaveConfig"]["ANNUAL"]["numAllow"]);
        // $this->pdf->Text(LEFT_MARGIN+LEFT_SEC_WIDTH-10, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5, $data["leaves"]["PERSONAL"]["total_days"]."/".$data["leaveConfig"]["PERSONAL"]["numAllow"]);
        // $this->pdf->Text(LEFT_MARGIN+LEFT_SEC_WIDTH/2-10, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5, $data["leaves"]["SICK"]["total_days"]."/".$data["leaveConfig"]["SICK"]["numAllow"]);

        // $amountBoxWidth = 20;
        $amountBoxHight = ROW_SIZE;
        $leftCol  = LEFT_MARGIN + (LEFT_SEC_WIDTH/2) - AMOUNT_COL_SIZE;
        $rightCol = LEFT_MARGIN + LEFT_SEC_WIDTH - AMOUNT_COL_SIZE;

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["salary"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5);

        // $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        // number_format($data["commission"], 2, ".", ","),
        // 0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["expenseAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 3) + 1.5);

        // $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        // number_format($data["allowanceAmount"], 2, ".", ","),
        // 0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 3) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["commissionAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["incentiveAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 4) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["overAcheiveAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 5) + 1.5);


        // " ".$data["overtimeHours"]." hrs."
        $this->pdf->MultiCell(30, $amountBoxHight, 
        $data["overtimeHours"]." hrs.",
        0, 'L', false, 1, LEFT_MARGIN + 18, $topMargin + HEADER_SIZE + (ROW_SIZE * 6) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["overtimeAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 6) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["phoneAllowanceAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 7) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["transportAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 8) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["bonusAmount"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 9) + 1.5);

        $this->pdf->MultiCell(RIGHT_SEC_WIDTH, $amountBoxHight, 
        number_format($data["totalIncome"], 2, ".", ","),
        0, 'R', false, 1, LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5);

        $this->pdf->MultiCell(RIGHT_SEC_WIDTH, $amountBoxHight, 
        number_format($data["totalDeduct"], 2, ".", ","),
        0, 'R', false, 1, LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5);

        $this->pdf->MultiCell(RIGHT_SEC_WIDTH, $amountBoxHight, 
            number_format($data["netIncome"], 2, ".", ","),
        0, 'R', false, 1, LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin + HEADER_SIZE + (ROW_SIZE * 4) + (ROW_SIZE * 0.5) + 1.5);




        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        ($data["leaves"]["ANNUAL"]["total_days"]?$data["leaves"]["ANNUAL"]["total_days"]:"0")."/".$data["leaveConfig"]["ANNUAL"]["numAllow"],
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        ($data["leaves"]["PERSONAL"]["total_days"]?$data["leaves"]["PERSONAL"]["total_days"]:"0")."/".$data["leaveConfig"]["PERSONAL"]["numAllow"],
        0, 'R', false, 1, $rightCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 11) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        ($data["leaves"]["SICK"]["total_days"]?$data["leaves"]["SICK"]["total_days"]:"0")."/".$data["leaveConfig"]["SICK"]["numAllow"],
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 12) + 1.5);

        
        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["tax"], 2, ".", ","),
        0, 'R', false, 1, $rightCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 1) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
        number_format($data["sso"], 2, ".", ","),
        0, 'R', false, 1, $rightCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 2) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
            number_format($data["totalWhTax"], 2, ".", ","),
        0, 'R', false, 1, $leftCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5);

        $this->pdf->MultiCell(AMOUNT_COL_SIZE, $amountBoxHight, 
            number_format($data["totalSSO"], 2, ".", ","),
        0, 'R', false, 1, $rightCol, $topMargin + HEADER_SIZE + (ROW_SIZE * 10) + 1.5);

        $this->pdf->SetFont('thsarabunnew', 'N', 12);
        $date = new DateUtil($data["payrollDate"]);
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin+(ROW_SIZE*0) + 1.5, $data["employeeId"]);
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin+(ROW_SIZE*1) + 1.5, $data["fullname"]);
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin+(ROW_SIZE*2) + 1.5, $data["position"]);
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin+(ROW_SIZE*3) + 1.5, $data["department"]);
        $this->pdf->Text(LEFT_MARGIN + LEFT_SEC_WIDTH, $topMargin+(ROW_SIZE*4) + 1.5, $date->format("DMMMMYYYY", "th"));

        // echo $data["companyInfo"]["autograph"];
        if($data["status"] == "APPROVED") {
            $filename = __DIR__."/../../../app_data/api/images/".$data["companyInfo"]["stamper"];
            if($data["companyInfo"]["stamper"] && file_exists($filename)) {
                $this->pdf->Image($filename, STAMPER_X, STAMPER_Y, STAMPER_WIDTH, 0, 'PNG', '', '', false, 300, '', false, false, 0, true, false, false);
            }

            $filename = __DIR__."/../../../app_data/api/images/".$data["companyInfo"]["autograph"];
            if($data["companyInfo"]["autograph"] && file_exists($filename)) {
                $this->pdf->Image($filename, LEFT_MARGIN + LEFT_SEC_WIDTH+5, $topMargin+(ROW_SIZE*12)+3, 35, 0, 'PNG', '', '', false, 300, '', false, false, 0, true, false, false);
            }
        }

    }
}


function renderPayrollForm()
{
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(LEFT_MARGIN, TOP_MARGIN, 20);
    $pdf->AddPage('P', 'A4');

    // $pndBody($legalEntity, $data["accNo"]);

    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN);
    $pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 5, "", 1, 0, 'R');

    $pdf->SetFillColor(20, 20, 150);
    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE);
    $pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', true);

    for ($i = 1; $i <= 8; $i++) {
        $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + ($i * ROW_SIZE));
        $pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', false);
    }

    $pdf->SetFillColor(160, 160, 160);
    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 9));
    $pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R', true);

    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 10));
    $pdf->Cell(LEFT_SEC_WIDTH, ROW_SIZE * 2, "", 1, 0, 'R', false);

    // Right section
    $pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN);
    $pdf->Cell(RIGHT_SEC_WIDTH, HEADER_SIZE, "", 1, 0, 'R');

    $pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE);
    $pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 4, "", 1, 0, 'R');

    $pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 4));
    $pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 2, "", 1, 0, 'R');

    $pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 6));
    $pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 5, "", 1, 0, 'R');

    $pdf->SetXY(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 11));
    $pdf->Cell(RIGHT_SEC_WIDTH, ROW_SIZE * 1, "", 1, 0, 'R');

    // Vertical lines
    $centerLeft = LEFT_MARGIN + (LEFT_SEC_WIDTH / 2);
    $pdf->Line($centerLeft, TOP_MARGIN + HEADER_SIZE, $centerLeft,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 12));
    $pdf->Line(LEFT_MARGIN + (LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH) - AMOUNT_COL_SIZE, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 4), LEFT_MARGIN + (LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH) - AMOUNT_COL_SIZE, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 6));

    // Vertical dasah-line
    $style = array('width' => 0.3, 'dash' => '2,2,2,2', 'phase' => 0, 'color' => array(128, 128, 128));
    // $pdf->Line($centerLeft - AMOUNT_COL_SIZE, TOP_MARGIN + HEADER_SIZE + ROW_SIZE, $centerLeft - AMOUNT_COL_SIZE,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 11), $style);
    // $pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH - AMOUNT_COL_SIZE, TOP_MARGIN + HEADER_SIZE + ROW_SIZE, LEFT_MARGIN + LEFT_SEC_WIDTH - AMOUNT_COL_SIZE,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 12), $style);

    // Horizontal dasah-line
    // $pdf->Line(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 14), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 11), $style);
    // $pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 2), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 2), $style);
    // $pdf->Line(LEFT_MARGIN + LEFT_SEC_WIDTH, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 3), LEFT_MARGIN + LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH,  TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 3), $style);

    // Confidential Remark
    $pdf->SetFont('thsarabunnew', 'N', 8);
    $pdf->SetTextColor(20, 20, 120);
    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 12));
    $pdf->Cell(LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH, ROW_SIZE, "ข้อมูลเงินเดือนและค่าจ้างเป็นข้อมูลส่วนตัว ห้ามเปิดเผยโดยเด็ดขาด และเอกสารนี้จะสมบูรณ์เมื่อมีลายเซ็นผู้มีอํานาจลงนามและตราประทับเท่านั้น", 0, 0, 'C');
    $pdf->SetXY(LEFT_MARGIN, TOP_MARGIN + HEADER_SIZE + (ROW_SIZE * 12) + (ROW_SIZE / 2));
    $pdf->Cell(LEFT_SEC_WIDTH + RIGHT_SEC_WIDTH, ROW_SIZE, "Salary and wages are confidential information. Disclosure is strictly prohibited. This document is only valid with an authorized signature and company stamp.", 0, 0, 'C');

    // $pdf->SetFont('thsarabunnew', 'N', 12);
    // $pdf->Text(20, 32, "IT-108");
    // $pdf->SetXY(20, 37);
    // $pdf->MultiCell(85, 15, "ทดสอบภาษาไทยว่าได้แสดงถูกต้องทั้งหมดหรือไม่", 0, 'L');
    // $pdf->Text(48, 51, "IT-108 taxid");

    // $pdf->Text(105, 32, "IT-108 payee");
    // $pdf->SetXY(105, 37);
    // $pdf->MultiCell(85, 15, "payee addr", 0, 'L');
    // $pdf->Text(($legalEntity === 'legal') ? 133 : 129.5, 51, $payeeTaxId);


    $pdfData = $pdf->Output("", "S");
    $base64 = base64_encode($pdfData);
    // echo "data:application/pdf;base64,".$pdfData;
    return $base64;
}

<?php
session_start();
require_once '../config/database.php';
require '../vendor/autoload.php'; // You'll need to install PhpSpreadsheet and TCPDF

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$format = $_GET['format'] ?? 'excel';

// Get payment data
$stmt = $pdo->prepare("
    SELECT 
        p.date,
        p.amount,
        CONCAT(s.name, ' ', s.surname) as staff_name,
        p.notes
    FROM payments p
    LEFT JOIN staff s ON p.staff_id = s.id
    ORDER BY p.date DESC
");
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get totals
$stmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(amount), 0) as total_payments
    FROM payments
");
$stmt->execute();
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

switch($format) {
    case 'excel':
        exportExcel($payments, $totals);
        break;
    case 'pdf':
        exportPDF($payments, $totals);
        break;
    case 'word':
        exportWord($payments, $totals);
        break;
    default:
        http_response_code(400);
        exit('Invalid format');
}

function exportExcel($payments, $totals) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Headers
    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Staff');
    $sheet->setCellValue('C1', 'Amount');
    $sheet->setCellValue('D1', 'Notes');
    
    // Data
    $row = 2;
    foreach ($payments as $payment) {
        $sheet->setCellValue('A'.$row, $payment['date']);
        $sheet->setCellValue('B'.$row, $payment['staff_name']);
        $sheet->setCellValue('C'.$row, $payment['amount']);
        $sheet->setCellValue('D'.$row, $payment['notes']);
        $row++;
    }
    
    // Total
    $sheet->setCellValue('B'.$row, 'Total:');
    $sheet->setCellValue('C'.$row, $totals['total_payments']);
    
    // Format
    $sheet->getStyle('C2:C'.$row)->getNumberFormat()->setFormatCode('#,##0');
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="payment_report.xlsx"');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function exportPDF($payments, $totals) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator('Assidcoff');
    $pdf->SetTitle('Payment Report');
    
    $pdf->AddPage();
    
    // Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Payment Report', 0, 1, 'C');
    
    // Headers
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(40, 7, 'Date', 1);
    $pdf->Cell(50, 7, 'Staff', 1);
    $pdf->Cell(40, 7, 'Amount', 1);
    $pdf->Cell(60, 7, 'Notes', 1);
    $pdf->Ln();
    
    // Data
    $pdf->SetFont('helvetica', '', 12);
    foreach ($payments as $payment) {
        $pdf->Cell(40, 6, $payment['date'], 1);
        $pdf->Cell(50, 6, $payment['staff_name'], 1);
        $pdf->Cell(40, 6, number_format($payment['amount']), 1);
        $pdf->Cell(60, 6, $payment['notes'], 1);
        $pdf->Ln();
    }
    
    // Total
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(90, 7, 'Total:', 1);
    $pdf->Cell(40, 7, number_format($totals['total_payments']), 1);
    
    $pdf->Output('payment_report.pdf', 'D');
    exit;
}

function exportWord($payments, $totals) {
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    
    // Title
    $section->addText('Payment Report', ['bold' => true, 'size' => 16]);
    $section->addTextBreak();
    
    // Table
    $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
    
    // Headers
    $table->addRow();
    $table->addCell(2000)->addText('Date', ['bold' => true]);
    $table->addCell(2500)->addText('Staff', ['bold' => true]);
    $table->addCell(2000)->addText('Amount', ['bold' => true]);
    $table->addCell(3000)->addText('Notes', ['bold' => true]);
    
    // Data
    foreach ($payments as $payment) {
        $table->addRow();
        $table->addCell(2000)->addText($payment['date']);
        $table->addCell(2500)->addText($payment['staff_name']);
        $table->addCell(2000)->addText(number_format($payment['amount']));
        $table->addCell(3000)->addText($payment['notes']);
    }
    
    // Total
    $table->addRow();
    $table->addCell(4500, ['gridSpan' => 2])->addText('Total:', ['bold' => true]);
    $table->addCell(2000)->addText(number_format($totals['total_payments']), ['bold' => true]);
    $table->addCell(3000);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment;filename="payment_report.docx"');
    
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');
    exit;
}
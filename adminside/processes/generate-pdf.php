<?php
function generatePDF($data, $reportTitle, $columns, $outputFileName, $includeTotal = false, $includeStatus = true, $orientation = 'P') {
    $pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('K8 Financial Consultancy Services');
    $pdf->SetTitle($reportTitle);
    $pdf->SetSubject('Report');
    $pdf->SetKeywords('TCPDF, PDF, report, clients');

    $pdf->AddPage();

    // Adjust dimensions based on orientation
    $pageWidth = $orientation === 'L' ? 297 : 210; // A4 width in mm for landscape or portrait
    $letterheadHeight = 50; // Height of the letterhead
    $logoX = $orientation === 'L' ? 25 : 25; // X position of the logo
    $logoY = 12; // Y position of the logo
    $logoWidth = 30; // Width of the logo
    $logoHeight = 30; // Height of the logo
    $titleX = $orientation === 'L' ? 70 : 60; // X position of the title
    $titleY = 15; // Y position of the title
    $subtitleY = 25; // Y position of the subtitle

    // Add letterhead
    $letterheadPath = realpath('assets/images/letterhead.png'); // Update this path
    if (!$letterheadPath) {
        die('Letterhead path is incorrect: ' . $letterheadPath);
    }
    $pdf->Image($letterheadPath, 0, 0, $pageWidth, $letterheadHeight, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Add logo
    $logoPath = realpath('assets/images/updated-logo.png'); // Update this path
    if (!$logoPath) {
        die('Logo path is incorrect: ' . $logoPath);
    }
    $pdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Add title and subtitle within the letterhead
    $pdf->SetY($titleY); // Adjusted to fit within the letterhead
    $pdf->SetX($titleX);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 15, 'K8 Financial Consultancy Services', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

    // Convert current time to Manila time
    $date = new DateTime('now', new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Manila'));
    $manilaTime = $date->format('Y-m-d H:i:s');

    $pdf->SetY($subtitleY); // Adjusted to fit within the letterhead
    $pdf->SetX($titleX); 
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 7, 'Report generated on ' . $manilaTime, 0, 1, 'L', 0, '', 0, false, 'T', 'M');

    // Move down to start the report content
    $pdf->Ln(27); 

    // Set font for the report content
    $contentFontSize = $orientation === 'L' ? 9 : 12;
    $pdf->SetFont('helvetica', '', $contentFontSize);

    // Create HTML content with minimalist design
    $html = '<h2 style="text-align:center; margin-top: -30px;">' . htmlspecialchars($reportTitle) . '</h2>';
    $html .= '<table border="0" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead>';
    $html .= '<tr style="background-color: #f2f2f2; text-align: center;">';

    foreach ($columns as $column) {
        $html .= '<th style="border-bottom: 1px solid #ddd; padding: 8px; text-align:center;">' . htmlspecialchars($column) . '</th>';
    }

    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    $totalAmount = 0;
    $totalCommission = 0;
    foreach ($data as $row) {
        $html .= '<tr style="text-align: center;">';
        foreach ($columns as $column) {
            $key = strtolower(str_replace(' ', '_', $column));
            $value = isset($row[$key]) ? $row[$key] : '';
            
            $html .= '<td style="border-bottom: 1px solid #ddd; padding: 8px; text-align:center;">' . htmlspecialchars($value) . '</td>';

            // Calculate total amount and commission fee
            if ($key === 'amount_finance') {
                $totalAmount += floatval($value);
                $commissionFee = floatval($value) * 0.05;
                $totalCommission += $commissionFee;
            }
        }
        $html .= '</tr>';
    }

    // Add total amount and commission fee row if $includeTotal is true
    if ($includeTotal) {
        $html .= '<tr>';
        $html .= '<td colspan="' . (count($columns) - 2) . '" style="border-bottom: 1px solid #ddd; padding: 8px; text-align:right; font-weight: bold;">Total</td>';
        $html .= '<td style="border-bottom: 1px solid #ddd; padding: 8px; text-align:center; font-weight: bold;">' . htmlspecialchars(number_format($totalAmount, 2)) . '</td>';
        $html .= '<td style="border-bottom: 1px solid #ddd; padding: 8px; text-align:center; font-weight: bold;">' . htmlspecialchars(number_format($totalCommission, 2)) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Add watermark on each page
    $pageCount = $pdf->getNumPages();
    for ($i = 1; $i <= $pageCount; $i++) {
        $pdf->setPage($i);
        $pdf->SetAlpha(0.2); 
        $pdf->SetFont('helvetica', 'B', 50);
        $pdf->SetTextColor(169, 169, 169); 

        // Adjust watermark position based on orientation
        if ($orientation === 'L') {
            $pdf->StartTransform();
            $pdf->Rotate(45, 148.5, 105); // Rotate around the center of the page
            $pdf->Text(55, 105, 'CONFIDENTIAL');
            $pdf->StopTransform();
        } else {
            $pdf->StartTransform();
            $pdf->Rotate(45, 35, 190); // Rotate around the center of the page
            $pdf->Text(55, 200, 'CONFIDENTIAL');
            $pdf->StopTransform();
        }

        $pdf->SetAlpha(1); 
        $pdf->SetTextColor(0, 0, 0); 
    }

    // Close and output PDF document
    $pdf->Output($outputFileName, 'I');
}
?>
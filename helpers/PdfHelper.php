<?php

class PdfHelper
{
    private $pdf;

    public function __construct($pdf){
        $this->pdf=$pdf;
        $this->pdf->setPaper('A4', 'portrait');
    }
    public function printPdf($nombrePdf, $data){
        $this->pdf->set_option('isRemoteEnabled', TRUE);
        $this->pdf->loadHtml($data);
        $this->pdf->render();
        $this->pdf->stream("$nombrePdf.pdf", ['Attachment' => 0]);
    }
    public function createPdf($data){
        $this->pdf->set_option('isRemoteEnabled', TRUE);
        $this->pdf->loadHtml($data);
        $this->pdf->render();
        file_put_contents( "public/pdf/pdf.pdf", $this->pdf->output());
    }
}
<?php

class QrHelper{
    private $qr;
    public function __construct($qr){
        $this->qr=$qr;
    }

    public function createQrImage($data, $tpmRuta){
        $this->qr->png($data,$tpmRuta,QR_ECLEVEL_L,4);
    }
}
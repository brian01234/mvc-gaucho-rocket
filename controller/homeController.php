<?php

class homeController{
    private $printer;
    private $sessionUser;
    private $reservaModel;
    public function __construct($printer, $reservaModel, $sessionUser){
        $this->printer=$printer;
        $this->sessionUser=$sessionUser;
        $this->reservaModel=$reservaModel;
    }

    public function show(){
        $dataSession=$this->sessionUser->validarLoginHome();
        $data["destinos"]=$this->reservaModel->dameDestinos();
        $data["session"]=$dataSession;
        echo $this->printer->render("view/homeView.html", $data);
    }
}
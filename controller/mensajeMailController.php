<?php

class mensajeMailController{
    private $printer;
    private $sessionUser;

    public function __construct($printer, $sessionUser){
        $this->printer=$printer;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $dataSession=$this->sessionUser->validarLoginHome();

        if (isset($_GET["msg"])and isset($_GET["type"])){
            $data["mensaje"]=$_GET["msg"];
            $data["type"]=$_GET["type"];
            $data["session"]=$dataSession;
            echo $this->printer->render("view/mensajeEmailView.html", $data);
        }else{
            header("Location: /mvc-gaucho-rocket/login");
        }
    }
}
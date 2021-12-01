<?php

class misReservasController{
    private $misReservasModel;
    private $printer;
    public function __construct($misReservasModel, $printer){
        $this->misReservasModel=$misReservasModel;
        $this->printer=$printer;
    }
    public function show(){
        if (isset($_SESSION["id_usuario"])){
            $data["reservas"]=$this->misReservasModel->misReservas($_SESSION["id_usuario"]);
            echo $this->printer->render("view/misReservasView.html", $data);
        }else{
            header("Location: /mvc-gaucho-rocket/login");
        }
    }
}
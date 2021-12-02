<?php

class sistemaVuelosController{
    private $printer;
    private $sistemaVuelosModel;
    private $sessionUser;

    public function __construct($sistemaVuelosModel, $printer, $sessionUser){
        $this->printer=$printer;
        $this->sistemaVuelosModel=$sistemaVuelosModel;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $this->sessionUser->validarAdministrador();
        $data["vuelos"]=$this->sistemaVuelosModel->vuelosDisponibles();
        $data["vuelosDisponibles"]="Vuelos disponibles";
        echo $this->printer->render("view/sistemaVuelosView.html", $data);
    }

    public function eliminarVuelo(){
        $this->sessionUser->validarAdministrador();
        if (isset($_POST["id_vuelo"])){
            $this->sistemaVuelosModel->eliminarVuelo($_POST["id_vuelo"]);
        }
        header("Location: /mvc-gaucho-rocket/sistemaVuelos");
    }
}
<?php
class logueadoController{

    private $printer;
    private $logueadoModel;
    private $sessionUser;

    public function __construct($logueadoModel, $printer, $sessionUser){
        $this->printer=$printer;
        $this->logueadoModel=$logueadoModel;
        $this->sessionUser=$sessionUser;
    }
    public function show(){
        $dataSession=$this->sessionUser->validarLogin();
        $data["session"]=$dataSession;
        $data["resultado"]=$this->logueadoModel->getDatosDelUsuario($_SESSION["id_usuario"]);
        if (isset($_SESSION["Administrador"])){$data["Administrador"]=$_SESSION["Administrador"];}
        echo $this->printer->render("view/logueadoView.html", $data);
    }
}
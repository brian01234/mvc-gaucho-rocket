<?php

class loginController{
    private $loginModel;
    private $printer;
    private $sessionUser;

    public function __construct($loginModel, $printer, $sessionUser){
        $this->printer=$printer;
        $this->loginModel=$loginModel;
        $this->sessionUser=$sessionUser;
    }
    public function show(){
        $data = [];
        if(!isset ($_SESSION["id_usuario"])) {
            if (isset($_GET["msg"])) {$data["msg"] = $_GET["msg"];};
            echo $this->printer->render("view/loginView.html", $data);
        }else{$this->showSesion();}
    }

    public function showSesion(){
        $id_rol=$this->sessionUser->getRol($_SESSION["id_usuario"]);
        $this->sessionUser->show($id_rol);
    }

    public function procesarLogin(){
        $email = isset($_POST["email"]) ? $_POST["email"] : "";$clave = isset($_POST["clave"]) ? $_POST["clave"] : "";
        $resultado=$this->loginModel->iniciarSesion($email, md5($clave));
        if ($resultado!=[]){
            $_SESSION["id_usuario"]=$resultado["id"];
            $this->sessionUser->show($resultado["id_rol"]);
        }else{
            header("Location: /mvc-gaucho-rocket/login?msg=El email o contraseña es incorrecto!");
        }
    }
}
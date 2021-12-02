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
            if (isset($_GET["msgRegistro"])) {$data["msgRegistro"] = $_GET["msgRegistro"];};
            echo $this->printer->render("view/loginView.html", $data);
        }else{$this->showSesion();}
    }

    public function showSesion(){
        $id_rol=$this->sessionUser->getRol($_SESSION["id_usuario"]);
        $this->sessionUser->show($id_rol);
    }

    //Action login
    public function procesarLogin(){
        if (isset($_POST["email"]) and isset($_POST["clave"])){
        $email = $_POST["email"];
        $clave = $_POST["clave"];

        $resultado=$this->loginModel->iniciarSesion($email, md5($clave));
        if ($resultado!=[]){
            if ($resultado["codigo_alta"]==null or $resultado["codigo_alta"]=="-"){
                $_SESSION["id_usuario"]=$resultado["id"];
                $_SESSION["nombre"]=$resultado["nombre"] ;
                $_SESSION["apellido"]=$resultado["apellido"];
                $_SESSION["email"]=$resultado["email"];
                $this->sessionUser->show($resultado["id_rol"]);
            }else{
                $msg["type"]="info";
                $msg["mensaje"]="Ingrese a <b>$email</b> para verificar su cuenta.";
                header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
            }
        }else{
            header("Location: /mvc-gaucho-rocket/login?msg=El email o contraseña es incorrecto!");
        }
        }else{header("Location: /mvc-gaucho-rocket/login");}
    }
}
<?php
class sessionUser{
    private $model;
    public function __construct($sessionModelUser){
        $this->model=$sessionModelUser;
    }

    public function show($rolUsuario){
        switch ($rolUsuario) {
            case "1":
                header("Location: /mvc-gaucho-rocket/sistema");
                break;
            case "2":
                header("Location: /mvc-gaucho-rocket/logueado");
                break;
            default:
                header("Location: /mvc-gaucho-rocket/home");
                break;
        }
    }
    public function getRol($idUsuario){
        $resultado=$this->model->getUserRol($idUsuario);
        return $resultado[0]["id_rol"];
    }
    public function usuarioExiste($idUsuario){
        return $this->model->getUsuario($idUsuario);
    }

    public function validarLogin(){
        if(isset ($_SESSION["id_usuario"])){
            return $_SESSION["email"];
        }else{
            header("Location: /mvc-gaucho-rocket/login");
            exit;
        }
    }
    public function validarLoginHome(){
        if(isset ($_SESSION["id_usuario"])){
            return $_SESSION["email"];
        }
    }

    function dameDatosUsuario($data){
        if(isset ($_SESSION["id_usuario"])){
            $data["nombre"] = $_SESSION["nombre"];
            $data["apellido"] = $_SESSION["apellido"];
            $data["email"] = $_SESSION["email"];
            return $data;
        }
    }

    public function validarAdministrador(){
        $this->validarLogin();
        $id_rol=$this->getRol($_SESSION["id_usuario"]);
        if ($id_rol!=1){
            $this->show($id_rol);
            exit;
        }
    }
}
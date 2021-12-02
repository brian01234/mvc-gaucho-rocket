<?php
class sistemaUsuariosController{
    private $printer;
    private $sistemaUsuariosModel;
    private $sessionUser;

    public function __construct($sistemaUsuariosModel, $printer, $sessionUser){
        $this->printer=$printer;
        $this->sistemaUsuariosModel=$sistemaUsuariosModel;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $this->sessionUser->validarAdministrador();
        $data["resultado"]=$this->sistemaUsuariosModel->listaDeUsuarios();
        echo $this->printer->render("view/sistemaUsuariosView.html", $data);
    }

    public function cambiarRolDelUsuario(){
        $this->sessionUser->validarAdministrador();
        if (isset($_POST["rol_usuario"]) AND isset($_POST["id_usuario"])){
            $this->sistemaUsuariosModel->cambiar_rol($_POST["id_usuario"], $_POST["rol_usuario"]);
            header("Location: /mvc-gaucho-rocket/sistemaUsuarios");
        }
    }

    public function eliminarUsuario(){
        $this->sessionUser->validarAdministrador();
        $this->sistemaUsuariosModel->eliminarUsuario($_POST["id_usuario"]);
        header("Location: /mvc-gaucho-rocket/sistemaUsuarios");
    }
}
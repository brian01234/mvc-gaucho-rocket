<?php
class sistemaController{
    private $printer;
    private $sistemaModel;
    private $sessionUser;

    public function __construct($sistemaModel, $printer, $sessionUser){
        $this->printer=$printer;
        $this->sistemaModel=$sistemaModel;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        if(isset ($_SESSION["id_usuario"])){
            $id_rol=$this->sessionUser->getRol($_SESSION["id_usuario"]);
            if ($id_rol==1){
                $data["resultado"]=$this->sistemaModel->listaDeUsuarios();
                echo $this->printer->render("view/sistemaView.html", $data);
            }else{
                $this->sessionUser->show($id_rol);
            }
        }else{
            header("Location: /mvc-gaucho-rocket/login");
        }
    }

    public function cambiarRolDelUsuario(){
        if (isset($_POST["rol_usuario"]) AND isset($_POST["id_usuario"])){
            $this->sistemaModel->cambiar_rol($_POST["id_usuario"], $_POST["rol_usuario"]);
            header("Location: /mvc-gaucho-rocket/sistema");
        }
    }

    public function eliminarUsuario(){
        $this->sistemaModel->eliminarUsuario($_POST["id_usuario"]);
        header("Location: /mvc-gaucho-rocket/sistema");
    }
}
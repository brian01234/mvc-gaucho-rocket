<?php

class RegistroController{
    private $registroModel;
    private $printer;
    private $mailHelper;
	
    public function __construct($registroModel,$printer, $mailHelper){
		$this->registroModel = $registroModel;
        $this->printer=$printer;
        $this->mailHelper=$mailHelper;
    }

    public function show(){
        if(isset ($_SESSION["id_usuario"])){header("Location: /mvc-gaucho-rocket/login");}
        echo $this->printer->render("view/registroView.html");
    }

    //Action registro
    public function procesarFormulario(){
        if (isset($_POST['usuario_nombre_reg'])){
        $codigo_alta=md5(time());
        $data = array(
            'nombre' => $_POST['usuario_nombre_reg'],
            'apellido' => $_POST['usuario_apellido_reg'],
            'email' => $_POST['usuario_email_reg'],
            'clave' => md5($_POST['usuario_clave_1_reg']),
            'codigo_alta' => $codigo_alta
        );

        if ($this->registroModel->validarEmail($data["email"])==[]){
            $this->registrarUsuario($data);
        }else{
            $data["msg"]="El email ya existe!";
            echo $this->printer->render("view/registroView.html",$data);
        }
        }else{header("Location: /mvc-gaucho-rocket/registro");}
    }

    public function registrarUsuario($dataRegistro){
        $mensaje='<h2>Por favor ingrese al siguiente link para poder verificar su cuenta</h2><br>';
        $mensaje.="<a href='http://localhost/mvc-gaucho-rocket/registro/verificacion/?codigo_alta=$dataRegistro[codigo_alta]'>Validar Registro</a>";
        $data=$this->mailHelper->enviarMail("Verificar cuenta",$mensaje,"$dataRegistro[email]");
        if ($data=="Enviado"){
            $msg["type"]="info";
            $this->registroModel->registrarUserModel($dataRegistro);
            $msg["mensaje"]="Enviamos un email a <b>$dataRegistro[email]</b> para verificar su cuenta.";
        }else{
            $msg["type"]="danger";
            $msg["mensaje"]="Error al registrar!";
        }
        header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
    }
    public function verificacion(){
        if (isset($_GET["codigo_alta"])){
            $data["codigo_alta"] = $_GET["codigo_alta"];
            echo $this->printer->render("view/verificacionView.html", $data);
        }else{header("Location: /mvc-gaucho-rocket/login");}
    }
    //Action de vista verificacion
    public function resultadoVerificacion(){
        if (isset($_POST["codigo_alta"])){
        $valido = $this->registroModel->validarCodigoAlta($_POST["codigo_alta"],$_POST["email"],(md5($_POST["pass"])));
        if(!$valido){
            $data["mensaje"] = "Ingreso algun dato incorrecto";
            $data["codigo_alta"]=$_POST["codigo_alta"];
            echo $this->printer->render("view/verificacionView.html", $data);
        }else{
            $idUsuario=$valido[0]["id"];
            $this->registroModel->actualizarCodigoAlta($idUsuario);
            $msg="Felicitaciones,ya puede ingresar al sistema.";
            header("Location: /mvc-gaucho-rocket/login?msgRegistro=$msg");
        }
        }else{header("Location: /mvc-gaucho-rocket/login");}
    }
}
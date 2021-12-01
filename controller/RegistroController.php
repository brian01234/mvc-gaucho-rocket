<?php

class RegistroController{
    private $registroModel;
    private $printer;
	
    public function __construct($registroModel,$printer){
		$this->registroModel = $registroModel;
        $this->printer=$printer;
    }

    public function show(){
        echo $this->printer->render("view/registroView.html");
    }
    public function procesarFormulario()
    {
        $codigo_alta=md5(time());
        $data = array(
            'nombre' => $_POST['usuario_nombre_reg'],
            'apellido' => $_POST['usuario_apellido_reg'],
            'email' => $_POST['usuario_email_reg'],
            'clave' => md5($_POST['usuario_clave_1_reg']),
            'codigo_alta' => $codigo_alta
        );

        $this->registroModel->registrarUserModel($data);
        header("Location: /mvc-gaucho-rocket/registro/validar?codigo_alta=" . $codigo_alta);
    }

    public function validar(){
		 $data["codigo_alta"] = $_GET["codigo_alta"];
         echo $this->printer->render("view/validarView.html", $data);
    }
	
	
	    public function verificacion(){
		        $data["codigo_alta"] = $_GET["codigo_alta"];
         echo $this->printer->render("view/verificacionView.html", $data);
    }
	
	public function resultadoVerificacion(){
		        $data["pass"] = $_POST["pass"];
		        $data["email"] = $_POST["email"];
				$data["codigo_alta"] = $_POST["codigo_alta"];
				$valido = $this->registroModel->getValidoCodigoAlta($data["codigo_alta"],$data["email"],$data["pass"]);
				if(!$valido){
				$data["mensaje"] = "Ingreso algun dato incorrecto";
 				}else{
				$data["muestroForm"] ="NO";
				$idUsuario=$valido[0]["id"] ;
				$actualicoCodigoAlta = $this->registroModel->getactualicoCodigoAlta($idUsuario);
 				$data["mensaje"] = "Felicitaciones,ya puede ingresar al sistema.";
 				header("Location: /mvc-gaucho-rocket/login?msgRegistro=Felicitaciones,ya puede ingresar al sistema.");

				}
         echo $this->printer->render("view/verificacionView.html", $data);
    }
}
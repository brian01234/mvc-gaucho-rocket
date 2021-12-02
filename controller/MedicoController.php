<?php

class MedicoController{
    private $medicoModel;
    private $printer;
	private $mailHelper;
	private $sessionUser;
    public function __construct($medicoModel,$printer, $mailHelper, $sessionUser){
		$this->medicoModel = $medicoModel;
        $this->printer=$printer;
        $this->mailHelper=$mailHelper;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $this->sessionUser->validarLogin();
        $turno=$this->medicoModel->datosTurnoActual($_SESSION["id_usuario"]);
        if ($turno!=[] AND $turno[0]["estado"]!="Baja"){
            if ($turno[0]["estado"]=="En espera"){
                //Realizar chequeo
                $this->realizarChequeo();
            }elseif ($turno[0]["estado"]=="Chequeo realizado"){
                //Mostrar vista de chequeo realizado con el codigo de viajero=columna 'nivel' de la tabla turnos
                $this->resultadoChequeo();
            }
        }else{
            //solicitar turno
            $this->solicitarTurno();
        }
    }
    public function solicitarTurno(){
        $dataSession=$this->sessionUser->validarLogin();
        $data["session"]=$dataSession;
        $data["centrosMedicos"] = $this->medicoModel->dameCentros();
        $data=$this->sessionUser->dameDatosUsuario($data);
        echo $this->printer->render("view/turnoView.html", $data);
    }
    //Action Formulario para solicitar turno
    public function procesarTurno(){
        $this->sessionUser->validarLogin();
        if (isset($_POST['id_centro'])){
        $data = array(
            'id_centro' => $_POST['id_centro'],
            'fecha' => $_POST['fecha'],
            'id_usuario' => $_SESSION["id_usuario"],
        );

        $id_turno=$this->medicoModel->crearTurno($data);

        $destinatario=$_SESSION["email"];
        $turno=$this->medicoModel->datosTurnoActual($_SESSION["id_usuario"]);
        $turno=$turno[0];
        $mensaje='<h2>Turno</h2>';
        $mensaje.="Fecha: $turno[fecha]<br>";
        $mensaje.="Centro: $turno[nombre], $turno[descripcion]<br>";
        $mensaje.="<a href='http://localhost/mvc-gaucho-rocket/medico/realizarChequeo?id_turno=$id_turno'>Ver Turno</a>";
        $data=$this->mailHelper->enviarMail("Turno",$mensaje,$destinatario);
        if ($data=="Enviado"){
            $msg["type"]="info";
            $msg["mensaje"]="Enviamos un email con el turno a <b>$destinatario</b>";
        }else{
            $msg["type"]="danger";
            $msg["mensaje"]="Error al enviar el Email <b><a href='http://localhost/mvc-gaucho-rocket/medico/realizarChequeo?id_turno=$id_turno'>Ver Turno</a></b>";
        }
        header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
        }else{$this->show();}
    }

    //Link de email vista mi turno
    public function realizarChequeo(){
        $dataSession=$this->sessionUser->validarLogin();
        if (isset($_GET['id_turno'])){
            $id_turno=$this->medicoModel->dameTurno($_GET['id_turno']);
        }else{
            $id_turno=$this->medicoModel->datosTurnoActual($_SESSION["id_usuario"]);
        }
        $data=[];
        $data=$this->sessionUser->dameDatosUsuario($data);
        $data["mensaje"] = "Turno realizado con éxito.";
        $data["turno"] = $id_turno;
        $data["session"]=$dataSession;

        echo $this->printer->render("view/turnoConfirmadoView.html", $data);
    }

    //Action Mi turno
    public function procesarChequeoMedico(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["idTurno"])){
            $destinatario=$_SESSION["email"];
            $codigoViajero=$this->asignarNivel();
            $turno=$this->medicoModel->datosTurnoActual($_SESSION["id_usuario"]);
            $turno=$turno[0];
            $mensaje='<h2>Chequeo médico</h2>';
            $mensaje.="Fecha: $turno[fecha]<br>";
            $mensaje.="Centro: $turno[nombre], $turno[descripcion]<br>";
            $mensaje.="<b>Codigo de viajero: $codigoViajero</b><br>";
            $mensaje.="<a href='http://localhost/mvc-gaucho-rocket/medico/resultadoChequeo'>Ver Resultado</a>";
            $data=$this->mailHelper->enviarMail("Chequeo medico",$mensaje,$destinatario);
            if ($data=="Enviado"){
                $msg["type"]="info";
                $msg["mensaje"]="Enviamos un email con el resultado del chequeo medico a <b>$destinatario</b>";
                $this->medicoModel->actualizarEstado($_POST["idTurno"], "Chequeo realizado");
                $this->medicoModel->asignarNivel($_POST["idTurno"], $codigoViajero);
            }else{
                $msg["type"]="danger";
                $msg["mensaje"]="Error al procesar el chequeo medico!";
            }
            header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
        }else{$this->show();}
    }

    //Link email resultado
    public function resultadoChequeo(){
        $dataSession=$this->sessionUser->validarLogin();
        $data=[];
        $data=$this->sessionUser->dameDatosUsuario($data);
        $data["turno"]=$this->medicoModel->datosTurnoActual($_SESSION["id_usuario"]);
        $data["mensaje"]="Chequeo medico realizado con exito!";
        $data["session"]=$dataSession;
        echo $this->printer->render("view/resultadoConfirmadoView.html", $data);
    }

    public function bajaTurno(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["idTurno"])){
            $this->medicoModel->actualizarEstado($_POST["idTurno"], "Baja");
            header("Location: /mvc-gaucho-rocket/login");
        }
    }
    public function asignarNivel(){
        return rand(1,3);
    }
}
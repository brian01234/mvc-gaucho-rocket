<?php

class ReservaController{
    private $reservaModel;
    private $printer;
    private $mailHelper;
    private $sessionUser;
	
    public function __construct($reservaModel,$printer, $mailHelper, $sessionUser){
		$this->reservaModel = $reservaModel;
        $this->printer=$printer;
        $this->mailHelper=$mailHelper;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $dataSession=$this->sessionUser->validarLogin();
        $resultado=$this->reservaModel->tieneCodigoDeViajero($_SESSION["id_usuario"], "Chequeo realizado");
        if ($resultado!=[]){
            $data["session"]=$dataSession;
            $data["destinos"] = $this->reservaModel->dameDestinos();
            echo $this->printer->render("view/solicitarReservaView.html", $data);
        }else{
            header("Location: /mvc-gaucho-rocket/medico");
        }
    }

    //Action Formulario para solicitar Reserva
	public function procesarDisponibilidad(){
        if (isset($_SESSION["id_usuario"])){
            $dataSession=$_SESSION["email"];
            $resultado=$this->reservaModel->tieneCodigoDeViajero($_SESSION["id_usuario"], "Chequeo realizado");
            if ($resultado==[]){header("Location: /mvc-gaucho-rocket/medico");}
        }else{$dataSession="";}

        if (isset($_GET["origen"]) and isset($_GET["destino"])){
            $data=$this->reservaModel->buscarDisponibilidadDeVuelo($_GET["origen"],$_GET["destino"]);
            if ($data!=[]){
                $data["vuelos"]=$data;
                $data["titulo"]=$this->titulo("Vuelos disponibles", $_GET["origen"], $_GET["destino"]);
            }else{
                $data["titulo"]=$this->titulo("No hay disponibilidad", $_GET["origen"], $_GET["destino"]);
                $data["vuelos"]=$this->reservaModel->vuelosDisponibles();
                $data["vuelosDisponibles"]="Vuelos disponibles";
            }
            $data["session"]=$dataSession;
            echo $this->printer->render("view/vuelosView.html", $data);
        }else{
            $this->show();
        }
    }

    //Action lista de vuelos disponibles
    public function procesarDisponibilidadVuelo(){
        $dataSession=$this->sessionUser->validarLogin();
        if (isset($_POST["vuelo"])){
            $resultado=$this->validarCodigo($_POST["vuelo"]);
            if ($resultado==3 or $resultado==1){
                $this->seleccionarCabinas();
            }else{
                $origen=$_POST["origen"];
                $destino=$_POST["destino"];
                $resultadoChequeo=$this->reservaModel->tieneCodigoDeViajero($_SESSION["id_usuario"], "Chequeo realizado");
                $data["nivel"]=$resultadoChequeo[0]["nivel"];
                $data["titulo"]=$this->titulo("Con ese nivel solo puede reservar vuelos con equipos de:", $origen, $destino);
                $data["equipos"]=$resultado;
                $data["session"]=$dataSession;
                echo $this->printer->render("view/mensajeVuelosView.html", $data);
            }
        }
        else{
            $this->show();
        }
    }

    public function seleccionarCabinas(){
        $dataSession=$this->sessionUser->validarLogin();
        if (isset($_POST["vuelo"])){
        $equipo=$this->reservaModel->datosVuelo($_POST["vuelo"]);
        $data["cabina"]=$this->reservaModel->dameCabinasDelEquipo($equipo[0]["id_equipo"]);
        $data["datosVuelo"]=$equipo[0]["nombre_origen"]."-".$equipo[0]["nombre_destino"];
        $data["idOrigen"]=$equipo[0]["id_origen"];
        $data["idDestino"]=$equipo[0]["id_destino"];
        $data["servicio"]=$this->reservaModel->dameServiciosDeABordo();
        $data["vuelo"]=$_POST["vuelo"];
        $data["session"]=$dataSession;
        echo $this->printer->render("view/seleccionarCabinaView.html", $data);
        }else{
            $this->show();
        }
    }

    //Action Seleccionar cabina
    public function seleccionarAsiento(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["vuelo"])) {
            $this->mostrarAsientos($_POST["vuelo"],$_POST["cabina"],$_POST["servicio"], "");
        }
        else{
            $this->show();
        }
    }
    public function mostrarAsientos($vuelo,$cabina,$servicio,$msg){
        $dataSession=$this->sessionUser->validarLogin();
        $disponible=$this->reservaModel->asientosDisponibles($vuelo, $cabina);
        if (($disponible[0]["cant"])==0){
            $data["noDisponible"]="No hay disponibilidad de asientos en esta cabina.";
        }
        $data["mensaje"]=$msg;
        $data["vuelo"] =$this->reservaModel->datosVuelo($vuelo);
        $data["cabina"]=$this->reservaModel->cabina($cabina);
        $data["servicio"] =$this->reservaModel->servicio($servicio);
        $data["asientos"]=$this->reservaModel->asientos($vuelo, $cabina);
        $data["session"]=$dataSession;
        echo $this->printer->render("view/seleccionarAsientoView.html", $data);
    }
    //Action Seleccionar asiento en el equipo
    public function realizarReserva(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["asiento"])){
            $disponible=$this->reservaModel->estadoAsiento($_POST["asiento"]);
            if ($disponible[0]["estado"]=="disponible"){
                $this->reservar($_POST["vuelo"],$_POST["cabina"],$_POST["servicio"], $_POST["asiento"], "");
            }else{
                $msg="El asiento no esta disponible.";
                $this->mostrarAsientos($_POST["vuelo"],$_POST["cabina"],$_POST["servicio"], $msg);
            }
        }else{$this->show();}
    }
    public function reservar($vuelo,$cabina,$servicio, $idAsiento, $estadoListaEspera){
        $this->sessionUser->validarLogin();
        $vuelo=$this->reservaModel->datosVuelo($vuelo);
        $codigoReserva=$vuelo[0]["id_equipo"]."-".time();
        $asiento=$this->reservaModel->getAsiento($idAsiento);
        if ($asiento!=[]) {
            $nAsiento=$asiento[0]["asiento"];
        }else{$nAsiento="0";}
        $data = array(
            'idVuelo' => $vuelo[0]["id_vuelo"],
            'idCabina' => $cabina,
            'codigoReserva' => $codigoReserva,
            'fecha' => $vuelo[0]["fecha"],
            'usuario' => $_SESSION["id_usuario"],
            'asiento' => $idAsiento,
            'idServicio' => $servicio,
            'idOrigen' => $vuelo[0]["id_origen"],
            'idDestino' => $vuelo[0]["id_destino"],
            'hora' => $vuelo[0]["hora"],
            'idEquipo' => $vuelo[0]["id_equipo"],
            'nAsiento' => $nAsiento,
            'lista_de_espera' => $estadoListaEspera,
            'precio' => $vuelo[0]["precio"],
        );

        $this->reservaMail($data, $estadoListaEspera);
    }
    public function irAlistaDeEspera(){
        if (isset($_POST["vuelo"])){
            $this->reservar($_POST["vuelo"],$_POST["cabina"],$_POST["servicio"], "0", "En espera");
        }else{$this->show();}
    }
    public function reservaMail($reserva, $estadoListaEspera){
        $this->sessionUser->validarLogin();
        $destinatario=$_SESSION["email"];
        $this->reservaModel->realizarReserva($reserva);
        $dataReserva=$this->reservaModel->reservaActual($_SESSION["id_usuario"]);
        $dataReserva=$dataReserva[0];
        $mensaje='<h2>Reserva</h2>';
        $mensaje.="Origen: $dataReserva[origen]<br>";
        $mensaje.="Destino: $dataReserva[destino]<br>";
        $mensaje.="Fecha: $dataReserva[fecha_reserva]<br>";
        $mensaje.="Hora: $dataReserva[hora]<br>";
        $mensaje.="Cabina: $dataReserva[cabina]<br>";
        $mensaje.="Servicio: $dataReserva[servicio]<br>";

        if ($estadoListaEspera=="En espera"){
            $mensaje.="<b>Reserva en lista de espera</b><br>";
        }else{
            $mensaje.="Asiento ($dataReserva[nAsiento])<br>";
            $mensaje.="Estado pago: <span style='color: #2ebcfc;'>$dataReserva[estado_pago]</span><br>";
        }

        $mensaje.="<a href='http://localhost/mvc-gaucho-rocket/misReservas'>Ver Reserva</a>";
        $data=$this->mailHelper->enviarMail("Reserva",$mensaje,$destinatario);
        if ($data=="Enviado"){
            $msg["type"]="info";
            $msg["mensaje"]="Enviamos un email con la reserva a <b>$destinatario</b>";
        }else{
            $msg["type"]="info";
            $msg["mensaje"]="Error al enviar el email <b><a href='http://localhost/mvc-gaucho-rocket/misReservas'>Ver Reserva</a></b>";
        }
        header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
    }

    public function validarCodigo($idVuelo){
        $resultado=$this->reservaModel->tieneCodigoDeViajero($_SESSION["id_usuario"], "Chequeo realizado");

        $nivelUsuario=$resultado[0]["nivel"];
        $equipo=$this->reservaModel->datosVuelo($idVuelo);
        if($nivelUsuario==1 or $nivelUsuario==2){
            $resultado=$this->reservaModel->equipoNivel_1_2($equipo[0]["id_equipo"]);
            if ($resultado!=[]){
                return 1;
            }else{
                return $this->reservaModel->listaEquiposNivel_1_2();
            }
        }
        elseif ($nivelUsuario==3){
            return 3;
        }
    }
    //MensajeVuelosView
    public function titulo($titulo, $origen, $destino){
        $data["titulo"]=$titulo;
        $data["origenDestino"]=$this->reservaModel->nombreDestino($origen)." - ".$this->reservaModel->nombreDestino($destino);
        $data["idOrigen"]=$origen;
        $data["idDestino"]=$destino;
        return $data;
    }
}
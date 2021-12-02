<?php

class misReservasController{
    private $misReservasModel;
    private $printer;
    private $pdfHelper;
    private $qrHelper;
    private $mailHelper;
    private $sessionUser;
    public function __construct($misReservasModel, $printer, $pdfHelper, $qrHelper, $mailHelper, $sessionUser){
        $this->misReservasModel=$misReservasModel;
        $this->printer=$printer;
        $this->pdfHelper=$pdfHelper;
        $this->qrHelper=$qrHelper;
        $this->mailHelper=$mailHelper;
        $this->sessionUser=$sessionUser;
    }
    public function show(){
        $dataSession=$this->sessionUser->validarLogin();
        $data["reservas"]=$this->misReservasModel->misReservas($_SESSION["id_usuario"], "");
        $listaDeEspera=$this->misReservasModel->misReservas($_SESSION["id_usuario"], "En espera");
        if ($listaDeEspera!=[]){$data["listaDeEspera"]="Reservas en lista de espera";$data["reservasEnEspera"]=$listaDeEspera;}
        $data["session"]=$dataSession;
        echo $this->printer->render("view/misReservasView.html", $data);
    }

    public function reserva(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["reserva"])){
        $reserva=$_POST["reserva"];
        $estadoPago=$this->misReservasModel->miReserva($_SESSION["id_usuario"], $reserva);
            if ($estadoPago[0]["estado_pago"]=="Pago realizado" or $estadoPago[0]["estado_pago"]=="Baja"){
                $dataPdf=$this->prepararPdf($_SESSION["id_usuario"], $reserva);
                $this->pdfHelper->printPdf("reserva", $dataPdf);
            }else{
                $this->pagoReserva();
            }
        }else{$this->show();}
    }

    public function prepararPdf($idUsuario,$reserva){
        $data=$this->misReservasModel->miReserva($idUsuario, $reserva);
        $dataQr=$this->datosReservaQr($data[0]);
        $dataPdf=$this->datosReservaPdf($data[0]);

        $this->qrHelper->createQrImage($dataQr, "public/qr.png");
        return $dataPdf;
    }

    public function datosReservaQr($pk){
        $data="$pk[codigo_reserva] \n";
        $data.="Nombre: $_SESSION[nombre] \n";
        $data.="Apellido: $_SESSION[apellido] \n";
        $data.="Email: $_SESSION[email] \n";
        $data.="$pk[origen] - $pk[destino] $pk[fecha_reserva] $pk[hora] \n";
        $data.="Asiento ($pk[nAsiento]) \n";
        $data.="Cabina $pk[cabina] \n";
        $data.="Servicio $pk[servicio] \n";
        $data.="Equipo ($pk[tipoEquipo]) $pk[modeloEquipo] \n";
        $data.="Estado reserva: $pk[estado] \n";
        $data.="Estado pago: $pk[estado_pago] \n";
        return $data;
    }
    public function datosReservaPdf($pk){
        if ($pk["estado_pago"]=="Pago realizado"){
            $color="green";
        }else{$color="red";}
        $data="<div style='padding:30px; width: 300px;margin: auto;border: solid 1px black;'><br>
               <img style='width:40%;margin-left: 30%;' src='http://localhost/mvc-gaucho-rocket/public/imagenes/logo.png'>
               <h1>Boarding-Pass </h1><h2>$pk[codigo_reserva]</h2>
               <h4>
               Nombre: $_SESSION[nombre]<br>
               Apellido: $_SESSION[apellido]<br>
               Email: $_SESSION[email]<hr>
               $pk[origen] - $pk[destino] $pk[fecha_reserva] $pk[hora]<br>
               Asiento ($pk[nAsiento])<br>
               Cabina $pk[cabina]<br>
               Servicio $pk[servicio]<br>
               Equipo ($pk[tipoEquipo]) $pk[modeloEquipo]<br>
               Estado reserva: $pk[estado]<br>
               Estado pago: <span style='color: $color;'>$pk[estado_pago]</span></h4>
               <img src='http://localhost/mvc-gaucho-rocket/public/qr.png'></div>";
        return $data;
    }


    /*public function pagoReserva(){
        if (isset($_SESSION["id_usuario"])) {
            if (isset($_POST["reserva"])) {
                $data=$this->misReservasModel->miReserva($_SESSION["id_usuario"], $_POST["reserva"]);
                $this->pagar($data);
            } else {
                $data = $this->misReservasModel->reservaActual($_SESSION["id_usuario"]);
                $this->pagar($data);
            }
        }else{
            header("Location: /mvc-gaucho-rocket/login");
        }
    }*/
    //Reemplazado por
    public function pagoReserva(){
        $dataSession=$this->sessionUser->validarLogin();
        if (isset($_POST["reserva"])) {
            $data = $this->misReservasModel->miReserva($_SESSION["id_usuario"], $_POST["reserva"]);
            if ($data[0]["estado_pago"]=="En espera"){
                $data["reserva"]=$data;
                $data["session"]=$dataSession;
                echo $this->printer->render("view/pagoReservaView.html", $data);
            }else{$this->show();}
        } else {
            $this->show();
        }
    }

    //Action vista pago reserva
    public function boardingPass(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["pago"])){
            $this->boardingPassMail($_POST["reserva"], $_POST["pago"]);
        }else{
            $this->show();
        }
    }

    public function boardingPassMail($reserva, $pago){
        $this->sessionUser->validarLogin();
        $destinatario=$_SESSION["email"];
        $dataReserva=$this->misReservasModel->miReserva($_SESSION["id_usuario"], $reserva);
        $dataReserva=$dataReserva[0];
        $mensaje='<h2>Boarding-Pass</h2>';
        $mensaje.="<h2>$reserva</h2>";
        $mensaje.="Origen: $dataReserva[origen]<br>";
        $mensaje.="Destino: $dataReserva[destino]<br>";
        $mensaje.="Fecha: $dataReserva[fecha_reserva]<br>";
        $mensaje.="Hora: $dataReserva[hora]<br>";
        $mensaje.="Cabina: $dataReserva[cabina]<br>";
        $mensaje.="Servicio: $dataReserva[servicio]<br>";
        $mensaje.="Asiento ($dataReserva[nAsiento])<br>";
        $mensaje.="Estado pago: <span style='color: green;'>Pago realizado</span><br>";
        $this->misReservasModel->pagarReserva($reserva, $pago);
        $this->pdfHelper->createPdf($this->prepararPdf($_SESSION["id_usuario"],$reserva));
        $data=$this->mailHelper->enviarMailPDF("Boarding-Pass",$mensaje,$destinatario, $reserva);
        if ($data=="Enviado"){
            $msg["type"]="success";
            $msg["mensaje"]="Pago realizado con éxito!<br>Te enviamos un email con el <i><b>Boarding-Pass</b></i> a <b>$destinatario</b>";
        }else{
            $msg["type"]="success";
            $msg["mensaje"]="Pago realizado con éxito!<br>Error al enviar el email <b><a href='http://localhost/mvc-gaucho-rocket/misReservas'>Ver Reserva</a></b>";
        }
        header("Location: /mvc-gaucho-rocket/mensajeMail?msg=$msg[mensaje]&type=$msg[type]");
    }

//Baja reserva
    public function reservaAjax(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["reserva"])){
            $data=$this->misReservasModel->miReserva($_SESSION["id_usuario"], $_POST["reserva"]);
            $pk=$data[0];
            $data = array(
                'codigoReserva' => $pk["codigo_reserva"],
                'dataReserva' => "$pk[origen] - $pk[destino] $pk[fecha_reserva] $pk[hora] asiento ($pk[nAsiento])<br>Estado reserva: $pk[estado]<br>Estado pago: $pk[estado_pago]",
                'estado' => $pk["estado"],
            );
            echo  json_encode($data);
        }else{
            $this->show();
        }
    }
    public function bajaReserva(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["reserva"])){
            $reservaBaja=$this->misReservasModel->miReserva($_SESSION["id_usuario"], $_POST["reserva"]);
            $this->misReservasModel->updateEstadoReserva("Baja", $reservaBaja[0]["id_reserva"]);
            $this->misReservasModel->updateEstadoAsiento("disponible", $reservaBaja[0]["id_asiento"], "success");
            $disponible=$this->misReservasModel->asientosDisponibles($reservaBaja[0]["id_vuelo"], $reservaBaja[0]["id_cabina"]);
            $disponible=$disponible[0]["cant"];
            if (($disponible-1)==0){
                $this->asignarAsiento($reservaBaja[0]);
            }
            $this->show();
        }else{
            $this->show();
        }
    }
    public function asignarAsiento($reservaBaja){
        //buscar en la lista de espera
        $alta=$this->misReservasModel->listaDeEspera($reservaBaja["id_vuelo"], $reservaBaja["id_cabina"], "En espera");
        if ($alta!=[]){
            //Asignar asiento
            $data = array(
                'asiento' => $reservaBaja["id_asiento"],
                'nAsiento' => $reservaBaja["nAsiento"],
                'estado_lista_espera' => "",
            );
            $this->misReservasModel->asignarAsiento($alta[0]["id_reserva"], $data);
            $this->misReservasModel->updateEstadoAsiento("ocupado", $data["asiento"], "danger");

            //Enviar email a usuario en espera
            $this->enviarEmailAlUsuarioEnListaDeEspera($alta[0]);
        }
    }

    public function enviarEmailAlUsuarioEnListaDeEspera($reserva){
        $destinatario=$reserva["email"];
        $dataReserva=$this->misReservasModel->miReserva($reserva["id_usuario"], $reserva["codigo_reserva"]);
        $dataReserva=$dataReserva[0];
        $mensaje='<h4>Su reserva dejó de estar en lista de espera</h4>';
        $mensaje.="Origen: $dataReserva[origen]<br>";
        $mensaje.="Destino: $dataReserva[destino]<br>";
        $mensaje.="Fecha: $dataReserva[fecha_reserva]<br>";
        $mensaje.="Hora: $dataReserva[hora]<br>";
        $mensaje.="Cabina: $dataReserva[cabina]<br>";
        $mensaje.="Servicio: $dataReserva[servicio]<br>";

        $mensaje.="Asiento ($dataReserva[nAsiento])<br>";
        $mensaje.="Estado pago: <span style='color: red;'>$dataReserva[estado_pago]</span><br>";

        $mensaje.="<a href='http://localhost/mvc-gaucho-rocket/misReservas'>Ver Reserva</a>";
        $this->mailHelper->enviarMail("Reserva",$mensaje,$destinatario);
    }

    public function bajaListaDeEspera(){
        $this->sessionUser->validarLogin();
        if (isset($_POST["reserva"])){
            $this->misReservasModel->updateEstadoListaDeEspera($_POST["reserva"], "Baja");
            $this->show();
        }else{
            $this->show();
        }
    }
}
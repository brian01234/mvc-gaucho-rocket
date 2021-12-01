<?php

class ReservaModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }
 
     public function dameDestinos(){
		 $SQL = "SELECT * FROM  destinos ";
        return $this->database->query($SQL);
    }
	
	     public function dameCabinas(){
		 $SQL = "SELECT * FROM  cabina ";
        return $this->database->query($SQL);
    }

    public  function codigo(){
        return $this->database->query("SELECT * FROM turno");
    }
  public function nombreDestino($id){
        $destino=$this->database->query("SELECT nombre FROM destinos WHERE id_destino='$id'");
        return $destino[0]["nombre"];
  }
  public function buscarDisponibilidadDeVuelo($origen,$destino){
        return $this->database->query("SELECT V.*, Origen.nombre AS nombre_origen, Destino.nombre AS nombre_destino, E.tipo AS tipo_equipo, TE.descripcion AS nombre_tipo_equipo FROM vuelo V JOIN destinos Origen ON V.id_origen=Origen.id_destino JOIN destinos Destino ON V.id_destino=Destino.id_destino JOIN equipo E ON V.id_equipo=E.matricula JOIN tipo_de_equipo TE ON E.tipo=TE.tipo
                                      WHERE Origen.id_destino='$origen' AND Destino.id_destino='$destino';");
    }
  public function vuelosDisponibles(){
      return $this->database->query("SELECT V.*, Origen.nombre AS nombre_origen, Destino.nombre AS nombre_destino, E.tipo AS tipo_equipo, TE.descripcion AS nombre_tipo_equipo FROM vuelo V JOIN destinos Origen ON V.id_origen=Origen.id_destino JOIN destinos Destino ON V.id_destino=Destino.id_destino JOIN equipo E ON V.id_equipo=E.matricula JOIN tipo_de_equipo TE ON E.tipo=TE.tipo;");
  }
  public function datosVuelo($idVuelo){
        return $this->database->query("SELECT V.*, Origen.nombre AS nombre_origen, Destino.nombre AS nombre_destino, E.tipo AS tipo_equipo, TE.descripcion AS nombre_tipo_equipo FROM vuelo V JOIN destinos Origen ON V.id_origen=Origen.id_destino JOIN destinos Destino ON V.id_destino=Destino.id_destino JOIN equipo E ON V.id_equipo=E.matricula JOIN tipo_de_equipo TE ON E.tipo=TE.tipo WHERE V.id_vuelo='$idVuelo';");
  }
  public function equipoNivel_1_2($idEquipo){
        return $this->database->query("SELECT * FROM equipo WHERE
                                      (tipo LIKE 'O' OR tipo LIKE 'BA') AND matricula LIKE '$idEquipo';");
  }
  public function listaEquiposNivel_1_2(){
        return $this->database->query("SELECT * FROM tipo_de_equipo WHERE
                                      (tipo LIKE 'O' OR tipo LIKE 'BA')");
  }
  public function dameServiciosDeABordo(){
        return $this->database->query("SELECT * FROM servicio_de_a_bordo;");
  }
  public function dameCabinasDelEquipo($matricula){
        return $this->database->query("SELECT * FROM equipo_cabina EC JOIN cabina ON EC.id_cabina=cabina.id_cabina WHERE matricula LIKE '$matricula';");
  }
  public function asientos($idVuelo, $idCabina){
        return $this->database->query("SELECT * FROM asiento WHERE id_vuelo='$idVuelo' AND id_cabina='$idCabina';");
  }
  public function estadoAsiento($id){
        return $this->database->query("SELECT * FROM asiento WHERE id_asiento='$id'");
  }
  public function realizarReserva($pk){
        $this->database->queryInsertUpdate("INSERT INTO reserva(id_vuelo, id_cabina,estado, codigo_reserva, fecha_reserva, id_usuario, id_asiento, id_servicio) VALUES
        ('$pk[idVuelo]','$pk[idCabina]','Activo','$pk[codigoReserva]','$pk[fecha]','$pk[usuario]', $pk[asiento], $pk[idServicio]);");
        $this->ocuparAsiento($pk["asiento"]);
  }
  public function ocuparAsiento($idAsiento){
        $this->database->queryInsertUpdate("UPDATE asiento SET estado='ocupado' WHERE id_asiento='$idAsiento';");
  }
  public function cabina($idCabina){
        return $this->database->query("SELECT * FROM cabina WHERE id_cabina='$idCabina'");
  }
  public function servicio($idServicio){
        return $this->database->query("SELECT * FROM servicio_de_a_bordo WHERE id_servicio='$idServicio'");
    }
}
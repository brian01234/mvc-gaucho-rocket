<?php

class misReservasModel{
    private $dataBase;
    public function __construct($dataBase){
        $this->dataBase=$dataBase;
    }
    public function misReservas($idUsuario){
       return $this->dataBase->query("SELECT V.id_vuelo AS idVuelo, codigo_reserva, fecha, Origen.nombre AS origen, Destino.nombre AS destino,
 A.asiento AS asiento, cabina.nombre AS cabina, V.hora AS hora, SA.nombre AS nombre_servicio, TE.tipo AS tipo_equipo, TE.descripcion AS nombre_tipo_equipo
 FROM reserva R JOIN vuelo V ON V.id_vuelo=R.id_vuelo
					  JOIN destinos Origen ON V.id_origen=Origen.id_destino
                      JOIN destinos Destino ON V.id_destino=Destino.id_destino
                      JOIN cabina ON R.id_cabina=cabina.id_cabina
                      JOIN asiento A ON R.id_asiento=A.id_asiento
                      JOIN servicio_de_a_bordo SA ON R.id_servicio=SA.id_servicio
                      JOIN equipo E ON V.id_equipo=E.matricula
                      JOIN tipo_de_equipo TE ON E.tipo=TE.tipo
                      WHERE id_usuario='$idUsuario';");
    }
}
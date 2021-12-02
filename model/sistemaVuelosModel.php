<?php

class sistemaVuelosModel{
    private $dataBase;
    public function __construct($dataBase){
        $this->dataBase=$dataBase;
    }

    public function vuelosDisponibles(){
        return $this->dataBase->query("SELECT V.*, Origen.nombre AS nombre_origen, Destino.nombre AS nombre_destino, E.tipo AS tipo_equipo, TE.descripcion AS nombre_tipo_equipo, E.modelo AS modelo_equipo FROM vuelo V JOIN destinos Origen ON V.id_origen=Origen.id_destino JOIN destinos Destino ON V.id_destino=Destino.id_destino JOIN equipo E ON V.id_equipo=E.matricula JOIN tipo_de_equipo TE ON E.tipo=TE.tipo ORDER BY id_vuelo ASC;");
    }
    public function eliminarVuelo($idVuelo){
        $this->eliminarAsientos($idVuelo);
        $this->dataBase->queryInsertUpdate("DELETE FROM vuelo WHERE id_vuelo='$idVuelo';");
    }
    public function eliminarAsientos($idVuelo){
        $this->dataBase->queryInsertUpdate("DELETE FROM asiento WHERE id_vuelo='$idVuelo';");
    }

}
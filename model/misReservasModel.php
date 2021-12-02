<?php

class misReservasModel{
    private $dataBase;
    public function __construct($dataBase){
        $this->dataBase=$dataBase;
    }
    public function misReservas($idUsuario, $estadoListaEspera){
       return $this->dataBase->query("SELECT R.*,E.modelo AS modeloEquipo, E.tipo AS tipoEquipo, Origen.nombre AS origen, Destino.nombre AS destino, C.nombre AS cabina, S.nombre AS servicio
FROM reserva R JOIN equipo E ON R.id_equipo=E.matricula
JOIN destinos Origen ON R.id_origen=Origen.id_destino
JOIN destinos Destino ON R.id_destino=Destino.id_destino
JOIN cabina C ON R.id_cabina=C.id_cabina
JOIN servicio_de_a_bordo S ON R.id_servicio=S.id_servicio
WHERE R.id_usuario='$idUsuario'
AND R.lista_de_espera like '$estadoListaEspera'
ORDER BY R.id_reserva DESC;");
    }
    public function miReserva($idUsuario, $codigo){
        return $this->dataBase->query("SELECT R.*,E.modelo AS modeloEquipo, E.tipo AS tipoEquipo, Origen.nombre AS origen, Destino.nombre AS destino, C.nombre AS cabina, S.nombre AS servicio
FROM reserva R JOIN equipo E ON R.id_equipo=E.matricula
JOIN destinos Origen ON R.id_origen=Origen.id_destino
JOIN destinos Destino ON R.id_destino=Destino.id_destino
JOIN cabina C ON R.id_cabina=C.id_cabina
JOIN servicio_de_a_bordo S ON R.id_servicio=S.id_servicio
                      WHERE id_usuario='$idUsuario' AND codigo_reserva='$codigo';");
    }
    public function reservaActual($idUsuario){
        return $this->dataBase->query("SELECT R.*,E.modelo AS modeloEquipo, E.tipo AS tipoEquipo, Origen.nombre AS origen, Destino.nombre AS destino, C.nombre AS cabina, S.nombre AS servicio
FROM reserva R JOIN equipo E ON R.id_equipo=E.matricula
JOIN destinos Origen ON R.id_origen=Origen.id_destino
JOIN destinos Destino ON R.id_destino=Destino.id_destino
JOIN cabina C ON R.id_cabina=C.id_cabina
JOIN servicio_de_a_bordo S ON R.id_servicio=S.id_servicio WHERE R.id_reserva = (SELECT MAX(R2.id_reserva) FROM reserva R2 WHERE R2.id_usuario='$idUsuario');");
    }
    public function pagarReserva($codReserva, $estado){
        $this->dataBase->queryInsertUpdate("UPDATE reserva SET estado_pago='$estado', type_reserva='success' WHERE codigo_reserva='$codReserva'");
    }
    public function updateEstadoReserva($estado, $idReserva){
        $this->dataBase->queryInsertUpdate("UPDATE reserva SET estado='$estado', estado_pago='$estado', type_reserva='danger' WHERE id_reserva='$idReserva'");
    }
    public function updateEstadoAsiento($estado, $idAsiento, $type){
        $this->dataBase->queryInsertUpdate("UPDATE asiento SET estado='$estado', type='$type' WHERE id_asiento='$idAsiento'");
    }
    public function asientosDisponibles($idVuelo, $idCabina){
        return $this->dataBase->query("SELECT COUNT(A.id_asiento) AS cant FROM asiento A
                                WHERE A.estado LIKE 'disponible'
                                AND A.id_vuelo='$idVuelo' AND A.id_cabina='$idCabina';");
    }
    public function listaDeEspera($idVuelo, $idCabina, $estadoListaEspera){
        return $this->dataBase->query("SELECT * FROM reserva R JOIN usuario U ON R.id_usuario=U.id
                                      WHERE R.id_reserva=(SELECT MIN(R2.id_reserva)
                                      FROM reserva R2
                                      WHERE R2.id_vuelo='$idVuelo' AND R2.id_cabina='$idCabina'
                                      AND R2.lista_de_espera LIKE '$estadoListaEspera');");
    }
    public function updateEstadoListaDeEspera($idReserva, $estado){
        $this->dataBase->queryInsertUpdate("UPDATE reserva SET 
                                lista_de_espera='$estado'
                                 WHERE id_reserva='$idReserva';");
    }
    public function asignarAsiento($idReserva, $pk){
        $this->dataBase->queryInsertUpdate("UPDATE reserva SET 
                                            id_asiento='$pk[asiento]',
                                            nAsiento='$pk[nAsiento]',
                                            lista_de_espera='$pk[estado_lista_espera]'
                                            WHERE id_reserva='$idReserva';");
    }
}
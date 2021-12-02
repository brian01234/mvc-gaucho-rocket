<?php
class sistemaGraficoModel{
    private $database;
    public function __construct($database){
        $this->database=$database;
    }

    public function cabinasVendidas(){
        return $this->database->query("SELECT C.*, COUNT(R.id_cabina) AS cantidad
                                      FROM reserva R JOIN cabina C ON R.id_cabina=C.id_cabina
                                      WHERE R.estado_pago LIKE 'Pago realizado' GROUP BY R.id_cabina;");
    }

    public function usuariosQueReservaron(){
        return $this->database->query("SELECT U.id AS id, U.nombre AS nombre, U.email AS email, COUNT(R.id_usuario) AS cantidad
                                    FROM reserva R JOIN usuario U ON R.id_usuario=U.id
                                    WHERE R.estado_pago LIKE 'Pago realizado' GROUP BY R.id_usuario;");
    }
}
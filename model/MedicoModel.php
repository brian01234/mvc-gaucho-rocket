<?php

class MedicoModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }
 
     public function dameCentros(){
		 $SQL = "SELECT * FROM  centros ";
        return $this->database->query($SQL);
    }
	
	
	     public function dameResultado($id_turno){
		 $SQL = "SELECT * FROM  turnos where id_turno=$id_turno ";
		 return $this->database->query($SQL);
		}
	
	
	 
	
	
	
	    public function crearTurno($data){
        $fecha=$data['fecha'];
        $id_centro=$data['id_centro']; 
        $id_usuario=$data['id_usuario'];
		$nivel=rand(1,3);

       return $this->database->queryInsertUpdateConReturnId("INSERT INTO turnos (id_usuario,id_centro,fecha,estado,nivel) VALUES ('$id_usuario','$id_centro','$fecha',1,'$nivel')");
		
		
		
    }




    public function tieneTurnoActual($id, $fechaActual){
        return $this->database->query("SELECT * FROM turnos WHERE id_usuario='$id'");
    }
	
	
/*

CREATE TABLE `centros` ( `id_centro` INT(9) NOT NULL , `nombre` VARCHAR(100) NULL , `descripcion` VARCHAR(100) NULL , `turnos` INT(9) NULL , PRIMARY KEY (`id_centro`)) ENGINE = InnoDB;


ALTER TABLE `centros` CHANGE `id_centro` `id_centro` INT(9) NOT NULL AUTO_INCREMENT;

INSERT INTO `centros` (`id_centro`, `nombre`, `descripcion`, `turnos`) VALUES ('', 'Buenos Aires', 'Calle Buenos Aires', '300'), ('', 'Shanghái', 'Calle Shanghái', '210'), ('', 'Ankara', 'Calle Ankara', '200');



CREATE TABLE `gauchorocket1`.`turnos` ( `id_turno` INT(9) NOT NULL AUTO_INCREMENT , `id_usuario` INT(9) NULL , `id_centro` INT(9) NULL , `fecha` DATE NULL , PRIMARY KEY (`id_turno`)) ENGINE = InnoDB;


ALTER TABLE `turnos` ADD `estado` VARCHAR(20) NULL AFTER `fecha`, ADD `nivel` INT(9) NULL AFTER `estado`;


*/


}
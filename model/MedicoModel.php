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
	
	
 
	 
	
		public function dameTurno($id_turno){
		 $SQL = "SELECT turnos.*,centros.nombre,centros.descripcion
			FROM  turnos
			left join centros on (turnos.id_centro=centros.id_centro) 
			where turnos.id_turno=$id_turno 
		 ";
		 return $this->database->query($SQL);
		}
	
	
	
	    public function crearTurno($data){
        $fecha=$data['fecha'];
        $id_centro=$data['id_centro']; 
        $id_usuario=$data['id_usuario'];
		$nivel=null;

       return $this->database->queryInsertUpdateConReturnId("INSERT INTO turnos (id_usuario,id_centro,fecha,estado,nivel) VALUES ('$id_usuario','$id_centro','$fecha','En espera','$nivel')");
		
		
		
    }
		    public function confirmarTurno($data){
        $id_turno=$data['id_turno']; 
		$nivel=null;

       return $this->database->queryInsertUpdateConReturnId("UPDATE turnos set estado='$estado' where id_turno='$id_turno' ");
		
		
		
    }
	
	




    public function tieneTurnoActual($id, $fechaActual){
        return $this->database->query("SELECT * FROM turnos WHERE id_usuario='$id'");
    }
    public function datosTurnoActual($idUsuario){
        return $this->database->query("SELECT *
                                       FROM turnos JOIN centros ON turnos.id_centro=centros.id_centro
                                       WHERE id_turno=(SELECT MAX(t2.id_turno) FROM turnos t2 WHERE t2.id_usuario='$idUsuario');");
    }
    public function datosUsuarioTurno($id){
        return $this->database->query("SELECT * FROM usuario WHERE id='$id'");
    }
    public function actualizarEstado($idTurno, $estado){
        $this->database->queryInsertUpdate("UPDATE turnos SET estado='$estado' WHERE id_turno='$idTurno'");
    }
    public function asignarNivel($idTurno, $nivel){
        $this->database->queryInsertUpdate("UPDATE turnos SET nivel='$nivel' WHERE id_turno='$idTurno'");
    }


    /*

    CREATE TABLE `centros` ( `id_centro` INT(9) NOT NULL , `nombre` VARCHAR(100) NULL , `descripcion` VARCHAR(100) NULL , `turnos` INT(9) NULL , PRIMARY KEY (`id_centro`)) ENGINE = InnoDB;


    ALTER TABLE `centros` CHANGE `id_centro` `id_centro` INT(9) NOT NULL AUTO_INCREMENT;

    INSERT INTO `centros` (`id_centro`, `nombre`, `descripcion`, `turnos`) VALUES ('', 'Buenos Aires', 'Calle Buenos Aires', '300'), ('', 'Shanghái', 'Calle Shanghái', '210'), ('', 'Ankara', 'Calle Ankara', '200');



    CREATE TABLE `gauchorocket1`.`turnos` ( `id_turno` INT(9) NOT NULL AUTO_INCREMENT , `id_usuario` INT(9) NULL , `id_centro` INT(9) NULL , `fecha` DATE NULL , PRIMARY KEY (`id_turno`)) ENGINE = InnoDB;


    ALTER TABLE `turnos` ADD `estado` VARCHAR(20) NULL AFTER `fecha`, ADD `nivel` INT(9) NULL AFTER `estado`;


    */


}
<?php

class RegistroModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

     public function getValidoCodigoAlta($clave,$email,$pass){



		/*
		$sql=$this->database->prepare->("SELECT * FROM  usuario WHERE codigo_alta=? and email=?  and clave=? ");
		$sql->bind_param("sss",$clave,$email,$pass);
		$sql->execute();
		$result = $sql->get_result();
		$rows=$result->num_rows; 
        return $rows;*/
		$clave=md5($clave);
		$pass=md5($pass);
		 $SQL = "SELECT * FROM  usuario WHERE codigo_alta='$clave' and email='$email'  and clave='$pass'";
        return $this->database->query($SQL);
    }

    public function getactualicoCodigoAlta($id){
		
		/* 	$sql=$mysqli->prepare("UPDATE usuario SET codigo_alta=null WHERE id=? ");
	$sql->bind_param("s",$id_usuario);
	$sql->execute();*/
	
        $SQL = "UPDATE usuario SET codigo_alta=null WHERE id=$id ";
        return $this->database->update($SQL);
    }
}
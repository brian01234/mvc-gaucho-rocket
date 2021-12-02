<?php

class RegistroModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }
    public function validarEmail($email){
        return $this->database->query("SELECT * FROM usuario WHERE email like '$email';");
    }
    public function registrarUserModel($data){
        $nombre=$data['nombre'];
        $apellido=$data['apellido'];
        $email=$data['email'];
        $clave=$data['clave'];
        $codigo_alta=$data['codigo_alta'];

        $this->database->queryInsertUpdate("INSERT INTO usuario
        (nombre,apellido,email,id_rol,clave,codigo_alta)VALUES
        ('".$nombre."','".$apellido."','".$email."',2,'".$clave."','".$codigo_alta."')");
    }
     public function validarCodigoAlta($clave,$email,$pass){
		 $SQL = "SELECT * FROM  usuario WHERE codigo_alta='$clave' and email='$email'  and clave='$pass'";
        return $this->database->query($SQL);
    }

    public function actualizarCodigoAlta($id){
        $SQL = "UPDATE usuario SET codigo_alta=null WHERE id=$id ";
        return $this->database->update($SQL);
    }
}
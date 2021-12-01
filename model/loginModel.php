<?php
class loginModel{
   private $dataBase;

    public function __construct($dataBase){
        $this->dataBase=$dataBase;
    }
    public function iniciarSesion($email, $clave){
        $consulta="SELECT * FROM usuario WHERE email=? AND clave=?";
        return $this->dataBase->queryLogin($consulta, $email, $clave);
    }
}
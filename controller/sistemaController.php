<?php
class sistemaController{
    private $printer;
    private $sessionUser;

    public function __construct($printer, $sessionUser){
        $this->printer=$printer;
        $this->sessionUser=$sessionUser;
    }

    public function show(){
        $this->sessionUser->validarAdministrador();
        echo $this->printer->render("view/sistemaView.html");
    }
}
<?php
class Configuration{

    private $config;

	    public  function createRegistroController(){
        require_once("controller/RegistroController.php");
        return new RegistroController($this->createRegistroModel(),$this->createPrinter());
    }

    public function createHomeController(){
        require_once("controller/homeController.php");
        return new homeController($this->createPrinter());
    }

    public function createLoginController(){
        require_once("controller/loginController.php");
        return new loginController($this->createLoginModel(), $this->createPrinter(), $this->createSessionUser());
    }

    public function createLogueadoController(){
        require_once("controller/logueadoController.php");
        return new logueadoController($this->createLogueadoModel(), $this->createPrinter());
    }

    public function createSistemaController(){
        require_once("controller/sistemaController.php");
        return new sistemaController($this->createSistemaModel(), $this->createPrinter(), $this->createSessionUser());
    }

    public function createCerrarSesionController(){
        require_once("controller/cerrarSesion.php");
        return new cerrarSesion();
    }
	
	
	
    private  function createRegistroModel(){
        require_once("model/RegistroModel.php");
        $database = $this->getDatabase();
        return new RegistroModel($database);
    }

    public function createLoginModel(){
        require_once("model/loginModel.php");
        $database=$this->getDatabase();
        return new loginModel($database);
    }

    public function createLogueadoModel(){
        require_once("model/logueadoModel.php");
        $database=$this->getDatabase();
        return new logueadoModel($database);
    }

    public function createSistemaModel(){
        require_once("model/sistemaModel.php");
        $database=$this->getDatabase();
        return new sistemaModel($database);
    }



    public function createSessionUser(){
        require_once ("controller/sessionUser.php");
        return new sessionUser($this->createModelSessionUser());
    }

    public function createModelSessionUser(){
        require_once ("model/modelSessionUser.php");
        $database=$this->getDatabase();
        return new modelSessionUser($database);
    }

    private  function getDatabase(){
        require_once("helpers/MyDatabase.php");
        $config = $this->getConfig();
        return new MyDatabase($config["servername"], $config["username"], $config["password"], $config["dbname"]);
    }

    private  function getConfig(){
        if( is_null( $this->config ))
            $this->config = parse_ini_file("config/config.ini");

        return  $this->config;
    }

    private function getLogger(){
        require_once("helpers/Logger.php");
        return new Logger();
    }

    public function createRouter($defaultController, $defaultAction){
        include_once("helpers/Router.php");
        return new Router($this,$defaultController,$defaultAction);
    }

    private function createPrinter(){
        require_once ('third-party/mustache/src/Mustache/Autoloader.php');
        require_once("helpers/MustachePrinter.php");
        return new MustachePrinter("view/partials");
    }

}
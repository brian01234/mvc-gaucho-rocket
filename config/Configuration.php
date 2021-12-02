<?php
class Configuration{

    private $config;

    public function createLoginController(){
        require_once("controller/loginController.php");
        return new loginController($this->createLoginModel(), $this->createPrinter(), $this->createSessionUser());
    }
    public function createRegistroController(){
        require_once("controller/RegistroController.php");
        return new RegistroController($this->createRegistroModel(),$this->createPrinter(), $this->createMailHelper());
    }
    public function createHomeController(){
        require_once("controller/homeController.php");
        return new homeController($this->createPrinter(), $this->createReservaModel(), $this->createSessionUser());
    }
    public function createLogueadoController(){
        require_once("controller/logueadoController.php");
        return new logueadoController($this->createLogueadoModel(), $this->createPrinter(), $this->createSessionUser());
    }
    public function createMedicoController(){
        require_once("controller/MedicoController.php");
        return new MedicoController($this->createMedicoModel(),$this->createPrinter(), $this->createMailHelper(), $this->createSessionUser());
    }
    public function createReservaController(){
        require_once("controller/ReservaController.php");
        return new ReservaController($this->createReservaModel(),$this->createPrinter(), $this->createMailHelper(), $this->createSessionUser());
    }
    public function createMisreservasController(){
        require_once("controller/misReservasController.php");
        return new misReservasController($this->createMisReservasModel(),$this->createPrinter(), $this->createPdfHelper(), $this->createQRHelper(), $this->createMailHelper(), $this->createSessionUser());
    }
    public function createMensajeMailController(){
        require_once("controller/mensajeMailController.php");
        return new mensajeMailController($this->createPrinter(), $this->createSessionUser());
    }

    public function createSistemaController(){
        require_once("controller/sistemaController.php");
        return new sistemaController($this->createPrinter(), $this->createSessionUser());
    }
    public function createSistemaUsuariosController(){
        require_once("controller/sistemaUsuariosController.php");
        return new sistemaUsuariosController($this->createSistemaUsuariosModel(), $this->createPrinter(), $this->createSessionUser());
    }
    public function createSistemaVuelosController(){
        require_once("controller/sistemaVuelosController.php");
        return new sistemaVuelosController($this->createSistemaVuelosModel(), $this->createPrinter(), $this->createSessionUser());
    }
    public function createSistemaGraficoController(){
        require_once("controller/sistemaGraficoController.php");
        return new sistemaGraficoController($this->createSistemaGraficoModel(), $this->createPrinter(), $this->createSessionUser(), $this->createPdfHelper());
    }
    public function createCerrarSesionController(){
        require_once("controller/cerrarSesion.php");
        return new cerrarSesion();
    }





    public function createLoginModel(){
        require_once("model/loginModel.php");
        $database=$this->getDatabase();
        return new loginModel($database);
    }
    private  function createRegistroModel(){
        require_once("model/RegistroModel.php");
        $database = $this->getDatabase();
        return new RegistroModel($database);
    }
    public function createLogueadoModel(){
        require_once("model/logueadoModel.php");
        $database=$this->getDatabase();
        return new logueadoModel($database);
    }
    private  function createMedicoModel(){
        require_once("model/MedicoModel.php");
        $database = $this->getDatabase();
        return new MedicoModel($database);
    }
    private  function createReservaModel(){
        require_once("model/ReservaModel.php");
        $database = $this->getDatabase();
        return new ReservaModel($database);
    }
    private  function createMisReservasModel(){
        require_once("model/misReservasModel.php");
        $database = $this->getDatabase();
        return new misReservasModel($database);
    }

    public function createSistemaUsuariosModel(){
        require_once("model/sistemaUsuariosModel.php");
        $database=$this->getDatabase();
        return new sistemaUsuariosModel($database);
    }
    public function createSistemaVuelosModel(){
        require_once("model/sistemaVuelosModel.php");
        $database=$this->getDatabase();
        return new sistemaVuelosModel($database);
    }
    public function createSistemaGraficoModel(){
        require_once("model/sistemaGraficoModel.php");
        $database=$this->getDatabase();
        return new sistemaGraficoModel($database);
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


    private function createPdfHelper(){
        require_once ("helpers/PdfHelper.php");
        return new PdfHelper($this->pdf());
    }

    private function pdf(){
        require_once 'third-party/dompdf/autoload.inc.php';
        return new Dompdf\Dompdf();
    }

    private function createQRHelper(){
        require_once ("helpers/QrHelper.php");
        return new QrHelper($this->qr());
    }
    private function qr(){
        require_once 'third-party/phpqrcode/qrlib.php';
        return new QRcode();
    }

    private function createMailHelper(){
        require_once ("helpers/mailHelper.php");
        return new mailHelper($this->mail());
    }
    private function mail(){
        require_once 'third-party/phpmailer/Exception.php';
        require_once 'third-party/phpmailer/PHPMailer.php';
        require_once 'third-party/phpmailer/SMTP.php';

        return new PHPMailer\PHPMailer\PHPMailer();
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
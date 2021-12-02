<?php
class sistemaGraficoController{
    private $printer;
    private $sistemaGraficoModel;
    private $sessionUser;
    private $pdfHelper;
    public function __construct($sistemaGraficoModel, $printer, $sessionUser, $pdfHelper){
        $this->printer=$printer;
        $this->sistemaGraficoModel=$sistemaGraficoModel;
        $this->sessionUser=$sessionUser;
        $this->pdfHelper=$pdfHelper;
    }

    public function show(){
        $this->sessionUser->validarAdministrador();
        echo $this->printer->render("view/sistemaGraficoView.html");
    }

    public function grafico(){
        $this->sessionUser->validarAdministrador();
        if (isset($_POST["grafico"])){
            $this->prepararGrafico($_POST["grafico"]);

            $nombre=$_POST["nombre"];
            $dataPDF="<img style='width: 600px;' src='http://localhost/mvc-gaucho-rocket/public/grafico/cabinas.jpg'>";
            $this->pdfHelper->printPdf("Grafico", $dataPDF);
        }else{echo "Error grafico";};
    }
    private function prepararGrafico($grafico){
        $this->sessionUser->validarAdministrador();
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $grafico));
        file_put_contents("public/grafico/cabinas.jpg",$data);
    }

    public function ajaxCabinas(){
        $data["cabinas"]=$this->sistemaGraficoModel->cabinasVendidas();
        $data["usuarios"]=$this->sistemaGraficoModel->usuariosQueReservaron();
        echo  json_encode($data);
    }
}
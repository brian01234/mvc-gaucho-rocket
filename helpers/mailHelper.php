<?php
class mailHelper{
    private $mail;
    private $destinatario;
    public function __construct($mail){
        $this->mail=$mail;
        $this->destinatario="maildepruebagm@gmail.com";
    }


    public function enviarMail($subject, $mensaje, $destinatario){
        if ($this->destinatario!=""){
            $destinatario=$this->destinatario;
        }
        $this->mail();
        $this->mail->AddAddress("$destinatario", 'El Destinatario');
        $this->mail->isHTML(true);                                  // Set email format to HTML
        $this->mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $this->mail->Body = $mensaje;
        $this->mail->AltBody = 'This is a plain-text message body';
//Enviamos el correo
        if(!$this->mail->Send()) {
            return "Error";
        } else {
            return "Enviado";
        }
    }
    public function enviarMailPDF($subject, $mensaje, $destinatario, $reserva){
        if ($this->destinatario!=""){
            $destinatario=$this->destinatario;
        }
        $this->mail();
        $this->mail->AddAddress("$destinatario", 'El Destinatario');
        $this->mail->addAttachment('public/pdf/pdf.pdf', "$reserva.pdf");    //Optional name
//Definimos el tema del email
        $this->mail->isHTML(true);                                  // Set email format to HTML
        $this->mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $this->mail->Body = $mensaje;
        $this->mail->AltBody = 'This is a plain-text message body';
//Enviamos el correo
        if(!$this->mail->Send()) {
            return "Error";
        } else {
            return "Enviado";
        }
    }
    public function mail(){
        $this->mail->IsSMTP();
//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
// 0 = off (producción)
// 1 = client messages
// 2 = client and server messages
        $this->mail->SMTPDebug  = 0;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
        $this->mail->Host       = 'smtp.gmail.com';
//El puerto será el 587 ya que usamos encriptación TLS
        $this->mail->Port       = 587;
//Definmos la seguridad como TLS
        $this->mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, así que esto a TRUE
        $this->mail->SMTPAuth   = true;
//Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $this->mail->Username   = "maildepruebagm@gmail.com";
//Introducimos nuestra contraseña de gmail
        $this->mail->Password   = "maildepruebagm@1234";
//Definimos el remitente (dirección y, opcionalmente, nombre)
        $this->mail->SetFrom('maildepruebagm@gmail.com', 'Gaucho Rocket');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
    }

}
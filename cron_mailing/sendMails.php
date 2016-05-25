<?php  	
	require 'PHPMailerAutoload.php';
	error_reporting(E_ALL);

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	/*$mail->Host       = 'smtp.tecnologiza.me';
	$mail->Port       = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth   = true;
	$mail->Username   = "no-reply@tecnologiza.me";
	$mail->Password   = "nOr3plym41l3r_";*/
	$mail->Host    = "mail2.grupouda.com.mx"; // specify main and backup server
    $mail->SMTPAuth  = true; // turn on SMTP authentication
    $mail->Username  = "avl.4togo"; // SMTP username
    $mail->Password  = "qazwsx"; // SMTP password

	$conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));
	
  	$sql = "SELECT *
    		FROM SYS_MAILING
        	WHERE ESTATUS = 0  LIMIT 30"; 
  	$query = mysqli_query($conexion, $sql);
  	$count = 0;
	while($result = mysqli_fetch_array($query)){
		//Definimos el remitente (dirección y, opcionalmente, nombre)
		$mail->SetFrom($result['REMITENTE_NOMBRE'], $result['REMITENTE_EMAIL']);

		$aDestinos = explode(",",$result['DESTINATARIOS']);
		$aNDestino = explode(",",$result['NOMBRES_DESTINATARIOS']);
		
		var_dump($aDestinos);
		var_dump($aNDestino);		

		for($i=0;$i<count($aDestinos);$i++){
			if($count==0){
				if (filter_var($aDestinos[$i], FILTER_VALIDATE_EMAIL) && validate_email($aDestinos[$i])) {
					$mail->AddAddress($aDestinos[$i], $aNDestino[$i]);    
				}else{
					setMarkRow($result['ID_MAILING']);
				}
			}else{
				if (filter_var($aDestinos[$i], FILTER_VALIDATE_EMAIL) && validate_email($aDestinos[$i])) {
					$mail->addBCC($aDestinos[$i], $aNDestino[$i]);
				}else{
					setMarkRow($result['ID_MAILING']);
				}
			}
			$count++;
		}
					
		//Definimos el tema del email
		$mail->Subject = $result['TITULO_MSG'];
		//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
		$mail->MsgHTML($result['CUERPO_MSG']);
		//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
		$mail->AltBody = $result['CUERPO_MSG'];	

		//Enviamos el correo
		if(!$mail->Send()) {
		  	echo 'Message could not be sent.';
        	echo 'Mailer Error: ' . $mail->ErrorInfo;
        	setMarkRowError($result['ID_MAILING']);
		} else {
		  setMarkRow($result['ID_MAILING']);
		}

		$mail->ClearAllRecipients();
	}

  	function setMarkRow($idOject){
	    global $conexion;
	    $result = false;
	      $sql ="UPDATE SYS_MAILING 
	            SET ESTATUS 		= 1,
	      			FECHA_ENVIADO   = CURRENT_TIMESTAMP
	            WHERE 	ID_MAILING 	= $idOject	            		
	            LIMIT 1";
	    $query  = mysqli_query($conexion, $sql);
	    if($query){
	      $result= true;
	    }
	    return $result;   
  	}

  	function setMarkRowError($idOject){
	    global $conexion;
	    $result = false;
	      $sql ="UPDATE SYS_MAILING 
	            SET ESTATUS 		= -3,
	      			FECHA_ENVIADO   = CURRENT_TIMESTAMP
	            WHERE 	ID_MAILING 	= $idOject	            		
	            LIMIT 1";
	    $query  = mysqli_query($conexion, $sql);
	    if($query){
	      $result= true;
	    }
	    return $result;   
  	}  	

	function validate_email($e){
	    return (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($e));
	}  	

<?php
	error_reporting(E_ALL);
	$sMails   = "c.instalaciones@grupouda.com.mx,oper.mesadecontrol@grupouda.com.mx,servicioaclientes@grupouda.com.mx,sup.monitoreo@grupouda.com.mx,contralor’a@grupouda.com.mx,c.cxc@grupouda.com.mx,a.cxc@grupouda.com.mx,c.almacen@grupouda.com.mx,calidad@grupouda.com.mx" ;
	$conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));
	
  	$sql = "SELECT *
    		FROM PROD_CITAS_MAILING
        	WHERE ENVIADO = 0 
        	  AND TIPO    = 'F'
        	GROUP BY ID_CITA LIMIT 20"; 
  	$query = mysqli_query($conexion, $sql);
  	$count = 0;
	while($result = mysqli_fetch_array($query)){

		$aInfo = getDataInfo($result['ID_CITA']);
		
		ob_start();
		include('tSolicitud_gracias.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'Servicio Finalizado';

		$lBodyUda = str_ireplace('@_id_@', 	 		@$aInfo['ID_CITA']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_razon_@', 	 	@$aInfo['RAZON_SOCIAL'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tipo_@', 	 	@$aInfo['N_TIPO']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@', 	 	@$aInfo['FECHA_CITA']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_hora_@', 	 	@$aInfo['HORA_CITA']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	@$aInfo['N_HORARIO']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_direccion_@', 	@$aInfo['DIRECCION_CITA'] , $lBodyUda);
		
		if(isset($aInfo['EMAIL']) && $aInfo['EMAIL']!=""){
			$aMailer    = Array(
				'inputIdSolicitud'	 => -1,
				'inputDestinatarios' => $aInfo['EMAIL'],
				'inputEmails' 		 => $aInfo['EMAIL'],
				'inputTittle' 		 => $sMensaje,
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);

			$insert = insertMailing($aMailer);
			if($insert){
				setMarkRow($result['ID_CITA']);
			}	
		}else{
			setMarkRow($result['ID_CITA']);
		}	

		$aUDA    = Array(
			'inputIdSolicitud'	 => -1,
			'inputDestinatarios' => $sMails,
			'inputEmails' 		 => $sMails,
			'inputTittle' 		 => $sMensaje,
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);

			$insert = insertMailing($aUDA);		
	}

	function getDataInfo($idCita){
		global $conexion;
		$aResult = Array();
	  	$sql = "SELECT C.ID_CITA, C.FECHA_CITA, C.HORA_CITA, C.CONTACTO, C.TELEFONO_CONTACTO,
				IF(D.ID_CITA  IS NULL,'Sin Direccion',CONCAT(D.CALLE,' ',D.COLONIA,' ',D.NO_EXT,' ',D.NO_INT,' ',D.MUNICIPIO,' ',D.ESTADO,',CP:',D.CP))  AS DIRECCION_CITA,
				L.RAZON_SOCIAL, T.DESCRIPCION AS N_TIPO, L.EMAIL
			FROM PROD_CITAS C 
			 LEFT JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA    = D.ID_CITA
			INNER JOIN PROD_CLIENTES       L ON C.ID_CLIENTE = L.ID_CLIENTE 
			INNER JOIN PROD_TPO_CITA       T ON C.ID_TPO     = T.ID_TPO  
				WHERE C.ID_CITA = $idCita
				LIMIT 1";
		$query   = mysqli_query($conexion, $sql);
		$result  = mysqli_fetch_array($query);
		$aResult = $result;
		
		return $aResult;
	}

  	function setMarkRow($idCita){
	    global $conexion;
	    $result = false;
	      $sql ="UPDATE PROD_CITAS_MAILING 
	            SET ENVIADO 		= 1,
	      			PROCESADO       = CURRENT_TIMESTAMP
	            WHERE ID_CITA 		= $idCita";
	    $query  = mysqli_query($conexion, $sql);
	    if($query){
	      $result= true;
	    }
	    return $result;   
  	}

  	function insertMailing($data){
	    global $conexion;
	    $result = false;
 		$sql="INSERT INTO SYS_MAILING
				SET ID_SOLICITUD			=  ".$data['inputIdSolicitud'].",
					NOMBRES_DESTINATARIOS	= '".$data['inputDestinatarios']."', 
					DESTINATARIOS			= '".$data['inputEmails']."',
					TITULO_MSG			 	= '".$data['inputTittle']."',
					CUERPO_MSG				= '".$data['inputBody']."',
					REMITENTE_NOMBRE		= '".$data['inputFromName']."',
					REMITENTE_EMAIL			= '".$data['inputFromEmail']."',
					LIVE_NOTIFICATION		=  ".$data['inputLiveNotif'].",
					FECHA_CREADO			= CURRENT_TIMESTAMP,
					ESTATUS 				= 0";
	    $query  = mysqli_query($conexion, $sql);
	    if($query){
	      $result= true;
	    }
	    return $result;   
  	}

<?php
	error_reporting(E_ALL);
	$conexion = new mysqli('201.131.96.56','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));
	//$conexion = new mysqli('localhost','root','root','DB_SIAMES') or die("Some error occurred during connection " . mysqli_error($conexion));
	
  	$sql = "SELECT *
			FROM PROD_CITAS 
			WHERE TIPO_FIRMA IS NULL
			  AND ID_ESTATUS = 4 
			  /*AND FECHA_CITA < '2016-03-16' */
			ORDER BY FECHA_CITA DESC, HORA_CITA DESC
			LIMIT 500";			
  	$query = mysqli_query($conexion, $sql);
  	$count = 0;
	while($result = mysqli_fetch_array($query)){
		$sFirma   = getDataFirma($result['ID_CITA']);

		if($sFirma!=""){
			setMarkRow($result['ID_CITA'],$sFirma);
		}		
	}

	function getDataFirma($idCita){
		global $conexion;
		$aResult = '';
	  	$sql = "SELECT E.ID_ELEMENTO,
    					E.ID_TIPO,		
				       IF (E.ID_TIPO = 8, 'ENCABEZADO','RESPUESTA') AS TIPO,			
				       E.DESCIPCION AS DESCRIPCION,			
				       B.CONTESTACION,			
				       A.FECHA_CAPTURA_EQUIPO,
				       L.ID_TIPO AS T_ELEMENTO,
				       B.ID_RESULTADO	
				FROM PROD_FORM_RESULTADO A			
				  INNER JOIN PROD_FORM_DETALLE_RESULTADO B ON A.ID_RESULTADO = B.ID_RESULTADO			
				  INNER JOIN PROD_FORMULARIO_ELEMENTOS C ON C.ID_ELEMENTO = B.ID_ELEMENTO			
				  INNER JOIN PROD_CITA_FORMULARIO D ON D.ID_RESULTADO = A.ID_RESULTADO			
				  INNER JOIN PROD_ELEMENTOS E ON E.ID_ELEMENTO = C.ID_ELEMENTO
				  INNER JOIN PROD_TPO_ELEMENTO L ON E.ID_TIPO = L.ID_TIPO		
				WHERE D.ID_CITA 	  = ".$idCita."
				  AND E.DESCIPCION    = 'TIPO DE FIRMA'
				 GROUP BY B.ID_ELEMENTO
				ORDER BY C.ORDEN ASC LIMIT 1";			
		$query   = mysqli_query($conexion, $sql);
		$result  = mysqli_fetch_array($query);

		if($result['CONTESTACION']=='FIRMA'){
			$aResult = $result['CONTESTACION'];
		}else if($result['CONTESTACION']=='QR'){
			$aResult = $result['CONTESTACION'];
		}
		
		return $aResult;
	}

  	function setMarkRow($idCita,$sFirma){
	    global $conexion;
	    $result = false;
	    $sql ="UPDATE PROD_CITAS
				     SET TIPO_FIRMA = '".$sFirma."'
				 WHERE ID_CITA = ".$idCita;	
		echo $sql."<br>";
	    $query  = mysqli_query($conexion, $sql);
	    if($query){
	      $result= true;
	    }
	    return $result;   
  	}
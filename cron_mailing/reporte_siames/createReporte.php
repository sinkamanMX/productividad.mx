<?php
	error_reporting(E_ALL);
	/** PHPExcel */ 
	require_once 'PHPExcel.php';
	$realPath   = '/var/www/vhosts/sima/htdocs/public';
	$pathReport = '/var/www/vhosts/sima/htdocs/public/reportes/';

	if(!PHPExcel_Settings::setPdfRenderer(
			PHPExcel_Settings::PDF_RENDERER_DOMPDF,
			'/var/www/vhosts/sima/cron/reporte_siames/PHPExcel/Classes/dompdf'
	)) {
		die(
			'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
			'<br />' .
			'at the top of this script as appropriate for your directory structure'
		);
	}


	$conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));
	
	$sql = "SELECT *
			FROM PROD_CITAS
			WHERE ENVIO_SAP = 1
			  AND FOLIO     IS NOT NULL
			  AND ID_ESTATUS= 4 LIMIT 5"; 
	//var_dump($sql);
	$query = mysqli_query($conexion, $sql);
	while($result = mysqli_fetch_array($query)){

		$dataCita = getDataRep($result['ID_CITA']);
		/** PHPExcel_Writer_Excel2007*/ 								
		$objPHPExcel = new PHPExcel();
				
		$objPHPExcel->getProperties()->setCreator("UDA")
								 ->setLastModifiedBy("UDA")
								 ->setTitle("Office 2007 XLSX")
								 ->setSubject("Office 2007 XLSX")
								 ->setDescription("Reporte del Viaje")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Reporte del Viaje");
		
								 
		$styleHeader = new PHPExcel_Style();
		$styleAutor	 = new PHPExcel_Style();
		$styleTittle = new PHPExcel_Style();
		$styleHeadermin = new PHPExcel_Style();
		$allBlank	 = new PHPExcel_Style(); 
		
		$allBlank->applyFromArray(array(
			'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => 'FFFFFF')
	        ),
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_NONE
			    )
			  )				        
		));					

		$styleHeader->applyFromArray(array(
			'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '000000')
	        ),
	        'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'FFFFFF'),
		        'size'  => 15,
		        'name'  => 'Calibri'
		    ),
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_NONE
			    )
			  )
		));
		$styleHeadermin->applyFromArray(array(
			'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '000000')
	        ),
	        'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'FFFFFF'),
		        'size'  => 15,
		        'name'  => 'Calibri'
		    ),
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_NONE
			    )
			  )
		));					
		$styleAutor->applyFromArray(array(
			'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '6E6E6E')
	        ),
	        'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'FFFFFF'),
		        'name'  => 'Calibri'
		    ),
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_NONE
			    )
			  )
		));
		
		$styleTittle->applyFromArray(array(
			'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => 'BDBDBD')
	        ),
	        'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'FFFFFF'),
		        'name'  => 'Calibri'
		    ),
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_NONE
			    )
			  )
		));					
	
		/**
		 * Header del Reporte
		 **/
		$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($allBlank, 'A1:Z99');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'ORDEN DE INSTALACION');
		$objPHPExcel->getActiveSheet()->mergeCells('C2:H2');
		$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'C2:H2');
		$objPHPExcel->getActiveSheet()->getStyle('C2:H2')
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
		$objPHPExcel->getActiveSheet()->mergeCells('C3:H3');
		$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAutor, 'C3:H3');
		$objPHPExcel->getActiveSheet()->getStyle('C3:H3')
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Orden de servicio:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', $dataCita['ID']);

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'FOLIO:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', $dataCita['FOLIO']);		

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Fecha:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', $dataCita['FECHA_CITA']);					
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Horario de la cita:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', $dataCita['HORA_CITA']);
		$objPHPExcel->getActiveSheet()->mergeCells('D8:H8');					
						
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Registrada por:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', $dataCita['USR_REGISTRADO']);	
		$objPHPExcel->getActiveSheet()->mergeCells('D9:H9');										

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C10', 'Cliente:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D10', $dataCita['NOMBRE_CLIENTE']);
		$objPHPExcel->getActiveSheet()->mergeCells('D10:H10');						
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C11', 'Domicilio del cliente');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D11', $dataCita['DIRECCION_CLIENTE1']);
		$objPHPExcel->getActiveSheet()->mergeCells('D11:H11');
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D12', $dataCita['DIRECCION_CLIENTE2']);
		$objPHPExcel->getActiveSheet()->mergeCells('D12:H12');											
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C13', 'Domicilio de instalacion:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', $dataCita['DIRECCION_CITA1']);
		$objPHPExcel->getActiveSheet()->mergeCells('D13:H13');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14', $dataCita['DIRECCION_CITA2']);
		$objPHPExcel->getActiveSheet()->mergeCells('D14:H14');					
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C15', utf8_encode('Personal asignado:'));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D15', $dataCita['NOMBRE_TECNICO']);
		$objPHPExcel->getActiveSheet()->mergeCells('D15:H15');						

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C16', 'Inicio de instalacion:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D16', $dataCita['FECHA_INICIO']);
		$objPHPExcel->getActiveSheet()->mergeCells('D16:H16');						
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C17', 'Fin de instalacion:');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D17', $dataCita['FECHA_TERMINO']);	
		$objPHPExcel->getActiveSheet()->mergeCells('D17:H17');	
		
		$objPHPExcel->getActiveSheet()->mergeCells('A18:H18');
		$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($allBlank, 'A18:H18');					

		$rowControl		= 19;
		$zebraControl  	= 0;								
		//var_dump("control 1");
		$aForms = getFormsCita($result['ID_CITA']);
		foreach($aForms as $key => $itemsForm){
			//var_dump("control ".$rowControl);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowControl.':B'.$rowControl);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $itemsForm['TITULO']);
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
			$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeadermin, 'C'.$rowControl.':H'.$rowControl);						
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
													
			$rowControl++;

			$aDataForms = getDataSendbyForms($result['ID_CITA'],$itemsForm['ID_FORMULARIO']);
			//var_dump("control 3");
			foreach($aDataForms as $key => $items){		

				if($items['TIPO']=='ENCABEZADO'){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
					$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleTittle, 'C'.$rowControl.':H'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$rowControl++;				
				}else{		
					/*------ La respuesta es una foto ------*/						
					if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){	
													
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);	
						$rowControl++;

						
						if(!is_readable($realPath.$items['CONTESTACION'])){
							var_dump("No se puede leer ->".$realPath.$items['CONTESTACION']);
						}			

						//var_dump("No se puede leer ->".$realPath.$items['CONTESTACION']);			

						if ($items['CONTESTACION']!="" && file_exists($realPath.$items['CONTESTACION'])) {

							$objDrawing = new PHPExcel_Worksheet_Drawing();
							
							$objDrawing->setName('Picture1');
							$objDrawing->setDescription('Picture1');
							
							$objDrawing->setPath($realPath.$items['CONTESTACION']);
							$objDrawing->setWidth(120);
							$objDrawing->setOffsetX(150);
							$objDrawing->setHeight(135);
							$objDrawing->setOffsetY(-160);

							$objDrawing->setCoordinates('C'.$rowControl);
							
							$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
								->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
							
							if($items['T_ELEMENTO']=='10'){
								//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(60);	
							}else{
								//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(150);
							}
							
							$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);
							
							$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
						}else{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), "Imagen no disponible.");								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);										
						}
						
					/*------ La respuesta es texto    ------*/	
					}else{
						
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':C'.$rowControl);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['CONTESTACION']);								
						$objPHPExcel->getActiveSheet()->mergeCells('D'.$rowControl.':H'.$rowControl);
					}
					$rowControl++;
				}					
			}	
			//var_dump("control 4");					
		}

		//var_dump("control 5");
		//var_dump($result);

		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);	

		$objPHPExcel->getActiveSheet()->setShowGridLines(true);
		$objPHPExcel->getActiveSheet()->setPrintGridLines(true);					
	
		$filename  = $result['FOLIO'].".pdf";

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		$objWriter->save($pathReport.$filename);

		//var_dump($pathReport.$filename);
		if(file_exists($pathReport.$filename)){
			setMarkReporte($result['ID_CITA']);
		}
	}

	function getFormsCita($idCita){
		global $conexion;
		$result = Array();
    	$sql ="SELECT B.ID_FORMULARIO,			
	               B.TITULO,		
			       B.FOTOS_EXTRAS,
			       B.QRS_EXTRAS,
			       B.FIRMAS_EXTRAS,
			       B.LOCALIZACION
				FROM PROD_CITA_FORMULARIO    A
				  INNER JOIN PROD_FORMULARIO B ON A.ID_FORMULARIO = B.ID_FORMULARIO
				WHERE A.ID_CITA = ".$idCita;
				//var_dump($sql);
		$query  = mysqli_query($conexion, $sql);
		if($query){
			while($resultSql = mysqli_fetch_array($query)){
				$result[] = $resultSql;
			}
		}
		return $result;
	}

	function getDataSendbyForms($idOject,$idForm){
		global $conexion;
		$result = Array();
    	$sql ="SELECT E.ID_TIPO,			
				       IF (E.ID_TIPO = 8, 'ENCABEZADO','RESPUESTA') AS TIPO,			
				       E.DESCIPCION AS DESCRIPCION,			
				       B.CONTESTACION,			
				       A.FECHA_CAPTURA_EQUIPO,
				       L.ID_TIPO AS T_ELEMENTO		
				FROM PROD_FORM_RESULTADO A			
				  INNER JOIN PROD_FORM_DETALLE_RESULTADO B ON A.ID_RESULTADO = B.ID_RESULTADO			
				  INNER JOIN PROD_FORMULARIO_ELEMENTOS C ON C.ID_ELEMENTO = B.ID_ELEMENTO			
				  INNER JOIN PROD_CITA_FORMULARIO D ON D.ID_RESULTADO = A.ID_RESULTADO			
				  INNER JOIN PROD_ELEMENTOS E ON E.ID_ELEMENTO = C.ID_ELEMENTO
				  INNER JOIN PROD_TPO_ELEMENTO L ON E.ID_TIPO = L.ID_TIPO		
				WHERE A.ID_FORMULARIO = $idForm AND			
				      D.ID_CITA 	  = $idOject			
				ORDER BY C.ORDEN ASC";		
		//var_dump($sql);
		$query  = mysqli_query($conexion, $sql);
		if($query){
			while($resultSql = mysqli_fetch_array($query)){
				$result[] = $resultSql;
			}
		}
		return $result;		
	}	

	function setMarkReporte($idOject){
		global $conexion;
		$result = false;
    	$sql ="UPDATE PROD_CITAS 
				  SET ENVIO_SAP = 2
				WHERE ID_CITA = $idOject";
		$query  = mysqli_query($conexion, $sql);
		if($query){
			$result= true;
		}
		return $result;		
	}
	
	function getDataRep($idOject){
		global $conexion;
		$result= Array();
    	$sql ="SELECT   C.ID_CITA AS ID, 
						C.FECHA_CITA,
						C.HORA_CITA,
						CONCAT(R.NOMBRE,' ',R.APELLIDOS) AS USR_REGISTRADO,
						CONCAT(P.NOMBRE,' ',P.APELLIDOS) AS NOMBRE_CLIENTE,		
						CONCAT(M.CALLE,' ',M.NUMERO_EXT,' ',M.NUMERO_INT,' ',M.COLONIA) AS DIRECCION_CLIENTE1,
						CONCAT(M.MUNICIPIO,' ',M.ESTADO,' ',M.CP) AS DIRECCION_CLIENTE2,
						CONCAT(D.CALLE,' ',D.NO_EXT,' ',D.NO_INT,' ',D.COLONIA) AS DIRECCION_CITA1,
						CONCAT(D.MUNICIPIO,' ',D.ESTADO,' ',D.CP) AS DIRECCION_CITA2,	
						IF(U.ID_USUARIO    IS NULL ,'Sin Asignar', CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS NOMBRE_TECNICO,
						IF(C.FECHA_INICIO  IS NULL ,'--',C.FECHA_INICIO) AS FECHA_INICIO,
						IF(C.FECHA_TERMINO IS NULL ,'--',C.FECHA_TERMINO) AS FECHA_TERMINO,
						C.FOLIO
				FROM PROD_CITAS C
					LEFT JOIN USUARIOS			   R ON C.ID_USUARIO_CREO = R.ID_USUARIO
					LEFT JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA 	 = D.ID_CITA
					LEFT JOIN PROD_ESTATUS_CITA   S ON C.ID_ESTATUS = S.ID_ESTATUS
					LEFT JOIN PROD_CLIENTES       P ON C.ID_CLIENTE = P.ID_CLIENTE
					LEFT JOIN PROD_DOMICILIOS_CLIENTE M ON P.ID_CLIENTE = M.ID_CLIENTE
					LEFT JOIN PROD_CITA_USR       A ON C.ID_CITA	 = A.ID_CITA
					LEFT JOIN USUARIOS			   U ON A.ID_USUARIO = U.ID_USUARIO 
				WHERE C.ID_CITA =".$idOject;  
		//var_dump($sql);
		$query  = mysqli_query($conexion, $sql);
		if($query){
			while($resultSql = mysqli_fetch_array($query)){
				$result[] = $resultSql;
			}
		}
		return $result[0];		
	}
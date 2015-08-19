<?php

class external_ReportController extends My_Controller_Action
{	
	protected $_clase = 'mservices';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/productividad.mx/public';
	
    public function init()
    {
    	try{	
    		$this->dataIn 			= $this->_request->getParams();
    		/*
    		$sessions = new My_Controller_AuthContact();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/external/login/index');		
			}
			
			
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);		
			*/
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }


	public function exportAction(){
		try{
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();

			if(isset($this->dataIn['catId'])  	 && $this->dataIn['catId']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['catId']);
				
				if(count($dataCita)>0){			
					/** PHPExcel */ 
					require_once 'PHPExcel.php';		
											
					if (!PHPExcel_Settings::setPdfRenderer(
							PHPExcel_Settings::PDF_RENDERER_DOMPDF,
							$this->realPath.'/PHPExcel/Classes/dompdf'
					)) {
						die(
							'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
							'<br />' .
							'at the top of this script as appropriate for your directory structure'
						);
					}
					
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Orden de Servicio")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Orden de Servicio");					 
					//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);				
					$sHeaderBig    = new PHPExcel_Style();
					$sHeaderBlack  = new PHPExcel_Style();
					$sTextBlack    = new PHPExcel_Style();
					$sHeaderOrange = new PHPExcel_Style();					
					$sBorderOrange = new PHPExcel_Style();
					$sBordersBottom= new PHPExcel_Style();
					$sTittleOrange = new PHPExcel_Style();
					$sTextOrange   = new PHPExcel_Style();
					
					$sHeaderBig->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 16,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )				        
					));					
		
					$sHeaderBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sTextBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sHeaderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sTextOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 10,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sBorderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));	
					
					$sTittleOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FF8000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    )
					));	
					
					$sBordersBottom->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => false,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'bottom' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));	
										
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1);	
					$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('10')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('12')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('18')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('22')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('24')->setRowHeight(5);					
					
					$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
					$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19);
					$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);						 
											 
					/**
					 * Header del Reporte
					 **/					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBig, 'B3:J3');
					
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName('Logo');
					$objDrawing->setDescription('Logo');
					
					$objDrawing->setPath($this->realPath.'/logoUDA.jpg');
					$objDrawing->setWidth(70);
					$objDrawing->setHeight(90);
					//$objDrawing->setOffsetX(10);
					$objDrawing->setCoordinates('I2');
					
					$objPHPExcel->getActiveSheet()->getRowDimension('I2')->setRowHeight(150);										
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('GERENCIA DE OPERACIONES '));
					$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', utf8_decode('ORDEN DE SERVICIO'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBlack, 'B7:J7');
					$objPHPExcel->getActiveSheet()->mergeCells('B7:G7');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Sucursal');								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', @$dataCita['SUCURSAL']);
					$objPHPExcel->getActiveSheet()->mergeCells('C9:D9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'C9:D9');
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', 'Tecnico');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F9', @$dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('F9:H9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'F9:H9');									
																									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I8', utf8_decode('Folio: '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J8:J8');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J8', ($dataCita['FOLIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('J8:J8');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I9', utf8_decode('Tipo de Servicio:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J9:J9');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J9', ($dataCita['TIPO_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('J9:J9');					
					/**
					 * Datos Generales de la Cita
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', utf8_decode('Datos generales'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B11:J11');	
					$objPHPExcel->getActiveSheet()->mergeCells('B11:J11');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B13', utf8_encode('Nombre o Razon Social:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B13:C13');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', substr($dataCita['NOMBRE_CLIENTE'], 0, 45) );	
					$objPHPExcel->getActiveSheet()->mergeCells('D13:G13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D13:G13');
					
									
					if(strlen($dataCita['NOMBRE_CLIENTE'])>45){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14',substr($dataCita['NOMBRE_CLIENTE'], 45, 60) );	
					}											
					$objPHPExcel->getActiveSheet()->mergeCells('D14:G14');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D14:G14');
										

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B15', utf8_encode('Direccion: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B15:C15');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D15', ($dataCita['DIRECCION_CITA1']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D15:G15');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D15:G15');					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D16', ($dataCita['DIRECCION_CITA2']));
					$objPHPExcel->getActiveSheet()->mergeCells('D16:G16');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D16:G16');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B17', utf8_decode('Contacto:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B17:C17');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D17', $dataCita['CONTACTO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('D17:G17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D17:G17');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H13', utf8_decode('Fecha de Servicio:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H13:H13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I13', ($dataCita['FECHA_CITA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I13:J13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I13:J13');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H14', utf8_decode('Hora de Cita:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H14:H14');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I14', ($dataCita['HORA_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('I14:J14');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I14:J14');
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H15', utf8_decode('Hora Inicial:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H15:J15');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I15', utf8_decode($dataCita['FECHA_INICIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I15:J15');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I15:J15');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H16', utf8_decode('Hora Final:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H16:H16');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I16', ($dataCita['FECHA_TERMINO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I16:J16');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I16:J16');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H17', utf8_decode('Telefono:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H17:H17');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I17', ($dataCita['TELEFONO_CONTACTO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I17:J17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I17:J17');

					/**
					 * Datos del Equipo y Accesorios Instalados
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B19', utf8_decode('Datos del Equipo y Accesorios Instalados'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B19:J19');	
					$objPHPExcel->getActiveSheet()->mergeCells('B19:J19');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B20', utf8_decode('Marca'));											
					$objPHPExcel->getActiveSheet()->mergeCells('B20:C20');
					$objPHPExcel->getActiveSheet()->getStyle('B20:C20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E20', utf8_decode('Modelo'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('E20:F20');	
					$objPHPExcel->getActiveSheet()->getStyle('E20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G20', utf8_decode('IMEI'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G20:H20');
					$objPHPExcel->getActiveSheet()->getStyle('G20:H20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I20', utf8_decode('IP'));	
					$objPHPExcel->getActiveSheet()->mergeCells('I20:J20');
										$objPHPExcel->getActiveSheet()->getStyle('I20:J20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					/*------------- -------------*/
					$aDataEqForm = $cCitas->getDataSendbyForms($this->dataIn['catId'],13);
					$dataEquipment = Array();
					
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']==220){
							@$dataEquipment['MARCA'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==223){
							@$dataEquipment['IP'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==222){
							@$dataEquipment['IMEI'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==221){
							@$dataEquipment['MODELO'] = $items['CONTESTACION'];
						}
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B21', ($dataEquipment['MARCA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B21:C21');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B21:C21');
					$objPHPExcel->getActiveSheet()->getStyle('B21:C21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E21', ($dataEquipment['MODELO']));	
					//$objPHPExcel->getActiveSheet()->mergeCells('E21:F21');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'E21');
					$objPHPExcel->getActiveSheet()->getStyle('E21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G21', ($dataEquipment['IMEI']));
					$objPHPExcel->getActiveSheet()->getStyle('G21')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);	
					$objPHPExcel->getActiveSheet()->mergeCells('G21:H21');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G21:H21');
					$objPHPExcel->getActiveSheet()->getStyle('G21:H21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I21', ($dataEquipment['IP']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I21:J21');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I21:J21');
					$objPHPExcel->getActiveSheet()->getStyle('I21:J21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					
					
					if($dataCita['FECHA_TERMINO']<'2015-02-20 23:59:00'){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', utf8_decode('Partes Instaladas (Marca con un X)')); 
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', ('Partes: (IN)staladas, (RE)visadas, (SU)stituidas '));
					}					
						
					$objPHPExcel->getActiveSheet()->mergeCells('B23:J23');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange, 'B23:J23');	
			
					$iValColumn = 0;
					$rowControl	= 25;
					/* ----- -----*/
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']>223 && $items['ID_ELEMENTO'] < 244){
							$sRespuesta="";
							if($dataCita['FECHA_TERMINO']<'2015-02-20 23:59:00'){
								$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							}else{
								$stringUpper = strtoupper($items['CONTESTACION']);
								$sRespuesta  =  substr($stringUpper, 0, 2); 
							}
							
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('D'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('J'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);								
								
								$rowControl++;								
								$iValColumn=0;								
							}
						}						 
					}
					
					$rowControl++;
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Partes Reemplazadas (Marca con un X)'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange,'B'.$rowControl.':J'.$rowControl);	
					
					$rowControl++;
					$rowControl++;
					
					$iValColumn = 0;							
					
					/* ----- -----*/					
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']>244){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							
							if($items['ID_ELEMENTO']==245){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);	
								$rowControl++;								
							}else{
								if($iValColumn==0){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':D'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'C'.$rowControl.':D'.$rowControl);	
									$iValColumn++;
								}else if($iValColumn==1){
									$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('F'.$rowControl.':G'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'F'.$rowControl.':G'.$rowControl);
									
									$iValColumn++;								
								}else if($iValColumn==2){
									$objPHPExcel->getActiveSheet()->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('I'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('I'.$rowControl.':J'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'I'.$rowControl.':J'.$rowControl);
									
									$rowControl++;								
									$iValColumn=0;								
								}								
							}							
						}						 
					}	
					
					
					$rowControl = $rowControl+10;
					
																			
					/**
					 * Pruebas del Funcionamiento del Equipo
					 **/
					
					$aDataPruebas = $cCitas->getDataSendbyForms($this->dataIn['catId'],14);		
					$aDataCUDA = Array();
					foreach($aDataPruebas as $items){
						if($items['ID_ELEMENTO']==252){
							@$aDataCUDA['FOLIO'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==253){
							@$aDataCUDA['EJUDA'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==254){
							@$aDataCUDA['FOLCLI'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==55){
							@$aDataCUDA['MCLI'] = $items['CONTESTACION'];
						}											
					}
															
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Pruebas del Funcionamiento del Equipo'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					$rowControl++;

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Folio de Validacion CCUDA: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, utf8_decode('* Folio de Validacion Cliente:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($aDataCUDA['FOLIO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);	
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($aDataCUDA['FOLCLI']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);
					
					$rowControl++;
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ('Ejecutivo de Atencion CCUDA:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ('* Monitorista por parte del cliente:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$rowControl++;
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($aDataCUDA['EJUDA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);											

										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, (@$aDataCUDA['MCLI']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);		
					$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					
					$rowControl++;											
					$rowControl++;
					
					$iValColumn = 0;
					foreach($aDataPruebas as $items){	
						if($items['ID_ELEMENTO']>255){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('D'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('J'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$rowControl++;								
								$iValColumn=0;
							}
						}						 
					}			
					$rowControl++;
					$rowControl++;
															 				
					$aDataFirma = $cCitas->getDataSendbyForms($this->dataIn['catId'],15);
					$dataFirma  = Array();
					
					foreach($aDataFirma as $items){
						if($items['ID_ELEMENTO']==274){
							@$dataFirma['NCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==275){
							@$dataFirma['OBSERVACIONES'] = $items['CONTESTACION'];		
						}else if($items['ID_ELEMENTO']==276){
							@$dataFirma['FCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==283){
							@$dataFirma['FQRCODE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==282){
							@$dataFirma['TCONTESTA'] = $items['CONTESTACION'];		
						}
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Observaciones'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($dataFirma['OBSERVACIONES']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					$rowControl++;
					$rowControl++;

					/**
					 * Firmas
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Firmas'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					$rowControl++;					
					
					if(@$dataFirma['TCONTESTA'] == 'FIRMA'){
						$exist_file = file_exists($this->realPath.$dataFirma['FCLIENTE']);	

						if($exist_file== true && $dataFirma['FCLIENTE']!="") {						
							$objDrawing = new PHPExcel_Worksheet_Drawing();
							
							$objDrawing->setName('Picture1');
							$objDrawing->setDescription('Picture1');
							
							$objDrawing->setPath($this->realPath.$dataFirma['FCLIENTE']);
							$objDrawing->setWidth(60);
							//$objDrawing->setOffsetX(150);
							$objDrawing->setHeight(75);
							//$objDrawing->setOffsetY(-160);
	
							$objDrawing->setCoordinates('C'.$rowControl);
							
							$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':F'.$rowControl)
								->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);
	
							//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
							$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
						}else{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, 'imagen no disponible');								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);
						}
					}else if(@$dataFirma['TCONTESTA'] == 'QR'){
						$qrExist    = file_exists($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");	
						if($qrExist== true && $dataFirma['FQRCODE']!="") {
							$objDrawing = new PHPExcel_Worksheet_Drawing();
							
							$objDrawing->setName('Picture1');
							$objDrawing->setDescription('Picture1');
							
							$objDrawing->setPath($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");
							$objDrawing->setWidth(60);
							//$objDrawing->setOffsetX(150);
							$objDrawing->setHeight(75);
							//$objDrawing->setOffsetY(-160);
	
							$objDrawing->setCoordinates('C'.$rowControl);
							
							$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':F'.$rowControl)
								->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);
	
							//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
							$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
							
						}else{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, $dataFirma['FQRCODE']);								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);										
						}
					}
					

					
					
					/*
					$exist_file = file_exists($this->realPath.$dataFirma['FINSTALADOR']); 

					if ($exist_file== true && $dataFirma['FINSTALADOR']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataFirma['FINSTALADOR']);
						$objDrawing->setWidth(60);
						//$objDrawing->setOffsetX(150);
						$objDrawing->setHeight(75);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('G'.$rowControl);
						
						$objPHPExcel->getActiveSheet()->getRowDimension('G'.$rowControl)->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);

						//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);										
					}	*/				
					
					$rowControl = $rowControl+5;	
					/*				
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, $dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					*/
						
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, $dataFirma['NCLIENTE']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);							
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					
					
					$rowControl = $rowControl-1;
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);
					/*$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);*/	
    
					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(false);	
					/*
					$filename  = "Orden_Servicio_".$this->dataIn['catId'].".xlsx";		
					// Redirect output to a clientÕs web browser (PDF)
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
					*/
					
					$filename  = "Orden_Servicio_".$dataCita['FOLIO'].".pdf";
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');	
							
				}else{
					echo "no hay informacion 1";
				}
			}else{
				echo "no hay informacio 2n";
			}			
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}	
	}
    
}
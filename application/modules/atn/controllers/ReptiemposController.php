<?php

class atn_ReptiemposController extends My_Controller_Action
{	
	protected $_clase = 'mrtiempos';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/productividad.mx/public';
	//public $realPath='/var/www/vhosts/taccsi.com/htdocs/public'; 		
    
	public function init(){
    	try{	
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
			$this->dataIn 			= $this->_request->getParams();
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

    public function indexAction()
    {
    	try{
    		$this->view->dataUser['allwindow'] = true;   
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$aSucursales 	= "";
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch	= Array(		
								array("id"=>"1",'name'=>'Fecha inicio programada' ),
								array("id"=>"2",'name'=>'Fecha inicio real ' )    );
			$bType 			= 1;
			$bStatus		= -1;
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);
			$aSucursales	= $cInstalaciones->getList($this->view->dataUser['ID_EMPRESA']);			
			
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				
				if(isset($this->dataIn['cboInstalacion']) && $this->dataIn['cboInstalacion']>0){
					$aSucursales	= $this->dataIn['cboInstalacion'];
					$idSucursal		= $this->dataIn['cboInstalacion'];	
				}
				
				$idTecnico	= $this->dataIn['inputTecnicos'];
				$bType		= $this->dataIn['cboTypeSearch'];
				$bStatus	= $this->dataIn['inputStatus'];				
				$bShowUsers=true;
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
				$bShowUsers=true;
				$idSucursal		= "";	
			}
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($aSucursales);
			$dataResume     = $cCitas->getResumetiempo($aSucursales,$dFechaIn,$dFechaFin,$idTecnico,$bType,1);			
			$processData	= $this->processData($dataResume);
						
			$this->view->cInstalaciones 	= $cFunciones->selectDb($dataCenter,$idSucursal);
			$this->view->aTecnicos 			= $cFunciones->selectDb($aTecnicos,$idTecnico);	
			$this->view->aTypeSearchs		= $cFunciones->cbo_from_array($aTypeSearch,$bType);
			$this->view->data 				= $this->dataIn;
			$this->view->showUsers			= $bShowUsers;
			$this->view->aResume 			= $processData;
			$this->view->iStatus			= $bStatus;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }  

    public function processData($dataTable){
    	$cCitas = new My_Model_Citas();
		$result = Array();
		foreach($dataTable as $key => $items){
			$aDataFormularios = $cCitas->getResumenTiempos($items['ID']);
			foreach($aDataFormularios as $key => $otems){
				$items[$otems['CAMPO_BD']] = $otems;							
			}	
			
			$result[] = $items;			
		}
		return $result;    	
    } 

	public function exportallAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();	    	
	    	
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch	= Array(		
								array("id"=>"1",'name'=>'Fecha inicio programada' ),
								array("id"=>"2",'name'=>'Fecha inicio real ' )    );
			$bType 			= 1;
			$bStatus		= -1;	
							
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);
			$aSucursales	= $cInstalaciones->getList($this->view->dataUser['ID_EMPRESA']);

			
			$dFechaIn	= $this->dataIn['inputFechaIn'];
			$dFechaFin	= $this->dataIn['inputFechaFin'];
			$idSucursal	= $this->dataIn['cboInstalacion'];			
			$bShowUsers=true;
							
			$bStatus	  = (isset($this->dataIn['inputStatus']) && $this->dataIn['inputStatus']!="") ? $this->dataIn['inputStatus'] : "-1";					
			$aSucursalesIn= (isset($this->dataIn['cboInstalacion']) && $this->dataIn['cboInstalacion']!="") ? $this->dataIn['cboInstalacion'] : $aSucursales;							
			$bType		  = (isset($this->dataIn['cboTypeSearch']) && $this->dataIn['cboTypeSearch']!="")   ? $this->dataIn['cboTypeSearch']  : "1";
			$idTecnico	  = (isset($this->dataIn['inputTecnicos']) && $this->dataIn['inputTecnicos']!="")   ? $this->dataIn['inputTecnicos']  : "";

			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($aSucursales);
			$dataResume     = $cCitas->getResumetiempo($aSucursales,$dFechaIn,$dFechaFin,$idTecnico,$bType,1);			
			$processData	= $this->processData($dataResume);
			
			//Zend_Debug::dump($processData);die();
			if(count($processData)>0){			
				/** PHPExcel */ 
				require_once 'PHPExcel.php';

					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);											 
					$sHeaderBig   	 = new PHPExcel_Style();
					$stylezebraTable = new PHPExcel_Style();  
					$sHeaderOrange 	 = new PHPExcel_Style();
					$sTittleTable 	 = new PHPExcel_Style();
							
					$stylezebraTable->applyFromArray(array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
						)
					));		

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

					$sHeaderOrange->applyFromArray(array(
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

					$sTittleTable->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FF8000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 10,
					        'name'  => 'Arial'
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
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('REPORTE DE CITAS'));
					$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');												
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Folio Cita');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Tipo Servicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Estatus');										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', 'Fecha/Hora Programada');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', 'Tecnico Asignado');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', 'Fecha Inicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', 'Fecha Fin');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', 'Duracion cita (mins.) ');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', 'Fecha Arribo');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K7', 'Diferencia Arribo (mins.)	');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L7', 'Diferencia inicio cita (mins.)  ');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M7', 'Recepcion Unidad (inicio)');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N7', 'Recepci—n Unidad (fin)');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O7', 'Recepcion Unidad Total (mins.)');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P7', 'Instalacion (inicio)');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q7', 'Instalacion (fin)') ;		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R7', 'Instalacion Total (mins.)');		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S7', 'Pruebas y validacion (inicio)');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T7', 'Pruebas y validacion (fin)');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U7', 'Pruebas y validacion Total (mins.)');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleTable, 'A7:U7');
									
					$rowControl		= 8;
					$zebraControl  	= 0;
					
					foreach($processData as $key => $items){
						$dataEquipment = Array();
						$bPrint = ($bStatus==-1) ? true : (($bStatus==$items['IDE']) ? true: false);
						if($bPrint){											
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['N_TIPO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['NOMBRE_CLIENTE']);	
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['F_PROGRAMADA']." ".$items['H_PROGRAMADA']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['NOMBRE_TECNICO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_INICIO']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['FECHA_TERMINO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControl), $items['DIF_FIN']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,  ($rowControl), @$items['FECHA_ARRIBO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, ($rowControl), @$items['DIF_ARRIBO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, ($rowControl), @$items['DIF_INICIO']);							
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, ($rowControl), @$items['FECHA_RECEPCION_UNIDAD']['FECHA_INICIAL']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, ($rowControl), @$items['FECHA_RECEPCION_UNIDAD']['FECHA_FIN']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, ($rowControl), @$items['FECHA_RECEPCION_UNIDAD']['DIF_CAPTURA']);							
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, ($rowControl), @$items['FECHA_INICIO_INSTALACION']['FECHA_INICIAL']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16, ($rowControl), @$items['FECHA_INICIO_INSTALACION']['FECHA_FIN']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(17, ($rowControl), @$items['FECHA_INICIO_INSTALACION']['DIF_CAPTURA']);	
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(18, ($rowControl), @$items['FECHA_PRUEBAS']['FECHA_INICIAL']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(19, ($rowControl), @$items['FECHA_PRUEBAS']['FECHA_FIN']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(20, ($rowControl), @$items['FECHA_PRUEBAS']['FECHA_INICIAL']);
							
							if($zebraControl++%2==1){
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':U'.$rowControl);			
							}
							
							$rowControl++;							
						}
					}		
								
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);					
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setAutoSize(true);	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setAutoSize(true);			
						
					$filename  = "Reporte_Tiempos_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header("Content-Disposition: attachment; filename=$filename"); 
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');								
			}else{
				echo "No Hay informaciÑn";
			}
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}    
}
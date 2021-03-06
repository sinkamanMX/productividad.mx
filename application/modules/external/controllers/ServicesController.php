<?php

class external_ServicesController extends My_Controller_Action
{	
	protected $_clase = 'mreporte';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/productividad.mx/public';
		
    public function init()
    {
    	try{	
			$sessions = new My_Controller_AuthContact();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/external/login/index');		
			}
			
			$this->dataIn 			= $this->_request->getParams();
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
			$this->view->bUserContact = true;		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

    public function indexAction()
    {
    	try{
			$cFunciones		= new My_Controller_Functions();
			$cCitas			= new My_Model_Citas();			
    		$dFechaIn		= '';
			$dFechaFin		= '';
			
    		if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];			
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
			}			
			
			$dataResume     = $cCitas->getResumeContact($this->view->dataUser['ID_CLIENTE'],$dFechaIn,$dFechaFin);
			$dataProcess	= $cFunciones->setResume($dataResume);			
			
			$this->view->data 				= $this->dataIn;
			$this->view->dataResume 	 	= $dataProcess;
			$this->view->dataResumeTotal 	= $dataProcess['TOTAL'];
			$this->view->aResume 			= $dataResume;
			unset($this->view->dataResume['TOTAL']);	
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }

	public function exportallAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();	    	
	    	
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
						
			$dFechaIn		= '';
			$dFechaFin		= '';

			$dFechaIn	= $this->dataIn['inputFechaIn'];
			$dFechaFin	= $this->dataIn['inputFechaFin'];

			$dataResume     = $cCitas->getResumeContact($this->view->dataUser['ID_CLIENTE'],$dFechaIn,$dFechaFin);
			$dataProcess	= $cFunciones->setResume($dataResume);

			if(count($dataResume)>0){
				/** PHPExcel */ 
				require_once 'PHPExcel.php';	

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
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', 'Fecha Programada');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', 'Hora Programada');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', 'Hora Inicio');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', 'Hora Terminado');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', 'Tecnico Asignado');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', 'Direccion Cita');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleTable, 'A7:J7');														
				
				$rowControl		= 8;
				$zebraControl  	= 0;
				
					foreach($dataResume as $key => $items){						
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['N_TIPO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['NOMBRE_CLIENTE']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['F_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['H_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_INICIO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['FECHA_TERMINO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControl), $items['NOMBRE_TECNICO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,  ($rowControl), $items['DIRECCION']);

						if($zebraControl++%2==1){
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':J'.$rowControl);			
						}
						$rowControl++;
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
						
					$filename  = "Reporte_Citas_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header("Content-Disposition: attachment; filename=$filename"); 
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');							
			}else{
				echo "No Hay informaci�n";
			}
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}

}
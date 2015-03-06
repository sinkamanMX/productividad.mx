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
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);											 
					$styleHeader = new PHPExcel_Style();
					$styleAutor	 = new PHPExcel_Style();
					$styleTittle = new PHPExcel_Style();
					$styleHeadermin = new PHPExcel_Style();
					$allBlank	 = new PHPExcel_Style(); 
					$stylezebraTable = new PHPExcel_Style();  
					
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

		
					$stylezebraTable->applyFromArray(array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
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
					        'name'  => 'Arial'
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
					        'name'  => 'Arial'
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
					        'name'  => 'Arial'
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
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($allBlank, 'A1:Z99');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Reporte de Citas');
					$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A2:H2');
					$objPHPExcel->getActiveSheet()->getStyle('A2:H2')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAutor, 'A3:H3');
					$objPHPExcel->getActiveSheet()->getStyle('A3:H3')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
				
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Folio Cita');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Estatus');										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', 'Fecha Programada');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E5', 'Hora Programada');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F5', 'Hora Inicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G5', 'Hora Terminado');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H5', 'Tecnico Asignado');					
														
					$rowControl		= 7;
					$zebraControl  	= 0;
					
					foreach($dataResume as $key => $items){						
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['NOMBRE_CLIENTE']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['F_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['H_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['FECHA_INICIO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_TERMINO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['NOMBRE_TECNICO']);

						if($zebraControl++%2==1){
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':H'.$rowControl);			
						}
						$rowControl++;
					}
								
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(5);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(5);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);	

					/*
					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(true);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(true);		*/			
				
					$filename  = "Reporte_Citas_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
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
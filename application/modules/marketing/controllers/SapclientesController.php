<?php

class marketing_SapclientesController extends My_Controller_Action
{
	protected $_clase = 'msapclientes';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;
	//public $pathCodes= '/qrcodes/';
	public $realPath  ='/var/www/vhosts/sima/htdocs/public';
	//public $pathCodes ='/Users/itecno2/Documents/workspace/zendblog.net/reporte_siames/codesqr/codes/';

    public function init()
    {
    	try{
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
		
		$this->dataIn = $this->_request->getParams();
		$this->validateNumbers = new Zend_Validate_Digits();
				
		if(isset($this->dataIn['optReg'])){
			$this->operation = $this->dataIn['optReg'];
			
			if($this->operation=='update'){
				$this->operation = $this->dataIn['optReg'];

				$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
			}	
		}
		
		if(isset($this->dataIn['catId']) && $this->dataIn['catId']!=""){
			$this->idToUpdate 	   = $this->dataIn['catId'];	
		}else{
			$this->idToUpdate 	   = -1;
			$this->errors['status'] = 'no-info';
		}	

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }

    
    public function indexAction(){
    	try{	    	
			$classObject = new My_Model_Sapclientes();
			$this->view->datatTable = $classObject->getDataTables();    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }   

    public function getinfoAction(){
    	try{	  
    		//require_once 'libs/qr/phpqrcode.php';	
			$dataInfo = Array();
			$dataTable= Array();
			$classObject 	= new My_Model_Sapclientes();
			$functions 		= new My_Controller_Functions();
	
	        if($this->idToUpdate >-1){
	        	if($this->operation=='new'){	
	        		$totalQr 		= $classObject->getTotalQrByClient($this->idToUpdate)+1;
	        		$countGenerate 	= $this->dataIn['txtTotalCodes'];
	        		
	        		for($i=0;$i<$countGenerate;$i++){
	        			$nameFile	 = $this->idToUpdate.'-'.$totalQr;
						//$nameImageQr = $this->pathCodes.$nameFile;
												
	        			$dataInsert = Array();
	        			$dataInsert['codCliente'] 	= $this->idToUpdate;
	        			$dataInsert['CadenaQr'] 	= $nameFile;
	        			
	        			$insertCode = $classObject->insertRow($dataInsert);
	        			if(!$insertCode){
	        				break;
	        				$this->errors['insert']= true;
	        			}
	        			
	        			$totalQr++;							
					
						/*
						QRcode::png($nameFile, $nameImageQr.'.png'); 
						
						if(file_exists($nameImageQr.'.png')){
		        			$dataInsert = Array();
		        			$dataInsert['codCliente'] 	= $this->idToUpdate;
		        			$dataInsert['nameImage'] 	= $nameFile.'.png';
		        			$dataInsert['CadenaQr'] 	= $nameFile;
		        			
		        			$insertCode = $classObject->insertRow($dataInsert);
		        			if(!$insertCode){
		        				break;
		        				$this->errors['insert']= true;
		        			}
		        			
		        			$totalQr++;														
						}else{
							$this->errors['insert']= true;
						}	
						*/					
	        		}

	        		if(!isset($this->errors['insert'])){
	        			$this->view->okCodes = true;
	        		}
	        	}
				$dataInfo	= $classObject->getData($this->idToUpdate);
	        	$dataTable  = $classObject->getDataTablesQr($this->idToUpdate);
			}else{
				$this->_redirect('/marketing/sapclientes/index');
			}
						
			$this->view->data 		= $dataInfo;	
			$this->view->dataTable 	= $dataTable;
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function exportcardAction(){
    	try{
			$dataInfo = Array();    		
			$classObject 	= new My_Model_Sapclientes();
			
			if($this->idToUpdate >-1){
				$dataInfo	= $classObject->getDataQr($this->idToUpdate);
				$bFileCodeQr= file_exists($this->realPath.'/movi/txt_'.$dataInfo['CADENA_QR'].'.png');
				$bFileTxtCode = file_exists($this->realPath.'/movi/'.$dataInfo['CADENA_QR'].'.png');
								
				if($bFileCodeQr && $bFileTxtCode){
					$this->_helper->layout->disableLayout();
					$this->_helper->viewRenderer->setNoRender();
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
					$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);	
											
					$objDrawing = new PHPExcel_Worksheet_Drawing();
	
					$objDrawing->setName('TargetFront');
					$objDrawing->setDescription('TargetFront');				
					$objDrawing->setPath($this->realPath.'/movi/targetFront.jpg');
					$objDrawing->setCoordinates('C1');
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
														
					$objDrawBack = new PHPExcel_Worksheet_Drawing();				
					$objDrawBack->setName('targetBack');
					$objDrawBack->setDescription('targetBack');				
					$objDrawBack->setPath($this->realPath.'/movi/targetBack.jpg');
					$objDrawBack->setCoordinates('C20');
					$objDrawBack->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
									
					$objDrawTxtQr = new PHPExcel_Worksheet_Drawing();				
					$objDrawTxtQr->setName('targetBack');
					$objDrawTxtQr->setDescription('targetBack');				
					$objDrawTxtQr->setPath($this->realPath.'/movi/txt_'.$dataInfo['CADENA_QR'].'.png');
					$objDrawTxtQr->setCoordinates('C26');
					$objDrawTxtQr->setOffsetX(40);
					$objDrawTxtQr->setOffsetY(10);
					$objDrawTxtQr->setWorksheet($objPHPExcel->setActiveSheetIndex(0));	
	
					$objDrawQr = new PHPExcel_Worksheet_Drawing();				
					$objDrawQr->setName('targetBack');
					$objDrawQr->setDescription('targetBack');				
					$objDrawQr->setPath($this->realPath.'/movi/'.$dataInfo['CADENA_QR'].'.png');
					$objDrawQr->setCoordinates('C23');
					$objDrawQr->setOffsetX(190);
					$objDrawQr->setOffsetY(5);
					$objDrawQr->setWidth(160);
					$objDrawQr->setHeight(160);
					$objDrawQr->setWorksheet($objPHPExcel->setActiveSheetIndex(0));						
									
					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(false);					
	
					$filename  = "Tarjeta_".$dataInfo['CADENA_QR'].".pdf";
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');											
				}
			}			
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}  		
    }
    
    public function exportallcardAction(){
    	try{
    		ini_set('memory_limit', '1024M');
			$dataInfo = Array();    		
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$classObject 	= new My_Model_Sapclientes();
			
			if($this->idToUpdate >-1){
				$dataFiles = $classObject->getData($this->idToUpdate);
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
				//$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
						
				$allCodes  = $classObject->getDataTablesQr($this->idToUpdate);
				$countTab  = 0;
				
				foreach($allCodes as $key => $items){
					if($countTab>0){
						$objPHPExcel->createSheet();
						$objPHPExcel->setActiveSheetIndex($countTab);  						 
					}
					$dataInfo	= $classObject->getDataQr($items['ID_QR']);
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName('TargetFront');
					$objDrawing->setDescription('TargetFront');				
					$objDrawing->setPath($this->realPath.'/movi/targetFront.jpg');
					$objDrawing->setCoordinates('C1');
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex($countTab));
														
					$objDrawBack = new PHPExcel_Worksheet_Drawing();				
					$objDrawBack->setName('targetBack');
					$objDrawBack->setDescription('targetBack');				
					$objDrawBack->setPath($this->realPath.'/movi/targetBack.jpg');
					$objDrawBack->setCoordinates('C20');
					$objDrawBack->setWorksheet($objPHPExcel->setActiveSheetIndex($countTab));
					
					$objDrawTxtQr = new PHPExcel_Worksheet_Drawing();				
					$objDrawTxtQr->setName('targetBack');
					$objDrawTxtQr->setDescription('targetBack');				
					$objDrawTxtQr->setPath($this->realPath.'/movi/txt_'.$dataInfo['CADENA_QR'].'.png');
					$objDrawTxtQr->setCoordinates('C26');
					$objDrawTxtQr->setOffsetX(40);
					$objDrawTxtQr->setOffsetY(10);
					$objDrawTxtQr->setWorksheet($objPHPExcel->setActiveSheetIndex($countTab));	
	
					$objDrawQr = new PHPExcel_Worksheet_Drawing();				
					$objDrawQr->setName('targetBack');
					$objDrawQr->setDescription('targetBack');				
					$objDrawQr->setPath($this->realPath.'/movi/'.$dataInfo['CADENA_QR'].'.png');
					$objDrawQr->setCoordinates('C23');
					$objDrawQr->setOffsetX(190);
					$objDrawQr->setOffsetY(5);
					$objDrawQr->setWidth(160);
					$objDrawQr->setHeight(160);
					$objDrawQr->setWorksheet($objPHPExcel->setActiveSheetIndex($countTab));						
									
					$objPHPExcel->setActiveSheetIndex($countTab)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex($countTab)->setPrintGridLines(false);					
					$countTab++;
				}
	
				$filename  = "Tarjetas_".$dataFiles['COD_CLIENTE'].".pdf";
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');									
				
				//$objWriter = PHPExcel_Writer_PDF::  ($objPHPExcel, 'PDF');
				$objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
				$objWriter->writeAllSheets();
				$objWriter->save('php://output');	
				
				/*
					$filename  = "Tarjetas_".$dataFiles['COD_CLIENTE'].".xlsx";
					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->writeAllSheets();
					$objWriter->save('php://output');
					*/				
			}						
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}  		
    } 

	public function exportallAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			$classObject = new My_Model_Sapclientes();
			$aDataTable  = $classObject->getDataTables();
			
			if(count($aDataTable)>0){
				// PHPExcel 
				require_once 'PHPExcel.php';
				// PHPExcel_Writer_Excel2007 								
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
				
				// 
				// Header del Reporte
				//					
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
				$objDrawing->setCoordinates('D2');
				
				$objPHPExcel->getActiveSheet()->getRowDimension('D2')->setRowHeight(150);										
				$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('REPORTE DE CLIENTES SAP'));
				$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');						
				

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', '# SAP');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Nombre');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Razon Social');										
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Codigos Qr Activados');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', 'Codigos Qr Sin Activados');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', 'Codigos Qr Total');
												
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleTable, 'A7:F7');				
				
				$rowControl		= 8;
				$zebraControl  	= 0;

				foreach($aDataTable as $key => $items){
					$varCliente = ($items['NAME']!='NULL') ? $items['NAME']: '--';
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['COD_CLIENTE']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $varCliente);										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['RAZON_SOCIAL']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['ACTIVATE_QR']);								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['INACTIVATE_QR']);								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['TOTAL_QR']);													

					if($zebraControl++%2==1){
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':F'.$rowControl);			
					}
					$rowControl++;
				}								
				
				
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
				
				$filename  = "Reporte_ClientesSap_".date("YmdHi").".xlsx";	

				header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
				header("Content-type:   application/x-msexcel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false);
				
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');						
			}else{
				echo "No Hay informaci—n";
			}		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}  

	public function migrateunitsAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			$dataInfo = Array();
			$dataTable= Array();
			$classObject 	= new My_Model_Sapclientes();
			$functions 		= new My_Controller_Functions();
			$cMutUnidades	= new My_Model_MutUnidades();
	
	        if(isset($this->dataIn['strInput']) && $this->dataIn['strInput'] != ""){
	        	$dataInfo	= $classObject->getData($this->dataIn['strInput']);
	        	$dataTable  = $cMutUnidades->getDataTable($dataInfo['COD_CLIENTE']);
			}
						
			$this->view->data 		= $dataInfo;	
			$this->view->dataTable 	= $dataTable;
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;	
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}
	
	public function processunitsAction(){
    	try{
    		$this->view->layout()->setLayout('layout_blank');
			$dataInfo = Array();
			$classObject 	= new My_Model_Sapclientes();
			$functions 		= new My_Controller_Functions();
			$cMutUnidades	= new My_Model_MutUnidades();
			$c = 0;
	        if(isset($this->dataIn['strInput']) && $this->dataIn['strInput'] != ""){
	        	$dataInfo	= $classObject->getData($this->dataIn['strInput']);
	        	//http://201.131.96.40
	        	//http://192.168.6.41
	        	$soap_client  = new SoapClient("http://ws.grupouda.com.mx/wsUDAHistoryGetByPlate.asmx?WSDL");
	        	//$soap_client = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
   				$aParams 	 = array('sLogin'         => 'wbs_admin@grupouda.com.mx',
	                  				'sPassword'       => 'w3b4dm1n',
	                  				'strCustomerPass' => $dataInfo['COD_CLIENTE']);
   				
   				$result = $soap_client->HistoyDataLastLocationByCustomerPass($aParams);
   				if (is_object($result)){
					$x = get_object_vars($result);
					$y = get_object_vars($x['HistoyDataLastLocationByCustomerPassResult']);
					
					$xml = $y['any'];		
					if($xml2 = simplexml_load_string($xml)){
						$cuenta = count($xml2->Response->Plate);
       					
       					for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
							$dataInsert = Array();
							$dataInsert['gpsdate'] 	= (string)$xml2->Response->Plate[$i]->hst->DateTimeGPS;
							$dataInsert['latitud'] 	= (string)$xml2->Response->Plate[$i]->hst->Latitude;
							$dataInsert['longitud'] = (string)$xml2->Response->Plate[$i]->hst->Longitude;
							$dataInsert['speed'] 	= (string)$xml2->Response->Plate[$i]->hst->Speed;
							$dataInsert['heading'] 	= (string)$xml2->Response->Plate[$i]->hst->Heading;
							$dataInsert['eventid'] 	= (string)$xml2->Response->Plate[$i]->hst->EventID;
							$dataInsert['event'] 	= (string)$xml2->Response->Plate[$i]->hst->Event;
							$dataInsert['imei'] 	= (string)$xml2->Response->Plate[$i]->hst->Imei;
							$dataInsert['ignition'] = (string)$xml2->Response->Plate[$i]->hst->IgnitionState;
							$dataInsert['fleet'] 	= (string)$xml2->Response->Plate[$i]->hst->Fleet;
							$dataInsert['ip'] 		= (string)$xml2->Response->Plate[$i]->hst->IP;
							$dataInsert['devicename'] = (string)$xml2->Response->Plate[$i]->hst->DeviceName;    
							$dataInsert['devicedesc'] = (string)$xml2->Response->Plate[$i]->hst->DeviceDesc;
							$dataInsert['lcoation'] = (string)$xml2->Response->Plate[$i]->hst->Location; 

							$validateUnit = $cMutUnidades->validateUnitByimei($dataInsert['imei']);
					          
							if(!$validateUnit['status']){
								$dataInsert['codCliente'] = $dataInfo['COD_CLIENTE'];
					        	$insertunit = $cMutUnidades->insertRow($dataInsert);
								if(!$insertunit){
					          		$errors[$c] = $sImei;
					          	}
							}else{
								$dataInsert['idUnit'] = $validateUnit['data']['ID_UNIDAD'];
					          	$updateUnit = $cMutUnidades->updateRow($dataInsert);
					          	if(!$updateUnit){
					          		$errors[$c] = $sImei;
					          	}
							}
				        	$c = $c+1;
				       }
						
						if($c >0){
			        		$this->_redirect('/marketing/sapclientes/migrateunits?strInput='.$dataInfo['COD_CLIENTE']);	
			        	}elseif($c==0){	
			        		$this->errors['no-units'] = 1;
			        	}											
					}else{
						$this->errors['no-info'] = 1;
					}
	        	}else{
					$this->errors['no-service'] = 1;
				}
			}

			$this->view->bPageAll = true;
    		$this->view->resultOp = $this->resultop;
    		$this->view->errors	  = $this->errors;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 			
	}
	
	public function clientformsAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			$dataInfo = Array();
			$dataTable= Array();
			$classObject 	= new My_Model_Sapclientes();
			$functions 		= new My_Controller_Functions();
			$cFormularios   = new My_Model_Formularios();
	
	        if(isset($this->dataIn['strInput']) && $this->dataIn['strInput'] != ""){
	        	$dataInfo	= $classObject->getData($this->dataIn['strInput']);	        	
	        	$dataTable	= $cFormularios->getDataByClient($dataInfo['ID_CLIENTE']);
	        	
	        	if($this->operation=='updateListForms'){
					$iControlE = 0;
					$aValuesForm = $this->dataIn['formsValues'];
					if(count($aValuesForm)>0){		
						$delete = $classObject->deleteForms($dataInfo['ID_CLIENTE']);					
						for($i=0;$i<count($aValuesForm);$i++){
							$aResult = false;
							
							if(isset($aValuesForm[$i]) && @$aValuesForm[$i]!=""){
								$idFormulario = $aValuesForm[$i];							
								$updateRel    = $classObject->updateRelForm($dataInfo['ID_CLIENTE'],$idFormulario);
								if($updateRel['status']){
									$aResult = true;
								}
							}else{
								$aResult = true;
							}
								
							if($aResult){
								$iControlE++;
							}
						}
						
						if($iControlE==count($aValuesForm)){
							$dataTable	= $cFormularios->getDataByClient($dataInfo['ID_CLIENTE']);
		    				$this->resultop = 'okRegister';		    				
						}
					}
					$this->resultop = 'okRegister';					      	
	        	}	        			
	        	$dataTable	= $cFormularios->getDataByClient($dataInfo['ID_CLIENTE']);
			}			
						
			$this->view->data 		= $dataInfo;
			$this->view->dataTable 	= $dataTable;
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
	}
	
	public function processformAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			$dataInfo = Array();
			$dataTable= Array();
			$classObject 	= new My_Model_Sapclientes();
			$functions 		= new My_Controller_Functions();
			$cFormularios   = new My_Model_Formularios();
	
	        if(isset($this->dataIn['strInput']) && $this->dataIn['strInput'] != ""){
	        	$dataInfo	= $classObject->getData($this->dataIn['strInput']);	        	
	        	$dataTable	= $cFormularios->getDataByClient($dataInfo['ID_CLIENTE']);
	        	$codeReset  = $cFormularios->getRandomCodeReset();
	        	$sUrl		= '/marketing/sapclientes/clientforms?strInput='.$dataInfo['ID_CLIENTE']."&codeR=".$codeReset;
		    	$this->_redirect($sUrl);
	        }
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	        			
	}
}	
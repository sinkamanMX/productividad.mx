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
}	
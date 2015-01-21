<?php 

class atn_AutorizacionController extends My_Controller_Action
{
	protected $_clase = 'mautorizacion';
	public $dataIn;	
	public $aService;
		
    public function init()
    {
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
			$cFunciones		= new My_Controller_Functions();			
			$cCitas			= new My_Model_Citas();

			$dataResume     	= $cCitas->getPendientesbyEmpresa($this->view->dataUser['ID_EMPRESA']);
			$this->view->aResume= $dataResume;			

        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
	public function citadetalleAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			 			
			$cCitas     = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$cUsuarios  = new My_Model_Usuarios();
			
			$dataStatus = $cCitas->getCboStatus();
			$dataUsers  = $cUsuarios->getCbOperadores();
			$dataDate = Array();
			$dataStat = '';
			$opAsign  = '';
			$statusOpr = false;
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$iIdCita	= $this->dataIn['strInput'];					
				if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=="opSearch"){
					$sRandomNumber = $this->getCodeValidate();					
					$this->dataIn['codeValidate']= $sRandomNumber;
					$this->dataIn['ID_USUARIO']  = $this->view->dataUser['ID_USUARIO'];
							
					$bDataUpdate  = $cCitas->validateDate($this->dataIn);
					if($bDataUpdate){
						$dataDate   = $cCitas->getCitasDet($iIdCita);
						
						$cHttpService = new Zend_Http_Client();
						$sUrl = "http://192.168.6.116/siames/sap_update_monitoreo.php?folio=".$dataDate['FOLIO'];
						$cHttpService->setUri($sUrl);
						$response = $cHttpService->request();	
						$statusOpr = true;				
					}
				}				
				$dataDate   = $cCitas->getCitasDet($iIdCita);
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus,$dataStat);
			$this->view->personal = $funtions->selectDb($dataUsers,$opAsign);
			$this->view->data     = $dataDate;
			$this->view->statusOpr= $statusOpr;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	}    
	
	public function getCodeValidate(){
		$sCodeRandom = ''; 
		$cCitas     = new My_Model_Citas();
		$funtions   = new My_Controller_Functions();
		$sRandomNumber = $funtions->getRandomCode();
		
		$validateRandom = $cCitas->getValidFolioAut($sRandomNumber,$this->view->dataUser['ID_EMPRESA']);
		if($validateRandom){
			$sCodeRandom = $this->getCodeValidate();
		}else{
			$sCodeRandom = $sRandomNumber;
		}
		
		return $sCodeRandom;
	}
	/*
	 * 1.- Que tenga folio de sap
	 * 2.- Que este terminada
	 * 3.- Que las fotos existan.
	 * 4.- Copiar a carpeta 
	 * 
	 * agregar en tabla de citas campo que diga enviado_sap
	 * 
	 * */
	
	public function updateServices($codFolio){
    	try{   			    

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
	}
}
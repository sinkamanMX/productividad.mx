<?php

class contacts_MainController extends My_Controller_Action
{	
	public $dataIn = NULL;
	public $aErrors= -1;
    public function init()
    {
		$this->view->layout()->setLayout('public');
		/*
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
		*/
		$this->dataIn = $this->_request->getParams();
		
    }

    public function indexAction()
    {
		try{
			$statusHeader = true;
			$cSapClientes = new My_Model_Sapclientes();			
			$codClient = -1;
			$aDataCodes = Array();
			$bStatusSearch = 0;
			
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']=='search' && 
			   isset($this->dataIn['inputCodeClient']) && $this->dataIn['inputCodeClient']!=""){
				$codClient = $this->dataIn['inputCodeClient'];
				
				$aDataCliente = $cSapClientes->getData($codClient);
				if(count($aDataCliente)>0 && isset($aDataCliente['ID_CLIENTE'])){
					$aDataCodes = $cSapClientes->getDataTablesQr($codClient);
					if(count($aDataCodes)==0){						
						$this->aErrors = 2;
					}else{
						$bStatusSearch=1;
						$statusHeader = false;
					}
				}else{
					$this->aErrors 	= 1;
				}
			}			
			
	
			$this->view->codeClient    = $codClient;
			$this->view->aErrors 	   = $this->aErrors;
			$this->view->datatTable    = $aDataCodes;
			$this->view->bStatusSearch = $bStatusSearch;
			$this->view->showHeader    = $statusHeader;			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }    
    
    public function activationAction(){
		try{
			$aContactInfo = Array();
			$aDataCliente = Array();
			$aDataQr      = Array();
			$cSapClientes = new My_Model_Sapclientes();	
			$cFunctions   = new My_Controller_Functions();	
			$sGenero      = '';
			$flagOnlyShow = false;
			if(isset($this->dataIn['activationCode'])  && $this->dataIn['activationCode'] !="" && 
			   isset($this->dataIn['inputCodeClient']) && $this->dataIn['inputCodeClient']!=""){
			   	$codeQrActivate = $this->dataIn['activationCode'];
			   	$codeClient		= $this->dataIn['inputCodeClient'];
			   	$aContactInfo   = $cSapClientes->getContactInfo($codeQrActivate,$codeClient);
				$aDataCliente   = $cSapClientes->getData($codeClient);
				$aDataQr		= $cSapClientes->getDataQr($codeQrActivate);
				
				if(count($aContactInfo)>0){
					$sGenero        = @$aContactInfo['GENERO'];
					$flagOnlyShow   = true;
				}
				
				if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']=='new'){
					$insertContact = $cSapClientes->insertRowContact($this->dataIn);
					if($insertContact){
						$cSapClientes->updateActivation($codeQrActivate);
						$this->_redirect('/contacts/main/activation?activationCode='.$codeQrActivate.'&inputCodeClient='.$codeClient);
					   	/*$aContactInfo   = $cSapClientes->getContactInfo($codeQrActivate,$codeClient);
						$aDataCliente   = $cSapClientes->getData($codeClient);
						$aDataQr		= $cSapClientes->getDataQr($codeQrActivate);						
						$sGenero        = $aContactInfo['GENERO'];
						$flagOnlyShow   = true;	*/				
					}
				}
			   	
			}else{
				$this->_redirect('/contacts/main/index');	
			}
			
			$this->view->aDataInfo   = $aContactInfo;
			$this->view->codeClient  = $aDataCliente;
			$this->view->dataQr 	 = $aDataQr;
			$this->view->sGenero	 = $cFunctions->cboGenero($sGenero);
			$this->view->onlyShow	 = $flagOnlyShow;
			$this->view->dataIn 	 = $this->dataIn;
			$this->view->showHeader  = false;		
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
}
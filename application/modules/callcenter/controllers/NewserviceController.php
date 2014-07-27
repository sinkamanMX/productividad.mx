<?php

class callcenter_NewserviceController extends My_Controller_Action
{	
	protected $_clase = 'mcallcenter';
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
			$functions = new My_Controller_Functions();
			$estados   = new My_Model_Estados();
			$aEstados  = $estados->getCbo();
			$aMunicipios=new My_Model_Municipios();
			$aColonias = new My_Model_Colonias();
			 
			$aNamespace = new Zend_Session_Namespace("sService");
			$this->view->estados= $functions->selectDb($aEstados);
			$this->view->genero = $functions->cboGenero();
			$this->view->mismoDomicilio = $functions->cboOptions();

			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->service)){
					unset($aNamespace->service);
				}
				
				$aNamespace->service = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/instalation');	
			}
			
			if(isset($aNamespace->service)){
				$this->view->data   = $aNamespace->service;
				$this->view->estados= $functions->selectDb($aEstados,$aNamespace->service['inputEstado']);
				$this->view->genero = $functions->cboGenero($aNamespace->service['inputGenero']);
				$this->view->mismoDomicilio = $functions->cboOptions($aNamespace->service['inputDom']);
				$dMunicipios = $aMunicipios->getCbo($aNamespace->service['inputEstado']);
				$this->view->municipios = $functions->selectDb($dMunicipios,$aNamespace->service['inputMunicipio']);
				$dColonia    = $aColonias->getCbo($aNamespace->service['inputMunicipio']);
				$this->view->colonias         = $functions->selectDb($dColonia,$aNamespace->service['inputcolonia']); 
			}
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function instalationAction(){
		try{
		    $aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service)){
				$this->_redirect('/callcenter/newservice/index');				
			}
			
			$this->view->dataService = $aNamespace->service;
			$cEstados   = new My_Model_Estados();
			$cMunicipios=new My_Model_Municipios();
			$cColonias = new My_Model_Colonias();			

			$estado 	= $cEstados->getData($aNamespace->service['inputEstado']);
			$municipio 	= $cMunicipios->getData($aNamespace->service['inputMunicipio'],$aNamespace->service['inputEstado']);
			$colonia 	= $cColonias->getData($aNamespace->service['inputcolonia'],$aNamespace->service['inputMunicipio']);
			
			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->direction)){
					unset($aNamespace->direction);
				}
				$aNamespace->direction = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/cardetail');	
			}

			if(isset($aNamespace->direction)){
				$this->view->data   = $aNamespace->direction;
			}
				
			$this->view->direccion = "Mexico,".$estado['NOMBRE'].",".$municipio['NOMBRE'].",".$colonia['NOMBRE'].",".$aNamespace->service['inputCP'].",".$aNamespace->service['inputStreet'];
			$cinstalaciones = new My_Model_Cinstalaciones();
			$this->view->cInstalaciones = $cinstalaciones->getAll($this->view->dataUser['ID_EMPRESA']);	
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
    
    public function cardetailAction(){
		try{
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}

			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->carDetail)){
					unset($aNamespace->carDetail);
				}
				$aNamespace->carDetail = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/datefinish');	
			}

			if(isset($aNamespace->carDetail)){
				$this->view->data   = $aNamespace->carDetail;
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	    	
    }
    
    public function datefinishAction(){
    	try{
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}

			if(isset($this->dataIn['optReg'])){
				$aClienteData = $aNamespace->service;
				$aInstalacion = $aNamespace->direction;
				$aCarDetail	  = $aNamespace->carDetail;
				
				Zend_Debug::dump($aClienteData);
				Zend_Debug::dump($aInstalacion);
				Zend_Debug::dump($aCarDetail);
				Zend_Debug::dump($this->dataIn);
			}   	

    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	 
    }
    
    public function cancelAction(){
    	try{
			$aNamespace = new Zend_Session_Namespace("sService");

    		if(isset($aNamespace->service)){
				unset($aNamespace->service);
			}			
    	    if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}			
			if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}
    		if(isset($aNamespace->carDetail)){
				unset($aNamespace->carDetail);
			}			
			$this->_redirect('/callcenter/newservice/index');				
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	     	
    }
}
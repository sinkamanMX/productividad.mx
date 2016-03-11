<?php
class leasing_PlacesController extends My_Controller_Action
{		
	protected $_clase = 'mplaces';	
	public $validateNumbers;
	public $validateAlpha;
	
	public $_dataIn;
	public $_dataUser;
	public $_dataOp;
	public $_idUpdate = -1;
	public $_aErrors  = Array();	
	public $_resultOp = null;
		
    public function init()
    {
    	try{
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
			$this->_dataUser        = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->_dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
			$this->view->idEmpresa  = $this->_dataUser['ID_EMPRESA'];		
			$this->_dataIn			= $this->_request->getParams();
			$this->validateNumbers = new Zend_Validate_Digits();		
					
			if(isset($this->_dataIn['optReg'])){
				$this->_dataOp	 = $this->_dataIn['optReg'];
				
				if($this->_dataOp=='update'){
					$this->_dataOp = $this->_dataIn['optReg'];
	
					$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
				}	
			}
			
			if(isset($this->_dataIn['catId']) && $this->validateNumbers->isValid($this->_dataIn['catId'])){
				$this->_idUpdate	   = $this->_dataIn['catId'];	
			}else{
				$this->_idUpdate 	   = -1;
				$this->_aErrors['status'] = 'no-info';
			}

			$this->view->dataUser = $this->_dataUser;

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }

    public function indexAction(){
    	try{
	    	$this->view->mOption = 'branches';			
			$cClassObject      = new My_Model_Lugares();
			$iSucursal				= ($this->_dataUser['ID_PERFIL']==20) ?  $this->_dataUser['ID_SUCURSAL'] : '-1';
			$this->view->datatTable = $cClassObject->getDataTable($this->_dataUser['ID_EMPRESA'],$iSucursal);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
    
    public function getinfoAction(){
    	try{
			$aDataInfo 	 = Array();
			$sEstatus    = 1;
			$classObject = new My_Model_Lugares();
			$cFunctions  = new My_Controller_Functions();
			$cClientes	 = new My_Model_Clientesint();
			$iSucursal	 = ($this->_dataUser['ID_PERFIL']==20) ?  $this->_dataUser['ID_SUCURSAL'] : '-1';
			$this->_dataIn['inputSucursal'] = $iSucursal; 
			$aClientes	 = $cClientes->getCbo($this->_dataUser['ID_EMPRESA'],$iSucursal);
			$sCliente	 = '';
			
			$this->_dataIn['inputEmpresa'] = $this->view->idEmpresa;			
			if($this->_idUpdate >-1){
				$aDataInfo  = $classObject->getData($this->_idUpdate);
				$sEstatus	= $aDataInfo['ESTATUS'];
				$sCliente	= $aDataInfo['ID_EMP_CLIENTE'];
			}
			
    	    if($this->_dataOp=='new'){
				$insert = $classObject->insertRowLeasing($this->_dataIn);
				if($insert['status']){
					$this->_idUpdate = $insert['id'];
					$this->resultop  = 'okRegister';	
					$aDataInfo       = $classObject->getData($this->_idUpdate);
					$this->_redirect('/leasing/places/index');
				}else{
					$this->errors['status'] = 'no-insert';
				}
    		}
			else if($this->_dataOp=='update'){				
				if($this->_idUpdate>-1){
					 $updated = $classObject->updateRowLeasing($this->_dataIn,$this->_idUpdate); //mandar el ide del transportista
					 if($updated['status']){
					 	$aDataInfo    = $classObject->getData($this->_idUpdate);
					 	$this->_resultOp = 'okRegister';	
					 	$this->_redirect('/leasing/places/index');
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}	
    		}else if($this->_dataOp=='delete'){
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				$answer = Array('answer' => 'no-data');
				    
				$this->_dataIn['idEmpresa'] = $this->view->idEmpresa;
				$delete = $classObject->deleteRow($this->_dataIn);
				if($delete['status']){
					$answer = Array('answer' => 'deleted'); 
				}
	
		        echo Zend_Json::encode($answer);
		        die();   						
			}
						
			$this->view->aClientes  = $cFunctions->selectDb($aClientes,$sCliente);
			$this->view->status     = $cFunctions->cboStatus($sEstatus);	
			$this->view->data 		= $aDataInfo; 
			$this->view->error 		= $this->_aErrors;	
	    	$this->view->mOption 	= 'mrutas';
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }        
    }  
    
}
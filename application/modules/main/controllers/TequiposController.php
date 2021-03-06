<?php

class main_TequiposController extends My_Controller_Action
{
	protected $_clase = 'mtequipo';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;	

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
		
		if(isset($this->dataIn['catId']) && $this->validateNumbers->isValid($this->dataIn['catId'])){
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
    		$classObject = new My_Model_Tequipos();
    		$this->view->datatTable = $classObject->getDataTables();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  	
    
    public function getinfoAction(){
    	try{
    		$classObject = new My_Model_Tequipos();
    		$cFunciones  = new My_Controller_Functions();
    		$aDataInfo 	 = Array();
    		$sEstatus  	 = '';
    		    		
    		
    	    if($this->idToUpdate >-1){
				$aDataInfo	= $classObject->getData($this->idToUpdate);
				$sEstatus	= $aDataInfo['ESTATUS'];
			}
			
			if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					$updated = $classObject->updateRow($this->dataIn);
					 if($updated['status']){			
						$aDataInfo	= $classObject->getData($this->idToUpdate);
						$sEstatus	= $aDataInfo['ESTATUS'];					
						$this->resultop = 'okRegister';
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}else if($this->operation=='new'){
				$insert = $classObject->insertRow($this->dataIn);			
		 		if($insert['status']){	
		 			$this->idToUpdate = $insert['id'];	
			 		$aDataInfo	= $classObject->getData($this->idToUpdate);
					$sEstatus	= $aDataInfo['ESTATUS'];
						 		
			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
				}
			}	
    		
    		
    		
    		
			$this->view->aEstatus   = $cFunciones->cboStatus($sEstatus); 			
			$this->view->data 		= $aDataInfo; 
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;	
			$this->view->mOption 	= 'mtequipo';	    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
}
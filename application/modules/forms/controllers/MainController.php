<?php

class forms_MainController extends My_Controller_Action
{
	protected $_clase 	  = 'mforms';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;	
	public $_dataUser;
	
	public $arrayTipo = Array();
	
    public function init()
    {
    	try{
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			$this->_dataUser		= $sessions->getContentSession();
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
			
			$this->arrayTipo[0]['id']   = 'M';
			$this->arrayTipo[0]['name'] = 'MT';
			$this->arrayTipo[1]['id']   = 'S';
			$this->arrayTipo[1]['name'] = 'Siames';	

		} catch (Zend_Exception $e) {

            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function indexAction(){
    	try{
    		$classObject = new My_Model_Formularios();
    		$this->view->datatTable = $classObject->getAdminTables();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }      
   
	public function getinfoAction(){
		try{
			$cFormularios = new My_Model_Formularios();
			$cFunctions   = new My_Controller_Functions();
			$cTipos			= new My_Model_TipoFormularios();
			$aTipos			= $cTipos->getCbo();
			
			$aDataInfo	  = Array();
			$aElementos	  = Array();
			$aEstatus	  = '';
			$aQrs  		  = ''; 
			$aFirms 	  = '';
			$aFotos		  = '';
			$aLocalizacion= '';	
			$sTipoForm	  = '';	    

			if($this->idToUpdate>0){
				$aDataInfo 	  = $cFormularios->getData($this->idToUpdate);
				$aEstatus	  = $aDataInfo['ACTIVO'];
				$aQrs  		  = $aDataInfo['QRS_EXTRAS'];
				$aFirms 	  = $aDataInfo['FIRMAS_EXTRAS'];
				$aFotos		  = $aDataInfo['FOTOS_EXTRAS'];
				$aLocalizacion= $aDataInfo['LOCALIZACION'];
				$sTipoForm    = $aDataInfo['TIPO_FORMULARIO'];
				$aElementos	  = $cFormularios->getElementos($this->idToUpdate,$this->_dataUser['ID_EMPRESA']);
			}
			
			if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					$this->dataIn['userRegister']= $this->_dataUser['ID_USUARIO'];
					$updated = $cFormularios->updateRow($this->dataIn);
					 if($updated['status']){	
					 	$aDataInfo 	  = $cFormularios->getData($this->idToUpdate);
					 	$aEstatus	  = $aDataInfo['ACTIVO'];
						$aQrs  		  = $aDataInfo['QRS_EXTRAS'];
						$aFirms 	  = $aDataInfo['FIRMAS_EXTRAS'];
						$aFotos		  = $aDataInfo['FOTOS_EXTRAS'];
						$aLocalizacion= $aDataInfo['LOCALIZACION'];
						$sTipoForm    = $aDataInfo['TIPO_FORMULARIO'];
						$aElementos	  = $cFormularios->getElementos($this->idToUpdate,$this->_dataUser['ID_EMPRESA']);		
						$this->resultop = 'okRegister';
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}else if($this->operation=='new'){
				$this->dataIn['inputEmpresa']= $this->_dataUser['ID_EMPRESA'];
				$this->dataIn['userRegister']= $this->_dataUser['ID_USUARIO'];
				$insert = $cFormularios->insertRow($this->dataIn);			
		 		if($insert['status']){	
		 			$this->idToUpdate = $insert['id'];	
		 			$aDataInfo 	  = $cFormularios->getData($this->idToUpdate);
				 	$aEstatus	  = $aDataInfo['ACTIVO'];
					$aQrs  		  = $aDataInfo['QRS_EXTRAS'];
					$aFirms 	  = $aDataInfo['FIRMAS_EXTRAS'];
					$aFotos		  = $aDataInfo['FOTOS_EXTRAS'];
					$aLocalizacion= $aDataInfo['LOCALIZACION'];
					$sTipoForm    = $aDataInfo['TIPO_FORMULARIO'];
					$aElementos	  = $cFormularios->getElementos($this->idToUpdate,$this->_dataUser['ID_EMPRESA']);		
			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
				}
			}else if($this->operation=='deleteRel'){
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				$answer = Array('answer' => 'no-data');
				    
				$this->dataIn['idEmpresa'] = $this->_dataUser['ID_EMPRESA'];
				
				$delete = $cFormularios->deleteRelAction($this->dataIn);
				if($delete['status']){
					$answer = Array('answer' => 'deleted'); 
				}	
	
		        echo Zend_Json::encode($answer);
		        die();   			
			}
			
			
			if($this->operation=='updateElements'){
				$iControlE = 0;
				$aValuesForm = $this->dataIn['aElements'];
				if(count($aValuesForm)>0){
					for($i=0;$i<count($aValuesForm);$i++){	
						$aResult = false;											
						$aElement = $aValuesForm[$i];
						if($aElement['op']=='new' && $aElement['id']==-1){
							$aResult = $cFormularios->insertElement($aElement,$this->idToUpdate,$this->_dataUser['ID_EMPRESA']);
						}else if($aElement['op']=='up' && $aElement['id']>-1){
							$aResult = $cFormularios->updateRowRel($aElement);
						}else if($aElement['op']=='del' && $aElement['id']>-1){
							$aResult = $cFormularios->deleteRowRel($aElement,$this->idToUpdate);
						}
						
						if($aResult){
							$iControlE++;
						}
					}
					
					if($iControlE==count($aValuesForm)){
						$this->_resultOp = 'okRegister';
						$aElementos	  = $cFormularios->getElementos($this->idToUpdate,$this->_dataUser['ID_EMPRESA']);
						$this->view->eventAction = true;
					}
				}
			}	

			
			
			
			$this->view->aElements	= $this->processFields($aElementos);
			$this->view->aDataInfo 	= $aDataInfo;
			$this->view->aTipoForm  = $cFunctions->cbo_from_array($this->arrayTipo,$sTipoForm);
			$this->view->aEstatus	= $cFunctions->cboStatusString($aEstatus);
			$this->view->aQrs		= $cFunctions->cboStatusYesNo($aQrs);
			$this->view->aFirms		= $cFunctions->cboStatusYesNo($aFirms);
			$this->view->aFotos		= $cFunctions->cboStatusYesNo($aFotos);
			$this->view->aLocal		= $cFunctions->cboStatusYesNo($aLocalizacion);
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;	
			$this->view->dataUser['allwindow'] = true;  	
    		$this->view->selectStatus  = $cFunctions->cboStatusString();
    		$this->view->selectOptions = $cFunctions->cboStatusYesNo();
    		$this->view->selectTypes   = $cFunctions->selectDb($aTipos,'');
			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	} 
	
	public function processFields($aElements){
		$cFunctions 	= new My_Controller_Functions();
		$cFormularios 	= new My_Model_Formularios();
		$cTipos			= new My_Model_TipoFormularios();
		$aTipos			= $cTipos->getCbo();
		$aResult = Array();
		
		foreach($aElements as $key => $items){
			$items['cboStatus'] = $cFunctions->cboStatusString($items['ACTIVO']);
			$items['cboReq']	= $cFunctions->cboStatusYesNo($items['REQUERIDO']);
			$items['cboVal']	= $cFunctions->cboStatusYesNo($items['VALIDAR_LOCAL']);
			$items['cboTipo']	= $cFunctions->selectDb($aTipos,$items['ID_TIPO']);
			$aResult[] = $items;
		}
		
		return $aResult;
	}
	
    public function searchiconsAction(){
    		try{
			$this->view->layout()->setLayout('layout_blank');
			
			$cClassObject = new My_Model_Iconos();
			$this->view->dataTable = $cClassObject->getDataTables();
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }	
}
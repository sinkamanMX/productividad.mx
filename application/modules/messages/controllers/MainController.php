<?php

class messages_MainController extends My_Controller_Action
{
	protected $_clase = 'mnotifs';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;
	
	protected $aSo		= Array(
							array("id"=>"0",'name'=>'Todos'),
							array("id"=>"1",'name'=>'Android'),
							array("id"=>"2",'name'=>'IOS'));
	protected $aUnitTime= Array(
							array("id"=>"M",'name'=>'Minuto(s)'),
							array("id"=>"H",'name'=>'Hora(s)'),
							array("id"=>"D",'name'=>'Dia(s)'));
	protected $aTypeSend= Array(
							array("id"=>"0",'name'=>'Todos'),
							array("id"=>"1",'name'=>'Seleccionados'));	

	protected $aTipoUsuario= Array(
							array("id"=>"0",'name'=>'Usuarios'),
							array("id"=>"1",'name'=>'Taccsistas'));		

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
    		$cMensajes = new My_Model_Mensajes();
    		$aMensajes = $cMensajes->getDataTable();
    		$this->view->aDatatable = $aMensajes;    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }
    
    public function getinfoAction(){
    	try{
    		$cFunctions = new My_Controller_Functions();
    		$cMensajes  = new My_Model_Mensajes();
    		//$cDevices	= new My_Model_Devices();
    		$cClientes  = new My_Model_Clientes();
    		$aDataObject= Array();
    		$sSo		= '';
    		$sUnitTime	= '';
    		$sResend	= '0';
    		$sCount		= '';
    		$sTypeSend  = '';
    		$sMotivo	= '';
    		$aMotivos   = $cMensajes->getTipos();
			$sTipoUsuario= 1;

    	    if($this->idToUpdate>-1){
    			$aDataObject= $cMensajes->getRowInfo($this->idToUpdate);
	    		$sSo		= $aDataObject['TIPO_DISPOSITIVOS'];
	    		$sUnitTime	= $aDataObject['UNIDAD_REPETICION'];
	    		$sResend	= $aDataObject['REENVIAR'];
	    		$sCount		= $aDataObject['ENVIAR_CADA'];
	    		$sTypeSend  = $aDataObject['TIPO_ENVIO'];
	    		$sMotivo 	= $aDataObject['ID_TIPO'];	   	
    		}

    		if($this->operation=='new'){
    			$bInsert = $cMensajes->insertRow($this->dataIn);
    			if($bInsert['status']){
    				$this->idToUpdate = $bInsert['id'];
    				
    			    $iControlE = 0;
					$aValuesForm = $this->dataIn['formsValues'];
					if(count($aValuesForm)>0){
						for($i=0;$i<count($aValuesForm);$i++){
							$aResult  = false;											
							$aElement['idMsg'] 		= $this->idToUpdate;
							$aElement['idRelation'] = $aValuesForm[$i];
							$aResult = $cMensajes->setRelations($aElement);
							if($aResult){
								$iControlE++;
							}
						}
	
						if($iControlE==count($aValuesForm)){				
							$this->redirect('/messages/main/index'); 					
						}
					}else{
						$this->redirect('/messages/main/index');
					}  				
    			}else{
    				$this->_aErrors['no-insert'] = 1;
    			}
    		}else if($this->operation=='update'){
    		    $bUpdate = $cMensajes->updateRow($this->dataIn);
    			if($bUpdate['status']){
    				$delete = $cMensajes->delRelations($this->idToUpdate);
    			    $iControlE = 0;
					$aValuesForm = $this->dataIn['formsValues'];
					if(count($aValuesForm)>0){
						for($i=0;$i<count($aValuesForm);$i++){
							$aResult  = false;											
							$aElement['idMsg'] 		= $this->idToUpdate;
							$aElement['idRelation'] = $aValuesForm[$i];
							
							$aResult = $cMensajes->setRelations($aElement);
							if($aResult){
								$iControlE++;
							}
						}
	
						if($iControlE==count($aValuesForm)){				
							$this->redirect('/messages/main/index'); 					
						}
					}else{
						$this->redirect('/messages/main/index');
					}      				
    			}else{
    				$this->_aErrors['no-insert'] = 1;
    			}
    		}
    		
    		$this->view->aMotivos	= $cFunctions->selectDb($aMotivos,$sMotivo);
    		$this->view->aSo  		= $cFunctions->cbo_from_array($this->aSo,$sSo);
    		$this->view->aUnitTime	= $cFunctions->cbo_from_array($this->aUnitTime,$sUnitTime);
    		$this->view->aTipoUsuarios= $cFunctions->cbo_from_array($this->aTipoUsuario,$sTipoUsuario);
    		$this->view->aReSend	= $cFunctions->cboOptions($sResend);
    		$this->view->aCount		= $cFunctions->cbo_number_on(1,60,$sCount);    		
    		$this->view->aClients   = $cClientes->getDataTable($this->idToUpdate);    	    
    		$this->view->aData		= $aDataObject;
    		$this->view->aTypeSend	= $cFunctions->cbo_from_array($this->aTypeSend,$sTypeSend);
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;    				
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	    	
    }    
    
    
    
    
    
    
    
    
    
    
	
/*
class messages_MainController extends My_Controller_Action
{	
	protected $_clase	= 'messages';	
	//
	// * Funcion que se ejecuta al llamar a este script
	// * Recibe las variables que se reciben, ademas de validar la session del usuario
	// * @see My_Controller_Action::init()
	
	protected $aSo		= Array(
							array("id"=>"0",'name'=>'Todos'),
							array("id"=>"1",'name'=>'Android'),
							array("id"=>"2",'name'=>'IOS'));
	protected $aUnitTime= Array(
							array("id"=>"M",'name'=>'Minuto(s)'),
							array("id"=>"H",'name'=>'Hora(s)'),
							array("id"=>"D",'name'=>'Dia(s)'));
	protected $aTypeSend= Array(
							array("id"=>"0",'name'=>'Todos'),
							array("id"=>"1",'name'=>'Seleccionados'));	

	protected $aTipoUsuario= Array(
							array("id"=>"0",'name'=>'Usuarios'),
							array("id"=>"1",'name'=>'Taccsistas'));							
							
    public function init()
    {
    	try{ 
    		$this->view->layout()->setLayout('admin_layout');
    		
    		$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if($sessions->validateSession()){
		        $this->_dataUser   = $sessions->getContentSession();   		
			}else{
				$this->_redirect("/login/main/index");
			}
			   		
			$this->view->dataUser   = $this->_dataUser;
			$this->view->modules    = $perfiles->getModules($this->_dataUser['TIPO_USUARIO']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
						
			$this->_dataIn 					= $this->_request->getParams();
			$this->_dataIn['userCreate']	= $this->_dataUser['ID_USUARIO'];
			$this->_dataIn['dataIdEmpresa'] = $this->_dataUser['ID_EMPRESA'];
			$this->_dataIn['dataIdCarrier'] = $this->_dataUser['ID_CARRIER'];
	    	if(isset($this->_dataIn['optReg'])){
				$this->_dataOp = $this->_dataIn['optReg'];				
			}
			
			if(isset($this->_dataIn['catId'])){
				$this->_idUpdate = $this->_dataIn['catId'];				
			}	    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }

    public function indexAction(){
    	try{
    		$cMensajes = new My_Model_Mensajes();
    		$aMensajes = $cMensajes->getDataTable();
    		$this->view->aDatatable = $aMensajes;    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }
    
    public function getinfoAction(){
    	try{
    		$cFunctions = new My_Controller_Functions();
    		$cMensajes  = new My_Model_Mensajes();
    		$cDevices	= new My_Model_Devices();
    		$aDataObject= Array();
    		$sSo		= '';
    		$sUnitTime	= '';
    		$sResend	= '0';
    		$sCount		= '';
    		$sTypeSend  = '';
    		$sMotivo	= '';
    		$aMotivos   = $cMensajes->getTipos();
			$sTipoUsuario= 1;
    	    if($this->_idUpdate>-1){
    			$aDataObject= $cMensajes->getRowInfo($this->_idUpdate);
	    		$sSo		= $aDataObject['TIPO_DISPOSITIVOS'];
	    		$sUnitTime	= $aDataObject['UNIDAD_REPETICION'];
	    		$sResend	= $aDataObject['REENVIAR'];
	    		$sCount		= $aDataObject['ENVIAR_CADA'];
	    		$sTypeSend  = $aDataObject['TIPO_ENVIO'];
	    		$sMotivo 	= $aDataObject['ID_TIPO'];	   
	    		$sTipoUsuario=$aDataObject['ENVIAR_TACCSISTA']; 			    	
    		}   

    		if($this->_dataOp=='new'){
    			$bInsert = $cMensajes->insertRow($this->_dataIn);
    			if($bInsert['status']){
    				$this->_idUpdate = $bInsert['id'];
    				
    			    $iControlE = 0;
					$aValuesForm = $this->_dataIn['formsValues'];
					if(count($aValuesForm)>0){
						for($i=0;$i<count($aValuesForm);$i++){
							$aResult  = false;											
							$aElement['idMsg'] 		= $this->_idUpdate;
							$aElement['idRelation'] = $aValuesForm[$i];
							
							$aResult = $cMensajes->setRelations($aElement);
							if($aResult){
								$iControlE++;
							}
						}
	
						if($iControlE==count($aValuesForm)){				
							$this->redirect('/messages/main/index'); 					
						}
					}else{
						$this->redirect('/messages/main/index');
					}  				
    			}else{
    				$this->_aErrors['no-insert'] = 1;
    			}
    		}else if($this->_dataOp=='update'){
    		    $bUpdate = $cMensajes->updateRow($this->_dataIn);
    			if($bUpdate['status']){
    				$delete = $cMensajes->delRelations($this->_idUpdate);
    			    $iControlE = 0;
					$aValuesForm = $this->_dataIn['formsValues'];
					if(count($aValuesForm)>0){
						for($i=0;$i<count($aValuesForm);$i++){
							$aResult  = false;											
							$aElement['idMsg'] 		= $this->_idUpdate;
							$aElement['idRelation'] = $aValuesForm[$i];
							
							$aResult = $cMensajes->setRelations($aElement);
							if($aResult){
								$iControlE++;
							}
						}
	
						if($iControlE==count($aValuesForm)){				
							$this->redirect('/messages/main/index'); 					
						}
					}else{
						$this->redirect('/messages/main/index');
					}      				
    			}else{
    				$this->_aErrors['no-insert'] = 1;
    			}
    		}
    		
    		$this->view->aMotivos	= $cFunctions->selectDb($aMotivos,$sMotivo);
    		$this->view->aSo  		= $cFunctions->cbo_from_array($this->aSo,$sSo);
    		$this->view->aUnitTime	= $cFunctions->cbo_from_array($this->aUnitTime,$sUnitTime);
    		$this->view->aTipoUsuarios= $cFunctions->cbo_from_array($this->aTipoUsuario,$sTipoUsuario);
    		$this->view->aReSend	= $cFunctions->cboOptions($sResend);
    		$this->view->aCount		= $cFunctions->cbo_number_on(1,60,$sCount);
    	  	$this->view->aDrivers   = $cDevices->getDrivers($this->_idUpdate);
    	    $this->view->aUsers     = $cDevices->getUsers($this->_idUpdate);
    		$this->view->aData		= $aDataObject;
    		$this->view->aTypeSend	= $cFunctions->cbo_from_array($this->aTypeSend,$sTypeSend);
			$this->view->errors 	= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;    				
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	    	
    }*/
}
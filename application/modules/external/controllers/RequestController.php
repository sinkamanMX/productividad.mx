<?php 

class external_RequestController extends My_Controller_Action
{
	protected $_clase = 'mrequest';
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

    public function indexAction()
    {
		try{    		 
			$cFunciones		= new My_Controller_Functions();			
			$cSolicitudes	= new My_Model_Solicitudes();

			$this->view->dataTable = $cSolicitudes->getDataTablebyClient($this->view->dataUser['ID_CLIENTE']);
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }

    public function getinfoAction(){    
    	try{    		 
			$dataInfo = Array();
			$classObject 	= new My_Model_Solicitudes();
			$cCitas			= new My_Model_Citas();
			$aTipoServicio	= $cCitas->getCboTipoServicio();
			$cFunctions 	= new My_Controller_Functions();			
			$sTipo			= '';
			
			$this->dataIn['inputCliente'] = $this->view->dataUser['ID_CLIENTE'];
			
			if($this->idToUpdate >-1){
				$dataInfo   = $classObject->getData($this->idToUpdate);
				$sTipo		= $dataInfo['ID_TIPO'];
			}
			
			if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					$updated = $classObject->updateRow($this->dataIn);
					if($updated['status']){
						$dataInfo   = $classObject->getData($this->idToUpdate);
						$sTipo		= $dataInfo['ID_TIPO'];
						
						$sSubject 	= 'Cambio en Solicitud de Cita';
						$sBody  	= 'El cliente <b>'.$this->view->dataUser['RAZON_SOCIAL'].'</b> ha realizado un cambio en la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>'.
									  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
									  '<a href="http://siames.grupouda.com.mx">Da Click Aqui</a><br/>'.
									  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
									  '<b> http://siames.grupouda.com.mx</b>';						
						$cFunctions->sendMailAdmins($sSubject,$sBody);
						//$this->resultop = 'okRegister';
						$this->_redirect('/external/request/index');
					}
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}elseif($this->operation=='new'){				
				$insert = $classObject->insertRow($this->dataIn);			
		 		if($insert['status']){	
		 			$this->idToUpdate = $insert['id'];	
					$dataInfo   = $classObject->getData($this->idToUpdate);
					$sTipo		= $dataInfo['ID_TIPO'];

					$sSubject 	= 'Nueva Solicitud de Cita';
					$sBody  	= 'El cliente <b>'.$this->view->dataUser['RAZON_SOCIAL'].'</b> ha realizado una solicitud de cita en el sistema de Siames<br/>'.
								  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
								  '<a href="http://siames.grupouda.com.mx">Da Click Aqui</a><br/>'.
								  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
								  '<b> http://siames.grupouda.com.mx</b>';						
					$cFunctions->sendMailAdmins($sSubject,$sBody);					
					$this->_redirect('/external/request/index');
			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
				}				
			}	

    		if(count($this->errors)>0 && $this->operation!=""){
    			$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
    			$sTipo						= $this->dataIn['inputTipo'];
    			$dataInfo['UNIDAD']			= $this->dataIn['inputUnidad'];
    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];    				
			}			
			
			$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);
			$this->view->data 		= $dataInfo; 
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;			    	
    	}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }   
}
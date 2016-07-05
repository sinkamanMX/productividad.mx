<?php
class main_BrokersController extends My_Controller_Action
{		
	protected $_clase = 'mbrokers';	
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
	    	$this->view->mOption = 'mcompanies';			
			$cEmpresas      = new My_Model_Empresas();
			
			//$this->view->datatTable = $cRutas->getDataTables();
			$this->view->datatTable = $cEmpresas->getBrokers();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  
    
    public function getinfoAction(){
    	try{
			$cEmpresas 		 = new My_Model_Empresas();
			$cSucursales	 = new My_Model_Sucursales();
			$cUsuarios  	 = new My_Model_Usuarios();
			//$cTransportistas = new My_Model_Transportistas();
			$cFunctions		 = new My_Controller_Functions();
			$sEstatus		 = '';
			$sClientUda		 = 0;
			$sCViajes 		 = 1;
			$sTipoEmpresa	 = '';
			$iTotalAgents	 = 1;
			$aDataInfo		 = Array();
			$aTiposEmpresa   = $cEmpresas->getCboTipos();
			
			$this->_dataIn['inputEmpresa'] = $this->view->idEmpresa;	
			$this->_dataIn['inputTipo']    = 4;		
			if($this->_idUpdate >-1){
				$aDataInfo  = $cEmpresas->getData($this->_idUpdate);
				$sEstatus	= $aDataInfo['ESTATUS'];
				$sTipoEmpresa= $aDataInfo['ID_TIPO_EMPRESA'];
				$iTotalAgents= $aDataInfo['NO_TECNICOS'];
				/*$sClientUda = $aDataInfo['CLIENTE_UDA'];
				$sCViajes 	= $aDataInfo['COBRAR_VIAJES'];*/
			}
			
    		if($this->_dataOp=="new"){
				$validateEmp  = $cEmpresas->validateExist($this->_dataIn['inputRFC']);
				if(count($validateEmp)==0){
					$validateUser = $cUsuarios->userExist($this->_dataIn['inputUser']);
					if(count($validateUser)==0){
						$insertEmpresa = $cEmpresas->insertRow($this->_dataIn);
						if($insertEmpresa['status']){
							$idEmpresa = $insertEmpresa['id'];
							$this->_dataIn['inputIdEmpresa']   = $idEmpresa;
							$this->_dataIn['inputStatus']      = 1;
							$this->_dataIn['inputDescripcion'] = 'Sucursal '.$this->_dataIn['inputDescripcion'];							
							
							$insertSucursal = $cSucursales->insertRowRegister($this->_dataIn);							
							if($insertSucursal['status']){
								$idSucursal 		= $insertSucursal['id'];
								$this->_dataIn['codeActivation'] = $cFunctions->getRandomCodeReset(); 
								$this->_dataIn['inputPerfil']    = 19;
								$insertiUser = $cUsuarios->insertRowRegister($this->_dataIn);
								if($insertiUser['status']){
									$idUsuario = $insertiUser['id'];
									$this->_dataIn['inputIdUsuario'] = $idUsuario;
									$this->_dataIn['inputSucursal']  = $idSucursal;
									$insertRel = $cUsuarios->setSucursalEmp($this->_dataIn);
									
									$bodymail   = '<h3>Estimado '.$this->_dataIn['inputName'].' '.$this->_dataIn['inputApps'].':</h3><br/>'.
												  'Se ha registrado como usuario del sistema Siames de Grupo UDA <br/>'.
												  'Datos de Acceso <br/>'.	
												  '<table><tr><td>Usuario (Email): </td><td>'.$this->_dataIn['inputUser'].'</td></tr>'.
												  '<tr><td>Contrase&ntilde;a: </td><td>'.$this->_dataIn['inputPassword'].'</td></tr>'.
												  '<tr><td>Acceso al Sistema: </td><td>http://siames.grupouda.com.mx</td></tr>'.
												  '</table>';
									$cMailing = new My_Model_Mailing();
									$aMailer    = Array(
										'inputIdSolicitud'	 => -1,
										'inputDestinatarios' => $this->_dataIn['inputName'].' '.$this->_dataIn['inputApps'],
										'inputEmails' 		 => $this->_dataIn['inputUser'],
										'inputTittle' 		 => 'Siames - Grupo UDA',
										'inputBody' 		 => $bodymail,
										'inputLiveNotif'	 => 0,
										'inputFromName' 	 => 'contacto@grupouda.com.mx',
										'inputFromEmail' 	 => 'Siames - Grupo UDA'						
									);	
			
									$cMailing->insertRow($aMailer);											
								 	$this->_resultOp = 'okRegister';	
								 	$this->_redirect('/admin/brokers/index');
								}else{
									$this->_aErrors['status'] = 1;	
								}						
							}else{
								$this->_aErrors['status'] = 1;	
							}
						}else{
							$this->_aErrors['status'] = 1;	
						}						
					}else{
						$this->_aErrors['eUsuario'] = 1;	
					}
				}else{
					$this->_aErrors['eEmpresa'] = 1;
				}
			}elseif($this->_dataOp=="update"){
				if($this->_idUpdate>-1){
					 $updated = $cEmpresas->updateRow($this->_dataIn,$this->_idUpdate); //mandar el ide del transportista
					 if($updated['status']){
					 	$aDataInfo    = $cEmpresas->getData($this->_idUpdate);
					 	$this->_resultOp = 'okRegister';	
					 	$this->_redirect('/admin/brokers/index');
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}					
			}			
			    		
	    	$this->view->errors 	 = $this->_aErrors;
	    	$this->view->resultOp    = $this->_resultOp;	    	
	    	$this->view->bUsuarioUda = $cFunctions->cboOptions($sClientUda);
	    	$this->view->aCviajes    = $cFunctions->cboOptions($sCViajes);	
	    	$this->view->status      = $cFunctions->cboStatus($sEstatus);
	    	$this->view->aTipos		 = $cFunctions->selectDb($aTiposEmpresa,$sTipoEmpresa);
	    	$this->view->aAgents	 = $cFunctions->cbo_number_on(1,20,$iTotalAgents);	
	    	$this->view->data	     = $aDataInfo;   
			$this->view->catId		 = $this->_idUpdate;
			$this->view->idToUpdate  = $this->_idUpdate;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }

}
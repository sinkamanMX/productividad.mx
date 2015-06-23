<?php 

class leasing_RequestController extends My_Controller_Action
{
	protected $_clase = 'mreqdate';
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
			
			$this->dataIn 			= $this->_request->getParams();
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);	

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
			
			$idCliente		= $this->view->dataUser['ID_EMPRESA'];
			$this->view->dataTable    = $cSolicitudes->getDataTablebyEmp($idCliente,'1,4');
			$this->view->dataTableRev = $cSolicitudes->getDataTablebyEmp($idCliente,5);
			$this->view->dataTableOk  = $cSolicitudes->getDataTablebyEmp($idCliente,2);
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
			$cFunctions 	= new My_Controller_Functions();
			$cHorariosCita  = new My_Model_HorariosCita();
			$cUnidades 		= new My_Model_Unidades();
			$cLog			= new My_Model_LogSolicitudes();
			$cSucursales	= new My_Model_Sucursales();
			$cTipoEquipo	= new My_Model_Tequipos();
			$cHtmlMail		= new My_Controller_Htmlmailing();			
			
			$aTipoServicio	= $cCitas->getCboTipoServicio();
			$aHorarios		= $cHorariosCita->getHorarios();
			$aUnidades		= $cUnidades->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$aSucursales	= $cSucursales->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$aTipoEquipo	= $cTipoEquipo->getCbo();			
			
			$sTipo			= '';
			$sHorario		= '';
			$sUnidad		= '';
			$sSucursal		= '';
			$sTequipo		= '';
			$aLogs			= Array();
			
			$this->dataIn['inputIdEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
			$this->dataIn['inputIdUsuario'] = $this->view->dataUser['ID_USUARIO'];
			
    		if($this->idToUpdate >-1){
				$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
				$aLogs		= $cLog->getDataTable($this->idToUpdate);
				$sTipo		= $dataInfo['ID_TIPO'];
				$sHorario	= $dataInfo['ID_HORARIO'];
				$sUnidad	= $dataInfo['ID_UNIDAD'];
				$sSucursal  = $dataInfo['ID_SUCURSAL'];
				$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];
			}
			
			$sSubject = '';
			$sBody    = '';
			$sModificaciones = '';
			
    		if($this->operation=='new'){	
				$aDataUnit = $cUnidades->getData($this->dataIn['inputUnidad']);
				$this->dataIn['inputInfo'] = "<b>Ult.reporte:</b> S/N<br/><b>Placas:</b>".$aDataUnit['PLACAS']."<br/><b>Eco:</b> ".$aDataUnit['ECONOMICO']."<br/><b>Ip:</b>".$aDataUnit['IDENTIFICADOR']."<br/><b>Tipo Equipo:</b>".$aDataUnit['TIPO_EQUIPO']."<br/><b>Tipo Unidad:</b>".$aDataUnit['TIPO_VEHICULO']."<br/>";		
				$insert = $classObject->insertRowEmp($this->dataIn);			
		 		if($insert['status']){
		 			$this->idToUpdate = $insert['id'];	
					$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
					$sTipo		= $dataInfo['ID_TIPO'];
					$sHorario	= $dataInfo['ID_HORARIO'];	
					$sUnidad	= $dataInfo['ID_UNIDAD'];
					$sSucursal  = $dataInfo['ID_SUCURSAL'];
					$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];
					
					if(isset($this->dataIn['chkSaveDir']) && $this->dataIn['chkSaveDir']=='on'){
						$cSucursales = new My_Model_Sucursales();
						$this->dataIn['inputEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
						$this->dataIn['inputEstatus'] = 1;
						$insert = $cSucursales->insertRow($this->dataIn);
						$aSucursales	= $cSucursales->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);						
					}
					
					$cHtmlMail->newSolicitud($dataInfo,$this->view->dataUser);

			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
				}				
			}else if($this->operation=='update'){
				if($this->idToUpdate>-1){
					if($this->dataIn['bOperation']=='accept'){
						$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';
						
						$updated = $classObject->updateRowEmp($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];
							$sSucursal  = $dataInfo['ID_SUCURSAL'];
							$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];
							
							$cHtmlMail->acceptuserSolicitud($dataInfo,$this->view->dataUser);
											
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por el usuario',
											'sOrigen'		=> 'USUARIO');
							$cLog->insertRow($aLog);							
						}
						
						$this->resultop = 'okRegister';
						
					}elseif($this->dataIn['bOperation']=='modify'){
						$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';
						
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
						}
						
						if($this->dataIn['inputPlace']!=$dataInfo['ID_SUCURSAL']){
							$sModificaciones .= 'Se modifico el lugar de instalaci—n <br/>';							
						}
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}						
						
						if($this->dataIn['inputComment']!=$dataInfo['COMENTARIO']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputComment'].'<br/>';						
						}
						
						$updated = $classObject->updateRowEmp($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];
							$sSucursal  = $dataInfo['ID_SUCURSAL'];
							$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];							
							
							$aLog = Array ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones ,
											'sOrigen'		=> 'USUARIO');
							$cLog->insertRow($aLog);

							$cHtmlMail->changeSolicitud($dataInfo,$this->view->dataUser);
						}
						$this->resultop = 'okRegister';
					}else{	
						if(isset($this->dataIn['inputUnidad'])){
							$aDataUnit = $cUnidades->getData($this->dataIn['inputUnidad']);
							$this->dataIn['inputInfo'] = "<b>Ult.reporte:</b> S/N<br/><b>Placas:</b>".$aDataUnit['PLACAS']."<br/><b>Eco:</b> ".$aDataUnit['ECONOMICO']."<br/><b>Ip:</b>".$aDataUnit['IDENTIFICADOR']."<br/><b>Tipo Equipo:</b>".$aDataUnit['TIPO_EQUIPO']."<br/><b>Tipo Unidad:</b>".$aDataUnit['TIPO_VEHICULO']."<br/>";								
						}
						
						$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';						
						
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
						}
						
						if($this->dataIn['inputPlace']!=$dataInfo['ID_SUCURSAL']){
							$sModificaciones .= 'Se modifico el lugar de instalaci—n <br/>';							
						}						
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}						
						
						if($this->dataIn['inputComment']!=$dataInfo['COMENTARIO']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputComment'].'<br/>';							
						}
						
						if($this->dataIn['inputUnidad']!=$dataInfo['ID_UNIDAD']){
							$sModificaciones .= 'Se modifico la unidad<br/>';							
						}	

						$updated = $classObject->updateRowEmp($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];
							$sSucursal  = $dataInfo['ID_SUCURSAL'];
							$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];					

							if($sModificaciones!=''){
								$cHtmlMail->changeSolicitud($dataInfo,$this->view->dataUser);
								$aLog = Array ('idSolicitud' 	=> $this->idToUpdate,
												'sAction' 		=> 'Cambio en la Solicitud',
												'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones ,
												'sOrigen'		=> 'USUARIO');
								$cLog->insertRow($aLog);								
							}
						}
						
						$this->resultop = 'okRegister';		
					}
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}
			
    	    if($this->resultop=='okRegister'){
    	    	if($this->view->dataUser['ID_TIPO_EMPRESA']==3 && $dataInfo['ID_TIPO']==1){
    	    		$this->_redirect('/leasing/request/newprotocol?strSol='.$this->idToUpdate);	
    	    	}else{
    	    		$this->_redirect('/leasing/request/index');	
    	    	}
			}
			
    	    if(count($this->errors)>0 && $this->operation!=""){
    			$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
    			$sTipo						= $this->dataIn['inputTipo'];
    			$dataInfo['UNIDAD']			= $this->dataIn['inputUnidad'];
    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];
    			$dataInfo['CALLE']			= $this->dataIn['inputCalle'];
    			$dataInfo['COLONIA']		= $this->dataIn['inputColonia'];
    			$dataInfo['MUNICIPIO']		= $this->dataIn['inputMunicipio'];
    			$dataInfo['ESTADO']			= $this->dataIn['inputEstado'];
    			$dataInfo['CP']				= $this->dataIn['inputCP'];
    			$sSucursal  				= $this->dataIn['inputPlace'];
			}
			
			$this->view->aTequipos	= $cFunctions->selectDb($aTipoEquipo,$sTequipo);
			$this->view->aUnidades	= $cFunctions->selectDb($aUnidades,$sUnidad);
			$this->view->aHorarioCita = $cFunctions->selectDb($aHorarios,$sHorario);					
			$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);
			$this->view->aSucursales= $cFunctions->selectDb($aSucursales,$sSucursal);
			$this->view->data 		= $dataInfo; 
			$this->view->logTable   = $aLogs;
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;			    	
    	}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function newprotocolAction(){
    	try{
    		$this->view->dataUser['allwindow'] = true;   
    		$validateNumbers = new Zend_Validate_Digits();    		
    		$cSolicitudes    = new My_Model_Solicitudes();
    		$cProtocolos	 = new My_Model_Protocolos();
    		$cFunctions		 = new My_Controller_Functions();
    		$aTiposContrato  = $cProtocolos->getTipo();
    		$aContactos 	 = Array();
    		$aProtInfo       = Array();
    		$aDataInfo       = Array();
    		$myOpt			 = 0;
    		
    		$sTipoContrato   = '';
    		
    		if($validateNumbers->isValid($this->dataIn['strSol']) ){
    			$idSolicitud = $this->dataIn['strSol'];
				$aDataInfo   = $cSolicitudes->getDataEmp($idSolicitud);
    			if($aDataInfo['ID_TIPO']==1){
    				
    				$aProtInfo  = $cProtocolos->getData($idSolicitud);    				
    				if(isset($aProtInfo['ID_PROTOCOLO']) && $aProtInfo['ID_PROTOCOLO']!=""){
    					$aContactos = $cProtocolos->getPersonas($aProtInfo['ID_PROTOCOLO']);
    					$myOpt=1;	
    				}
    				    				
    				if($this->operation=='new'){
    					$this->dataIn['idSolicitud'] = $idSolicitud;
    					$this->dataIn['idAgencia']   = $this->view->dataUser['ID_EMPRESA'];
    					$insertSolic = $cProtocolos->insertRow($this->dataIn);
    					if($insertSolic['status']){
    						$this->idToUpdate = $insertSolic['id'];
    						$idProtocolo = $insertSolic['id'];
	    					$aProtInfo  = $cProtocolos->getData($idSolicitud);
		    				$aContactos = $cProtocolos->getPersonas($aProtInfo['ID_PROTOCOLO']);	
		    				
    						$iControlE = 0;
							$aValuesForm = $this->dataIn['aElements'];
							if(count($aValuesForm)>0){
								for($i=0;$i<count($aValuesForm);$i++){	
									$aResult = false;											
									$aElement = $aValuesForm[$i];
									if($aElement['op']=='new' && $aElement['id']==-1){
										$aResult = $cProtocolos->insertElement($aElement,$idProtocolo);
									}else if($aElement['op']=='up' && $aElement['id']>-1){
										$aResult = $cProtocolos->updateRowRel($aElement);
									}else if($aElement['op']=='del' && $aElement['id']>-1){
										$aResult = $cProtocolos->deleteRowRel($aElement,$idProtocolo);
									}
									
									if($aResult){
										$iControlE++;
									}
								}
								
								if($iControlE==count($aValuesForm)){
									$this->resultop = 'okRegister';
									$myOpt=1;
									$aContactos = $cProtocolos->getPersonas($aProtInfo['ID_PROTOCOLO']);	
								}
							}
    					}else{
    						$this->errors['errorInsert'] = 1;		
    					}
    				}else if($this->operation=='update'){
    					$this->dataIn['idSolicitud'] = $idSolicitud;
    					$this->dataIn['idAgencia']   = $this->view->dataUser['ID_EMPRESA'];
    					$this->dataIn['idProtocolo'] = $aProtInfo['ID_PROTOCOLO'];
    					$idProtocolo = $aProtInfo['ID_PROTOCOLO'];
    					
    					$update = $cProtocolos->updateRow($this->dataIn);    					
    					if($update['status']){
    						$aProtInfo  = $cProtocolos->getData($idSolicitud);		    				

    						$iControlE = 0;
							$aValuesForm = $this->dataIn['aElements'];
							if(count($aValuesForm)>0){
								for($i=0;$i<count($aValuesForm);$i++){	
									$aResult = false;											
									$aElement = $aValuesForm[$i];
									if($aElement['op']=='new' && $aElement['id']==-1){
										$aResult = $cProtocolos->insertElement($aElement,$idProtocolo);
									}else if($aElement['op']=='up' && $aElement['id']>-1){
										$aResult = $cProtocolos->updateRowRel($aElement);
									}else if($aElement['op']=='del' && $aElement['id']>-1){
										$aResult = $cProtocolos->deleteRowRel($aElement,$idProtocolo);
									}
									
									if($aResult){
										$iControlE++;
									}
								}
								
								if($iControlE==count($aValuesForm)){
									
									$this->resultop = 'okRegister';
									$aContactos = $cProtocolos->getPersonas($aProtInfo['ID_PROTOCOLO']);	
								}
							}    						
    						
    					}else{
    						$this->errors['errorInsert'] = 1;		
    					}
    				}
    			}else{
    				$this->_redirect('/leasing/request/index');
    			}
			}else{
				$this->_redirect('/leasing/request/index');		
			}
			
			$this->view->sTipos    = $cFunctions->selectDb($aTiposContrato,$sTipoContrato);
			$this->view->data 	   = $aDataInfo;
			$this->view->dataProt  = $aProtInfo;
			$this->view->aPosition = $cFunctions->cboOptions('');
			$this->view->aPersonas = $this->processFields($aContactos);
			$this->view->strSol    = $this->dataIn['strSol'];
			$this->view->myOpte	   = $myOpt;
			$this->view->resultOp  = $this->resultop;
		 }catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }
    
	public function getinfodirAction(){
		try{
			$answer = Array('answer' => 'no-data');
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$sResult = '';
			$validateNumbers = new Zend_Validate_Digits();
			$cSucursales = new My_Model_Sucursales();
			$aDataInfo   = Array();
			
			if($validateNumbers->isValid($this->dataIn['catId']) ){
				$aDataInfo = $cSucursales->getData($this->dataIn['catId']);
				$sResult = 'ok';
			}else{
				$sResult = 'noinfo';	
			}
			
			$answer = Array('answer' 	=> $sResult,
							'aData'		=> $aDataInfo);    
	        echo Zend_Json::encode($answer); 			
			
		 }catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
	}
	
	public function processFields($aElements){
		$aResult    = Array();
		$cFunctions 	= new My_Controller_Functions();
		foreach($aElements as $key => $items){
			$items['cboPalta'] = $cFunctions->cboOptions($items['EVENTOS_PRIORIDAD']);
			$items['cboPosic'] = $cFunctions->cboOptions($items['SOLICITAR_POSICION']);
			$aResult[] = $items;
		}
		
		return $aResult;
	}	
}
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
			$cEmpresas= new My_Model_Empresas();
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
			$this->sMailsBrokers = $cEmpresas->getDataByCode($this->view->dataUser['ID_BROKER']);
			$this->dataIn['inputSucursal'] = $this->view->dataUser['ID_BROKER'];
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }   	
    }

    public function indexAction()
    {
		try{  
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cSolicitudes   = new My_Model_Soleasing();
			
			$aSucursales 	= "";
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch    = $cSolicitudes->getCboStatus();		
			$aEstatus 		= -1;			
			$bType 			= -1;
			$bStatus		= -1;	

			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				$bType		= $this->dataIn['cboTypeSearch'];				
				$bShowUsers=true;
			}else{
				$fecha = date('Y-m-d');
				$nuevafecha = strtotime ( '+15 day' , strtotime ( $fecha ) ) ;
				$nuevafecha = date ( 'Y-m-d' , $nuevafecha );				
				
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= $nuevafecha;					
				$bShowUsers=true;
				$idSucursal		= "";	
				$this->dataIn['inputFechaIn']  = $dFechaIn;
				$this->dataIn['inputFechaFin'] = $dFechaFin;					
			}			
			$idCliente		= $this->view->dataUser['ID_EMPRESA'];
			$dataResume     = $cSolicitudes->getResumeByDay($dFechaIn,$dFechaFin,$idCliente,$bType);	
			$this->view->aTypeSearchs	= $cFunciones->selectDb($aTypeSearch,$bType);
			$this->view->dataTable	 	= $dataResume;
			$this->view->data 			= $this->dataIn;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }

    public function getinfoAction(){    
    	try{    		 
			$dataInfo = Array();
			$classObject 	= new My_Model_Soleasing();
			$cCitas			= new My_Model_Citas();			
			$cFunctions 	= new My_Controller_Functions();
			$cHorariosCita  = new My_Model_HorariosCita();
			$cUnidades 		= new My_Model_Unidades();
			$cLog			= new My_Model_LogSolicitudes();
			$cSucursales	= new My_Model_Lugares();
			$cTipoEquipo	= new My_Model_Tequipos();
			$cHtmlMail		= new My_Controller_Maileasing();			
			$cClientes	 	= new My_Model_Clientesint();
			$aTipoServicio	= $cCitas->getCboTipoServicio();
			$aHorarios		= $cHorariosCita->getHorarios();
			$aSucursales	= Array();
			$aUnidades 		= Array();		
			//$aUnidades		= $cUnidades->getCbobyEmpLe($this->view->dataUser['ID_EMPRESA']);
			//$aSucursales	= $cSucursales->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$aTipoEquipo	= $cTipoEquipo->getCbo();			
			$aClientes		= $cClientes->getCbo($this->view->dataUser['ID_EMPRESA']);
			$cColores		= new My_Model_Colores();
			$cMarcas 		= new My_Model_Activosmarcas();
			$cModelos 		= new My_Model_Activosmodelos();
					
			$aColores		= $cColores->getCbo();
			$aMarcas		= $cMarcas->getCbo();			
			$sCliente		= '';				
			$sTipo			= '';
			$sHorario		= '';
			$sUnidad		= '';
			$sSucursal		= '';
			$sTequipo		= '';
			$aLogs			= Array();
			$sModelo	= '';
			$sColor		= '';
			$sMarca		= '';
			$sAnio		= '';			
			$dataInfoUnit	= Array();
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
				$sCliente	= $dataInfo['ID_EMP_CLIENTE'];
				$aUnidades	= $cUnidades->getCboByEmpCliente($sCliente);
				$aSucursales= $cSucursales->getCbobyClient($sCliente);
				
				$dataInfoUnit= $cUnidades->getDataLeasing($dataInfo['ID_UNIDAD']);
				$sColor 	 = $dataInfoUnit['ID_COLOR'];
				$sAnio	 	 = $dataInfoUnit['ANIO'];
				$sModelo	 = $dataInfoUnit['ID_MODELO'];
				$sMarca		 = $dataInfoUnit['ID_MARCA'];
				$sCliente    = $dataInfoUnit['ID_EMP_CLIENTE'];
				$aModelos	= $cModelos->getCbo($sMarca);
				$this->view->aModelos    = $cFunctions->selectDb($aModelos,$sModelo);
			}
			
			$sSubject = '';
			$sBody    = '';
			$sModificaciones = '';

    		if($this->operation=='new'){
    			//se agrega la validacion de tecnicos asignados a la hora.
    			$bDateEnable     = $classObject->validateDate($this->dataIn['inputIdEmpresa'],$this->dataIn['inputHorario'],-1,$this->dataIn['inputFechaIn']);
    			if($bDateEnable < $this->view->dataUser['NO_TECNICOS']){
    				$this->dataIn['inputCliente'] = $this->dataIn['inpuClienteEmp'];
    				
    				if($this->dataIn['inputUnidad']>-1){
						$aDataUnit = $cUnidades->getData($this->dataIn['inputUnidad']);						
						$this->dataIn['inputInfo'] = "<b>Marca/Modelo:</b>".$aDataUnit['N_MARCA']."/".$aDataUnit['N_MODELO']."</br>".
													 "<b>Color       :</b>".$aDataUnit['N_COLOR']."</br>".
													 "<b>Placas      :</b>".$aDataUnit['PLACAS']."</br>".
													 "<b>No. Serie   :</b>".$aDataUnit['IDENTIFICADOR']."</br>".
													 "<b>No. Contrato:</b>".$aDataUnit['IDENTIFICADOR_2'];
    				}else{
    					$iValidateUnit = $classObject->validateUnit($this->dataIn['inputPlacas'],$this->dataIn['inputIden'],$this->dataIn['inputIden2']);
    					if($iValidateUnit==0){
    						$bInsertUnit = $classObject->insertNewRowLeasing($this->dataIn);    				
	    					if($bInsertUnit['status']){	    						
								$this->dataIn['inputUnidad']= $bInsertUnit['id'];
								
								$aDataUnit = $cUnidades->getData($bInsertUnit['id']);
								$this->dataIn['inputInfo'] = "<b>Marca/Modelo:</b>".$aDataUnit['N_MARCA']."/".$aDataUnit['N_MODELO']."</br>".
															 "<b>Color       :</b>".$aDataUnit['N_COLOR']."</br>".
															 "<b>Placas      :</b>".$aDataUnit['PLACAS']."</br>".
															 "<b>No. Serie   :</b>".$aDataUnit['IDENTIFICADOR']."</br>".
															 "<b>No. Contrato:</b>".$aDataUnit['IDENTIFICADOR_2'];    						
	    					}else{
								$this->errors['status'] = 'no-insert';	    					    						
	    					}    							
    					}else{
    						$this->dataIn['inputUnidad']= $iValidateUnit;
							$aDataUnit = $cUnidades->getData($iValidateUnit);						
							$this->dataIn['inputInfo'] = "<b>Marca/Modelo:</b>".$aDataUnit['N_MARCA']."/".$aDataUnit['N_MODELO']."</br>".
														 "<b>Color       :</b>".$aDataUnit['N_COLOR']."</br>".
														 "<b>Placas      :</b>".$aDataUnit['PLACAS']."</br>".
														 "<b>No. Serie   :</b>".$aDataUnit['IDENTIFICADOR']."</br>".
														 "<b>No. Contrato:</b>".$aDataUnit['IDENTIFICADOR_2'];    						
    					}  
    				}
    				    		
    				if($this->dataIn['inputInfo']!=""){
						$insert = $classObject->insertRowEmp($this->dataIn);
    					if($insert['status']){
    						$this->idToUpdate = $insert['id'];
    						$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							
							if(isset($this->dataIn['chkSaveDir']) && $this->dataIn['chkSaveDir']=='on'){
								$cSucursales = new My_Model_Lugares();
								$this->dataIn['inputEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
								$this->dataIn['inputEstatus'] = 1;
								$insert = $cSucursales->insertRowLeasing($this->dataIn);
								if($insert['status']){
									$udate  = $classObject->updateSucursal($this->idToUpdate,$insert['id']);	
								}								 						
							}
							
							$iMailsSender = ($this->view->dataUser['ID_TIPO_EMPRESA']==3) ? '2': '1';

							$cHtmlMail->newSolicitud($dataInfo,$this->view->dataUser,$this->sMailsBrokers,$iMailsSender);
							$this->resultop ='okRegister';
							//$this->redirect("/leasing/request/index");
							//$this->redirect("/leasing/request/getinfo?catId=".$this->idToUpdate);
						}else{
							$this->errors['status'] = 'no-insert';
						}
    				}else{
    					$this->errors['status'] = 'no-insert';
    				}
    			}else{
    				$this->errors['errorDate'] = 'errordate';	
    			}
			}else if($this->operation=='update'){
				if($this->idToUpdate>-1){
					$bValidate = true;
					if($dataInfo['ID_HORARIO']!=$this->dataIn['inputHorario']){
						//se agrega la validacion de tecnicos asignados a la hora.
	    				$bDateEnable     = $classObject->validateDate($this->dataIn['inputIdEmpresa'],$this->dataIn['inputHorario'],$this->idToUpdate); 
	    				if($bDateEnable < $this->view->dataUser['NO_TECNICOS']){
							$bValidate =true;	    					
	    				}else{
	    					$bValidate =false;
	    				}						
					}
					
					if($bValidate){
						$bModifyCar = 0;				
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
						}
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}				
						
						if($this->dataIn['inputComment']!=$dataInfo['COMENTARIO']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputComment'].'<br/>';							
						}		

						if($this->dataIn['inputTipo']!=$dataInfo['ID_TIPO']){
							$sModificaciones .= 'Se modifico el tipo de cita. <br/>';							
						}							
		                
						if($this->dataIn['inputTequipo']!=$dataInfo['ID_TIPO_EQUIPO']){
							$sModificaciones .= 'Se modifico el tipo de equipo. <br/>';							
						}

						if($this->dataIn['inputCalle']!=$dataInfo['CALLE']){
							$sModificaciones .= 'Se modifico la direccion de la cita <br/>';							
						}
												
						if($this->dataIn['inputEntreCalles']!=$dataInfo['ENTRE_CALLES']){
							$sModificaciones .= 'Se modificaron las entre calles  (direccion).<br/>';							
						}
												
						if($this->dataIn['inputRefs']!=$dataInfo['REFERENCIAS']){
							$sModificaciones .= 'Se modificaron las referencias (direccion). <br/>';							
						}

						if($this->dataIn['inputColonia']!=$dataInfo['COLONIA']){
							$sModificaciones .= 'Se modifico la colonia (direccion). <br/>';					
						}

						if($this->dataIn['inputMunicipio']!=$dataInfo['MUNICIPIO']){
							$sModificaciones .= 'Se modifico el municipio (direccion). <br/>';					
						}						

						if($this->dataIn['inputEstado']!=$dataInfo['ESTADO']){
							$sModificaciones .= 'Se modifico el estado (direccion). <br/>';
						}
						
						if($this->dataIn['inputCP']!=$dataInfo['CP']){
							$sModificaciones .= 'Se modifico el codigo postal (direccion). <br/>';
						}

						if($this->dataIn['inputPlacas']!=$dataInfoUnit['PLACAS']){
							$sModificaciones .= 'Se modifico el No. de Placa (vehiculo). <br/>';
							$bModifyCar++;
						}
						
						if($this->dataIn['inputIden']!=$dataInfoUnit['IDENTIFICADOR']){
							$sModificaciones .= 'Se modifico el No. de Serie (vehiculo). <br/>';
							$bModifyCar++;
						}

						if($this->dataIn['inputColor']!=$dataInfoUnit['ID_COLOR']){
							$sModificaciones .= 'Se modifico el color (vehiculo). <br/>';
							$bModifyCar++;
						}

						if($this->dataIn['inputAnio']!=$dataInfoUnit['ANIO']){
							$sModificaciones .= 'Se modifico el ano (vehiculo). <br/>';
							$bModifyCar++;
						}

						if($this->dataIn['inputMarca']!=$dataInfoUnit['ID_MARCA']){
							$sModificaciones .= 'Se modifico la marca (vehiculo).<br/>';
							$bModifyCar++;
						}				

						if($this->dataIn['inputModelo']!=$dataInfoUnit['ID_MODELO']){
							$sModificaciones .= 'Se modifico el modelo (vehiculo). <br/>';
							$bModifyCar++;
						}			

						if($this->dataIn['inputIden2']!=$dataInfoUnit['IDENTIFICADOR_2']){
							$sModificaciones .= 'Se modifico el No. de Contrato (vehiculo). <br/>';
							$bModifyCar++;
						}
						
						$bContinue = false;
						if($bModifyCar>0){
							$bUpCar = $classObject->upUnitUser($this->dataIn);
							if($bUpCar['status']){
								$bContinue = true;
							}
						}else{
							$bContinue = true;
						}
						
						if($bContinue){
							$aDataUnit = $cUnidades->getData($this->dataIn['txtIdCarSelected']);
							$this->dataIn['inputInfo'] = "<b>Marca/Modelo:</b>".$aDataUnit['N_MARCA']."/".$aDataUnit['N_MODELO']."</br>".
														 "<b>Color       :</b>".$aDataUnit['N_COLOR']."</br>".
														 "<b>Placas      :</b>".$aDataUnit['PLACAS']."</br>".
														 "<b>No. Serie   :</b>".$aDataUnit['IDENTIFICADOR']."</br>".
														 "<b>No. Contrato:</b>".$aDataUnit['IDENTIFICADOR_2'];    							
							$updated = $classObject->upSolUser($this->dataIn);
							if($updated['status']){
								$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							
								if($sModificaciones!=''){
									$iMailsSender = ($this->view->dataUser['ID_TIPO_EMPRESA']==3) ? '2': '1';
									$cHtmlMail->changeSolicitud($dataInfo,$this->view->dataUser,$this->sMailsBrokers,$iMailsSender);
									$aLog = Array ('idSolicitud' 	=> $this->idToUpdate,
													'sAction' 		=> 'Cambio en la Solicitud',
													'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones ,
													'sOrigen'		=> 'USUARIO');
									$cLog->insertRow($aLog);								
								}
							}
							$this->resultop ='okRegister';
							//$this->redirect("/leasing/request/index");									
						}else{
							$this->errors['errorUpdate'] = 'error';
						}						
					}else{
						$this->errors['errorDate'] = 'errordate';
					}						
				}else{
					$this->errors['status'] = 'no-info234';
				}					
			}
			
    	    if($this->resultop=='okRegister' && count($this->errors)==1){
    	    	if($this->view->dataUser['ID_TIPO_EMPRESA']==3 && $dataInfo['ID_TIPO']==1){
    	    		$this->_redirect('/leasing/request/newprotocol?strSol='.$this->idToUpdate);	
    	    	}else{
    	    		$this->_redirect('/leasing/request/index');	
    	    	}
			}		
    		
    	    if(count($this->errors)>0 && $this->operation!=""){
				$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
    			$sTipo						= $this->dataIn['inputTipo'];
    			$dataInfo['UNIDAD']			= @$this->dataIn['inputUnidad'];
    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];
    			$dataInfo['CALLE']			= $this->dataIn['inputCalle'];
    			$dataInfo['COLONIA']		= $this->dataIn['inputColonia'];
    			$dataInfo['MUNICIPIO']		= $this->dataIn['inputMunicipio'];
    			$dataInfo['ESTADO']			= $this->dataIn['inputEstado'];
    			$dataInfo['CP']				= $this->dataIn['inputCP'];
    			$sSucursal  				= $this->dataIn['inputPlace'];
    			$sCliente					= $this->dataIn['inpuClienteEmp'];
				$sHorario					= $this->dataIn['inputHorario'];	
				$sUnidad					= @$this->dataIn['inputUnidad'];
				$sTequipo					= @$this->dataIn['inputTequipo'];    
			}
			
			$yearEnd= Date("Y")+2;
			$yearIn	= Date("Y")-15;
			
			$this->view->aMarcas	= $cFunctions->selectDb($aMarcas,$sMarca);		
			$this->view->sAnios	    = $cFunctions->cbo_rangeNumber($yearIn,$yearEnd,$sAnio);
			$this->view->sColores	= $cFunctions->selectDb($aColores,$sColor);			
			$this->view->aInfoUnit  = $dataInfoUnit;				
			$this->view->aClientes  = $cFunctions->selectDb($aClientes,$sCliente);
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
			$cSucursales = new My_Model_Lugares();
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
	
	public function findunitsAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			$cUnidades 		= new My_Model_Unidades();
			$aUnidades		= $cUnidades->getCbobyEmpLe($this->view->dataUser['ID_EMPRESA']);			
			
			$this->view->dataTable = $aUnidades;
		 }catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
	} 

	public function getunitsAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
			    	
	    	$result = 'no-info';
			$this->dataIn = $this->_request->getParams();
			$functions = new My_Controller_Functions();				
			$validateNumbers = new Zend_Validate_Digits();
			$validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
											
			if($validateNumbers->isValid($this->dataIn['catId']) && $validateAlpha->isValid($this->dataIn['oprDb'])){
				$cClassUnits = new My_Model_Unidades();
				$cboValues   = $cClassUnits->getCboByEmpCliente($this->dataIn['catId']);
				$result      = $functions->selectDb($cboValues);
			}
			
			echo $result;		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
	}
	
	public function getplacesAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
			    	
	    	$result = 'no-info';
			$this->dataIn = $this->_request->getParams();
			$functions = new My_Controller_Functions();				
			$validateNumbers = new Zend_Validate_Digits();
			$validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
											
			if($validateNumbers->isValid($this->dataIn['catId']) && $validateAlpha->isValid($this->dataIn['oprDb'])){
				$cClassObject = new My_Model_Lugares();				
				$cboValues   = $cClassObject->getCbobyClient($this->dataIn['catId']);
				$result      = $functions->selectDb($cboValues);
			}
			
			echo $result;		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	}
	
    public function cancelAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');    	
			$cSolicitudes = new My_Model_Soleasing();
			$cLog   	  = new My_Model_LogSolicitudes(); 
							
	    	if($this->operation=='cancel'){
	    		if(isset($this->dataIn['catId'])    && $this->dataIn['catId']  !="" && 
	    		   isset($this->dataIn['sComent'])  && $this->dataIn['sComent']!="" ){
				
	    		   	$bCancel = $cSolicitudes->cancelUsuario($this->dataIn);
	    		   	if($bCancel['status']){
						$aLog = Array ('idSolicitud' 	=> $this->dataIn['catId'],
										'sAction' 		=> 'Solicitud Cancelada',
										'sDescripcion' 	=> 'Razon de cancelacion : <br>'.$this->dataIn['sComent'] ,
										'sOrigen'		=> 'USUARIO');
						$cLog->insertRow($aLog);
										    		   		
						$answer = Array('answer' => 'canceled'); 
					}
	    		}
			}
			
			echo Zend_Json::encode($answer);
		    die();			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	    	
    }
    
    public function getinfosolAction(){
    	try{
			$this->view->layout()->setLayout('layout_blank');
		
			$cSolicitudes = new My_Model_Soleasing();
			$cFunctions   = new My_Controller_Functions();			
			$cLog		  = new My_Model_LogSolicitudes();
			$dataInfo	  = Array();
						
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$dataInfo   = $cSolicitudes->getDataEmp($this->dataIn['strInput']);				
				$aLogs		= $cLog->getDataTable($this->dataIn['strInput']);					
			}
			
			$this->view->aDataInfo	= $dataInfo;
			$this->view->logTable   = $aLogs;			
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
    
    public function acceptsolAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
			    
	    	$validateNumbers = new Zend_Validate_Digits();
			$validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
			$classObject 	 = new My_Model_Soleasing();
			$cLog			 = new My_Model_LogSolicitudes();			
			$cHtmlMail		 = new My_Controller_Maileasing();				
										
			if($validateNumbers->isValid($this->dataIn['catId']) && $validateAlpha->isValid($this->dataIn['oprDb'])){
				$this->dataIn['bOperation'] = 'accept';
				$updated = $classObject->updateRowEmp($this->dataIn);
				if($updated['status']){
					$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
					$iMailsSender = ($this->view->dataUser['ID_TIPO_EMPRESA']==3) ? '2': '1';
					$cHtmlMail->acceptuserSolicitud($dataInfo,$this->view->dataUser,$this->sMailsBrokers,$iMailsSender);
									
					$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
									'sAction' 		=> 'Solicitud Aceptada',
									'sDescripcion' 	=> 'La solicitud ha sido aceptada por el usuario',
									'sOrigen'		=> 'USUARIO');
					$cLog->insertRow($aLog);	
					$answer = Array('answer' => 'accept');								
				}
			}

	        echo Zend_Json::encode($answer);
	        die();		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }	        
    }    
}
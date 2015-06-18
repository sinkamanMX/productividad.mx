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
			
			$idCliente		= $this->view->dataUser['ID_CLIENTE'];
			$this->view->dataTable    = $cSolicitudes->getDataTablebyClient($idCliente,'1,4');
			$this->view->dataTableRev = $cSolicitudes->getDataTablebyClient($idCliente,5);
			$this->view->dataTableOk  = $cSolicitudes->getDataTablebyClient($idCliente,2);
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
			$aTipoServicio	= $cCitas->getCboTipoServicio(true);
			$aHorarios		= $cHorariosCita->getHorarios();
			$aUnidades		= $cUnidades->getCbo($this->view->dataUser['ID_CLIENTE']);
			$cLog			= new My_Model_LogSolicitudes();
			$sTipo			= '';
			$sHorario		= '';
			$sHorario2		= '';
			$sUnidad		= '';
			$aLogs			= Array();
			$cHtmlMail		= new My_Controller_Htmlmailing();		
			
			$this->dataIn['inputCliente'] = $this->view->dataUser['ID_CLIENTE'];			
			$this->dataIn['inputUserQr']  = $this->view->dataUser['ID_CONTACTO_QR'];
			
			if($this->idToUpdate >-1){
				$dataInfo   = $classObject->getData($this->idToUpdate);
				$aLogs		= $cLog->getDataTable($this->idToUpdate);
				$sTipo		= $dataInfo['ID_TIPO'];
				$sHorario	= $dataInfo['ID_HORARIO'];
				$sHorario2	= @$dataInfo['ID_HORARIO2'];
				$sUnidad	= $dataInfo['ID_UNIDAD'];
			}
			
			$sSubject = '';
			$sBody    = '';
			$sModificaciones = '';

			if($this->operation=='updateUnits'){
				//192.168.6.41
				//201.131.96.40
				$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
				$aParams 	  = array('sLogin'     => 'wbs_test@grupouda.com.mx',
				                  	  'sPassword'  => 't3stud4',
									  'strCustomerPass' => $this->view->dataUser['COD_CLIENTE']);		
				$result=$soap_client->HistoyDataLastLocationByCustomerPass($aParams);
				if (is_object($result)){
			       	$x = get_object_vars($result);
					$y = get_object_vars($x['HistoyDataLastLocationByCustomerPassResult']);
								
					$xml = $y['any'];		
					if($xml2 = simplexml_load_string($xml)){
						$bContinue = 0;						
						if($xml2->Response->Status->code=='101'){
							$bContinue = 1;								
						}else if($xml2->Response->Status->code=='113'){
							$bContinue = 2;							
						}
							
						$c = 0;		
						if($bContinue==0){								
							for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
				          		$sImei    	= (string) $xml2->Response->Plate[$i]['id'];
				          		$sEconomico = (string) $xml2->Response->Plate[$i]->hst->ECO;
				          		$sIp 		= (string) $xml2->Response->Plate[$i]->hst->IP;
				          		$sPlacas	= (string) $xml2->Response->Plate[$i]->hst->Plate;				          		
				          		$sTipoVehiculo= (string) $xml2->Response->Plate[$i]->hst->TypeMobile;
				          		$sTipoGps 	  = (string) $xml2->Response->Plate[$i]->hst->DeviceName;
				          		$sIdent2 	  = (string) $xml2->Response->Plate[$i]->hst->Imei;
				          		
				          		$validateUnit = $cUnidades->validateUnitByPlaque($sEconomico);
				          		if(!$validateUnit){
				          			$aDataInsertUnit['inputIdCliente'] 	= $this->view->dataUser['ID_CLIENTE'];	
				          			$aDataInsertUnit['inputEco'] 		= $sEconomico;
				          			$aDataInsertUnit['inputPlacas']   	= $sPlacas;
				          			$aDataInsertUnit['inputIden']  		= $sImei;
				          			$aDataInsertUnit['inputIden2']  	= $sIdent2;
				          			$aDataInsertUnit['inputStatus']  	= 1;
				          			$aDataInsertUnit['inputVehiculo']  	= $sTipoVehiculo;
				          			$aDataInsertUnit['inputEquipo']  	= $sTipoGps;
				          			
				          			$insertunit = $cUnidades->insertRow($aDataInsertUnit);
				          			if(!$insertunit){
				          				$errors[$c] = $sImei;
				          			}
				          		}
				
				          		$c = $c+1;
				        	}								
						}
						
			        	if($c >0){
			        		$aUnidades		= $cUnidades->getCbo($this->view->dataUser['ID_CLIENTE']);
			        		$this->resultop = 'okUpdate';			        		
			        	}elseif($c=0 && $bContinue==0){
			        		$this->errors['no-units'] = 1;
			        	}else if($bContinue==2){
			        		$this->errors['login'] = 1;
			        	}else if($bContinue==1){
			        		$this->errors['client-problem'] = 1;	
			        	}
					}else{
						$this->errors['no-info'] = 1;
					}
				}else{
					$this->errors['no-service'] = 1;
				} 				
			}elseif($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					if($this->dataIn['bOperation']=='accept'){
						$updated = $classObject->updateRow($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getData($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];

							$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
							$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';							
							
							$cHtmlMail->acceptuserExternalSolicitud($dataInfo,$this->view->dataUser);
							
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
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}						
						
						if($this->dataIn['inputComment']!=$dataInfo['COMENTARIO']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputComment'].'<br/>';						
						}
						
						$updated = $classObject->updateRow($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getData($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];

							$cHtmlMail->changeSolicitudExt($dataInfo,$this->view->dataUser);

							$aLog = Array ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones ,
											'sOrigen'		=> 'USUARIO');
							$cLog->insertRow($aLog);
						}
						$this->resultop = 'okRegister';					
					}else{
						$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';						
						
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
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

						$updated = $classObject->updateRow($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getData($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];
							
							if($sModificaciones!=''){
								$cHtmlMail->changeSolicitudExt($dataInfo,$this->view->dataUser);
								$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
												'sAction' 		=> 'Cambio en la Solicitud',
												'sDescripcion' 	=> 'Modificaciones :  <br>'.$sModificaciones ,
												'sOrigen'		=> 'USUARIO');
								$cLog->insertRow($aLog);								
							}
						
							$this->resultop = 'okRegister';							
						}
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
					$sHorario	= $dataInfo['ID_HORARIO'];	
					$sUnidad	= $dataInfo['ID_UNIDAD'];

					$sHorario2  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': ''; 
					
					$sSubject 	= 'Nueva Solicitud de Cita';
					$sBody  	= 'El cliente <b>'.$this->view->dataUser['RAZON_SOCIAL'].'</b> ha realizado una solicitud de cita en el sistema de Siames<br/>'.
								  '<table><tr><td><b>Fecha</b></td><td>'.$dataInfo['FECHA_CITA'].'</td></tr>'.	
									'<tr><td><b>Horario</b></td><td>'.$dataInfo['N_HORARIO'].'</td></tr>'.
									$sHorario2.
									'<tr><td><b>Tipo de Cita</b></td><td>'.$dataInfo['N_TIPO'].'</td></tr>'.	
									'<tr><td><b>Unidad</b></td><td>'.$dataInfo['N_UNIDAD'].'</td></tr>'.		
									'<tr><td><b>Informaci&oacute;n de la Unidad</b></td><td>'.$dataInfo['INFORMACION_UNIDAD'].'</td></tr>'.			
									'<tr><td><b>Comentarios</b></td><td>'.$dataInfo['COMENTARIO'].'</td></tr></table><br/>'.								  
								  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
								  '<a href="http://192.168.6.23">Da Click Aqui</a><br/>'.
								  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
								  '<b> http://192.168.6.23</b>';						
			 		$this->resultop = 'okRegister';
				}else{
					$this->errors['status'] = 'no-insert';
				}				
			}	
			
			if($this->resultop=='okRegister'){
				/*$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
				$aDataAdmin = $config->getOption('admin');					
				$cMailing   = new My_Model_Mailing();
				$aMailer    = Array(
					'inputIdSolicitud'	 => $this->idToUpdate,
					'inputDestinatarios' => $aDataAdmin['mails'],
					'inputEmails' 		 => $aDataAdmin['mails'],
					'inputTittle' 		 => $sSubject,
					'inputBody' 		 => $sBody,
					'inputLiveNotif'	 => 1,
					'inputFromName' 	 => 'contacto@grupouda.com.mx',
					'inputFromEmail' 	 => 'Siames - Grupo UDA'						
				);	

				$cMailing->insertRow($aMailer);		
				*/						
				//$cFunctions->sendMailAdmins($sSubject,$sBody);
					
				$this->_redirect('/external/request/index');				
			}

    		if(count($this->errors)>0 && $this->operation!=""){
    			$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
    			$sTipo						= $this->dataIn['inputTipo'];
    			$dataInfo['UNIDAD']			= $this->dataIn['inputUnidad'];
    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];    				
			}
			
			$this->view->aUnidades	= $cFunctions->selectDb($aUnidades,$sUnidad);
			$this->view->aHorarioCita = $cFunctions->selectDb($aHorarios,$sHorario);
			$this->view->aHorarioCita2= $cFunctions->selectDb($aHorarios,$sHorario2);
			$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);
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

    public function getinfodataAction(){
		try{
			$answer = Array('answer' => 'no-data');
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			$validateNumbers = new Zend_Validate_Digits();
			$validateString  = new Zend_Validate_Alnum();
			$cUnidades 		 = new My_Model_Unidades();	
			
			$sResult = '';
			$uReporte= '';
			$sPlacas = '';
			$sEco	 = '';
			$sIp 	 = '';
			$sTipoE	 = '';
			$sTipoGps= '';

			if($validateNumbers->isValid($this->dataIn['catId']) ){
				$idUnidad 	= $this->dataIn['catId'];
				$dataInfo   = $cUnidades->getData($idUnidad);
				
				//192.168.6.41
				//201.131.96.40
				$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
				$aParams 	  = array('sLogin'     => 'wbs_test@grupouda.com.mx',
				                  	  'sPassword'  => 't3stud4',
									  'strCustomerPass' => $dataInfo['COD_CLIENTE']);

				$result=$soap_client->HistoyDataLastLocationByCustomerPass($aParams);
				if (is_object($result)){
			       	$x = get_object_vars($result);
					$y = get_object_vars($x['HistoyDataLastLocationByCustomerPassResult']);
			
					$xml = $y['any'];		
					if($xml2 = simplexml_load_string($xml)){
						$bContinue = 0;						
						if($xml2->Response->Status->code=='101'){
							$bContinue = 1;								
						}else if($xml2->Response->Status->code=='113'){
							$bContinue = 2;							
						}
							
						$c = 0;		
						if($bContinue==0){								
							for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
								$sPlacas	= (string) $xml2->Response->Plate[$i]->hst->Plate;
				          		$sImei    	= (string) $xml2->Response->Plate[$i]['id'];
				          		$sEconomico = (string) $xml2->Response->Plate[$i]->hst->ECO;
				          		$sIp 		= (string) $xml2->Response->Plate[$i]->hst->IP;
				          		$sPlacas	= (string) $xml2->Response->Plate[$i]->hst->Plate;				          		
				          		$sTipoVehiculo= (string) $xml2->Response->Plate[$i]->hst->TypeMobile;
				          		$sTipoGps 	  = (string) $xml2->Response->Plate[$i]->hst->DeviceName;
				          		$sIdent2 	  = (string) $xml2->Response->Plate[$i]->hst->Imei;
				          		$sDateGps	  = (string) $xml2->Response->Plate[$i]->hst->DateTimeGPS;

								if($sPlacas == $dataInfo['PLACAS']){
									$sResult = 'ok';
									$uReporte= $sDateGps;
									$sPlacas = $sPlacas;
									$sEco	 = $sEconomico;
									$sIp 	 = $sIp;
									$sTipoE	 = $sTipoVehiculo;
									$sTipoGps= $sTipoGps;
									break;
								}
				        	}								
						}						
					}else{
						$sResult = 'problem';
					}
				}else{
					$sResult = 'no-service';
				}
			}else{
	            $sResult = 'noinfo';
	        }			
	        
			$answer = Array('answer' 	=> $sResult,
							'uReporte'	=> $uReporte,
							'Placas'	=> $sPlacas,
							'Eco'		=> $sEco,
							'Ip'		=> $sIp,
							'TipoE'		=> $sTipoE,
							'Tunidad'	=> $sTipoGps);    
	        echo Zend_Json::encode($answer);   
    	} catch (Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }	    	
    }
    
    public function getinfounitAction(){
		try{
			//192.168.6.41
			//201.131.96.40
			$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
			$aParams 	  = array('sLogin'     => 'wbs_test@grupouda.com.mx',
			                  	  'sPassword'  => 't3stud4',
								  'strCustomerPass' => 'CL00000851');		
			
			
			
			/*
			

			*/
    	}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
    }
}
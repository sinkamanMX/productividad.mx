<?php

class sac_MainController extends My_Controller_Action
{	
	public $dataIn = NULL;
	public $aErrors= -1;
	public $validateNumbers;
	public $validateAlpha;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='';
	public $resultop=null;	
		
    public function init()
    {
		$this->view->layout()->setLayout('public');

		$this->dataIn 			= $this->_request->getParams();
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
    }

    public function indexAction()
    {
		try{
			$statusHeader = false;
			$cSapClientes = new My_Model_Sapclientes();	
			$cFunctions	  = new My_Controller_Functions();
					
			$codClient = -1;
			$aDataCodes = Array();
			$bStatusSearch = 0;
			
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']=='search' && 
			   isset($this->dataIn['inputCodeClient']) && $this->dataIn['inputCodeClient']!=""){				
			   	$codClient 	  = $this->dataIn['inputCodeClient'];							   	
				$aDataCliente = $cSapClientes->getData($codClient);
				
				if(count($aDataCliente)>0 && isset($aDataCliente['ID_CLIENTE'])){
					$this->_redirect('/sac/main/getinfo?ssKey5earch='.$aDataCliente['ID_CLIENTE'].'&str0tn='.$cFunctions->getRandomCode());	
					$bStatusSearch=1;	
				}else{
					$this->aErrors 	= 1;
				}
			}			
			
	
			$this->view->codeClient    = $codClient;
			$this->view->aErrors 	   = $this->aErrors;
			$this->view->datatTable    = $aDataCodes;
			$this->view->bStatusSearch = $bStatusSearch;
			$this->view->showHeader    = $statusHeader;			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }

    public function getinfoAction(){
    	try{  
    		$dataInfo     = Array();  		
			$cSapClientes = new My_Model_Sapclientes();
			$codClient    = '';	
			$aContactos	  = Array();
    		if(isset($this->dataIn['ssKey5earch']) &&  $this->dataIn['ssKey5earch']!=""){				
			   	$codClient 	  = $this->dataIn['ssKey5earch'];							   	
				$aDataCliente = $cSapClientes->getDataById($codClient); 				
						    			
				$classObject 	= new My_Model_Solicitudes();
				$cCitas			= new My_Model_Citas();			
				$cFunctions 	= new My_Controller_Functions();
				$cHorariosCita  = new My_Model_HorariosCita();
				$cUnidades 		= new My_Model_Unidades();
				$aTipoServicio	= $cCitas->getCboTipoServicio(true);
				$aHorarios		= $cHorariosCita->getHorarios();
				$aUnidades		= $cUnidades->getCbo($aDataCliente['ID_CLIENTE']);
				$cLog			= new My_Model_LogSolicitudes();
				$cContactos		= new My_Model_Contactos();
				$sTipo			= '';
				$sHorario		= '';
				$sHorario2		= '';
				$sUnidad		= '';	
				$sContacto		= '';	
				$aContactos		= $cContactos->getCbo($aDataCliente['COD_CLIENTE']);
				$this->dataIn['inputCliente'] = $aDataCliente['ID_CLIENTE'];			
				//$this->dataIn['inputUserQr']  = $aDataCliente['ID_CONTACTO_QR'];    			
    			
				$sSubject = '';
				$sBody    = '';				
				
    			if($this->operation=='updateUnits'){
					//192.168.6.41
					//201.131.96.40
					//$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
					$soap_client  = new SoapClient("http://ws.grupouda.com.mx/wsUDAHistoryGetByPlate.asmx?WSDL");
					$aParams 	  = array('sLogin'     => 'wbs_test@grupouda.com.mx',
					                  	  'sPassword'  => 't3stud4',
										  'strCustomerPass' => $aDataCliente['COD_CLIENTE']);	
						
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
					          			$aDataInsertUnit['inputIdCliente'] 	= $aDataCliente['ID_CLIENTE'];	
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
				        		$aUnidades		= $cUnidades->getCbo($aDataCliente['ID_CLIENTE']);
				        		$this->resultop = 'okUpdate';			        		
				        	}elseif($c=0 && $bContinue==0){
				        		$this->errors['no-units'] = 1;
				        	}else if($bContinue==1){
				        		$this->errors['problem-units'] = 1;
				        	}else if($bContinue==1){
				        		$this->errors['problem-units'] = 1;	
				        	}
						}else{
							$this->errors['problem-units'] = 1;
						}
					}else{
						$this->errors['problem-units'] = 1;
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
					$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
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
					//$cFunctions->sendMailAdmins($sSubject,$sBody);	
					//$this->_redirect('/external/request/index');				
				}
	
	    		if(count($this->errors)>0 && $this->operation!=""){
	    			$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
	    			$sTipo						= $this->dataIn['inputTipo'];
	    			$dataInfo['UNIDAD']			= $this->dataIn['inputUnidad'];
	    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];    				
				}
				
				$this->view->aUnidades	  = $cFunctions->selectDb($aUnidades,$sUnidad);
				$this->view->aHorarioCita = $cFunctions->selectDb($aHorarios,$sHorario);
				$this->view->aHorarioCita2= $cFunctions->selectDb($aHorarios,$sHorario2);
				$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);
				$this->view->data 		= $dataInfo;
				$this->view->errors 	= $this->errors;
				$this->view->resultOp   = $this->resultop;
				$this->view->catId		= $this->idToUpdate;
				$this->view->idToUpdate = $this->idToUpdate;
				$this->view->ssKey5earch = $codClient;  				
				$this->view->aContactos  = $cFunctions->selectDb($aContactos,$sContacto);
    		}else{
				$this->_redirect('/sac/main/index');    			
    		}    			    	
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
				//$soap_client  = new SoapClient("http://ws.grupouda.com.mx/wsUDAHistoryGetByPlate.asmx?WSDL");
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
}
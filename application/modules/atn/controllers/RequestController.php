<?php 

class atn_RequestController extends My_Controller_Action
{
	protected $_clase = 'msolicitud';
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

    public function indexAction()
    {
		try{    		 
			$cFunciones		= new My_Controller_Functions();			
			$cSolicitudes	= new My_Model_Solicitudes();

			$this->view->dataTable    = $cSolicitudes->getDataTable('1,4,5');
			$this->view->dataTableOk  = $cSolicitudes->getDataTable(2);
			$this->view->dataTableRev = $cSolicitudes->getDataTable(4);
			
			$this->view->dataTableEmp    = $cSolicitudes->getDataTableEmp('1,4');
			//$this->view->dataTableEmpRev = $cSolicitudes->getDataEmp(5);
			$this->view->dataTableEmpOk  = $cSolicitudes->getDataTableEmp(2);
        }catch (Zend_Exception $e) {
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
			$cEstatus		= new My_Model_EstatusSolicitud();
			$aEstatus 		= $cEstatus->getCbo(1);						
			$sEstatus		= '';
			
			$cHorariosCita  = new My_Model_HorariosCita();
			$cUnidades 		= new My_Model_Unidades();
			$aTipoServicio	= $cCitas->getCboTipoServicio(true);
			$aHorarios		= $cHorariosCita->getHorarios();
			$cLog			= new My_Model_LogSolicitudes();
			$aLogs			= Array();
			$cHtmlMail		= new My_Controller_Htmlmailing();	
			
			$sTipo			= '';
			$sHorario		= '';
			$sHorario2		= '';
			$sUnidad		= '';			

			if($this->idToUpdate >-1){
				$dataInfo   = $classObject->getData($this->idToUpdate);
				$aUnidades	= $cUnidades->getCbo($dataInfo['ID_CLIENTE']);
				
				
				$aLogs		= $cLog->getDataTable($this->idToUpdate);
				$sTipo		= $dataInfo['ID_TIPO'];
				$sHorario	= $dataInfo['ID_HORARIO'];
				$sHorario2	= @$dataInfo['ID_HORARIO2'];
				$sUnidad	= $dataInfo['ID_UNIDAD'];				
			}
			$sModificaciones = '';
    		if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){

					if($this->dataIn['bOperation']=='modify'){
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';						
						
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
						}
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}						
						
						if($this->dataIn['inputRevision']!=$dataInfo['REVISION']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputRevision'].'<br/>';							
						}
					}
					$updated = $classObject->updateAtencion($this->dataIn);
					if($updated['status']){
						$dataInfo   = $classObject->getData($this->idToUpdate);
						$aUnidades	= $cUnidades->getCbo($dataInfo['ID_CLIENTE']);
						$sTipo		= $dataInfo['ID_TIPO'];
						$sHorario	= $dataInfo['ID_HORARIO'];
						$sHorario2	= @$dataInfo['ID_HORARIO2'];
						$sUnidad	= $dataInfo['ID_UNIDAD'];	

						$cMailing   = new My_Model_Mailing();
						$sSubject 	= 'Revision de Solicitud de Cita';
						
						if($this->dataIn['bOperation']=='accept'){
							$cHtmlMail->acceptAdminSolicitud($dataInfo,$this->view->dataUser);		
											
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por CCUDA',
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);
						}else{
							$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
							$cHtmlMail->changeSolicitudExt($dataInfo,$this->view->dataUser);
										
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones,
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);																		
						}
						
						$this->resultop = 'okRegister';
						$this->_redirect('/atn/request/index');
					}					
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}			

			$this->view->aUnidades	  = $cFunctions->selectDb($aUnidades,$sUnidad);
			$this->view->aHorarioCita = $cFunctions->selectDb($aHorarios,$sHorario);
			$this->view->aHorarioCita2= $cFunctions->selectDb($aHorarios,$sHorario2);
			$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);			
			$this->view->aEstatus   = $cFunctions->selectDb($aEstatus,$sEstatus);
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
    
    
    public function getinfoempAction(){    
    	try{    		 
			$dataInfo = Array();			
			$classObject 	= new My_Model_Solicitudes();
			$cCitas			= new My_Model_Citas();			
			$cFunctions 	= new My_Controller_Functions();
			$cHorariosCita  = new My_Model_HorariosCita();
			$cUnidades 		= new My_Model_Unidades();
			$cLog			= new My_Model_LogSolicitudes();
			$cEstatus		= new My_Model_EstatusSolicitud();
			$cSucursales	= new My_Model_Sucursales();			
			$aEstatus 		= $cEstatus->getCbo(1);	
			$cTipoEquipo	= new My_Model_Tequipos();	
			$cProtocolos	= new My_Model_Protocolos();
			$aTiposContrato  = $cProtocolos->getTipo();
			$aProtocolo 	= Array();
			$sEstatus		= '';
			$sTipoContrato   = '';
			$aContactos		= Array();
			
			$aTipoServicio	= $cCitas->getCboTipoServicio();
			$aHorarios		= $cHorariosCita->getHorarios();
			$aUnidades		= $cUnidades->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$aSucursales	= $cSucursales->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$cHtmlMail		= new My_Controller_Htmlmailing();	
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
    			$aProtocolo = $cProtocolos->getData($this->idToUpdate);						
				if(count($aProtocolo)>0){
					$sTipoContrato = @$aProtocolo['ID_TIPO_CONTRATO'];
					$aContactos    = $cProtocolos->getPersonas(@$aProtocolo['ID_PROTOCOLO']);	
				}
				$sTipo		= $dataInfo['ID_TIPO'];
				$sHorario	= $dataInfo['ID_HORARIO'];
				$sUnidad	= $dataInfo['ID_UNIDAD'];
				$sSucursal  = $dataInfo['ID_SUCURSAL'];
				$sTequipo	= $dataInfo['ID_TIPO_EQUIPO'];
			}			
					
			$sModificaciones = '';
    		if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){

					if($this->dataIn['bOperation']=='modify'){
						$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';						
						
						if($this->dataIn['inputHorario']!=$dataInfo['ID_HORARIO']){
							$sModificaciones .= 'Se modifico el horario <br/>';							
						}
						
						if($this->dataIn['inputFechaIn']!=$dataInfo['FECHA_CITA']){
							$sModificaciones .= 'Se modifico la fecha de la cita <br/>';							
						}						
						
						if($this->dataIn['inputRevision']!=$dataInfo['REVISION']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputRevision'].'<br/>';							
						}
					}
					$updated = $classObject->updateAtencion($this->dataIn);
					if($updated['status']){
						$dataInfo   = $classObject->getDataEmp($this->idToUpdate);						
						//$aUnidades	= $cUnidades->getCbo($dataInfo['ID_CLIENTE']);						
						
						$sTipo		= $dataInfo['ID_TIPO'];
						$sHorario	= $dataInfo['ID_HORARIO'];
						$sHorario2	= @$dataInfo['ID_HORARIO2'];
						$sUnidad	= $dataInfo['ID_UNIDAD'];	

						$cMailing   = new My_Model_Mailing();
						$sSubject 	= 'Revision de Solicitud de Cita';
						
						if($this->dataIn['bOperation']=='accept'){
							
							/*$cHtmlMail		= new My_Controller_Htmlmailing();	
							$sBody  	= 'Se ha revisado la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>';
							$sBody     .= 'La solicitud ha sido aceptada con la siguiente informaci&oacute;n:<br/>'.
										  '<table><tr><td><b>Fecha</b></td><td>'.$dataInfo['FECHA_CITA'].'</td></tr>'.	
											'<tr><td><b>Horario</b></td><td>'.$dataInfo['N_HORARIO'].'</td></tr>'.
											$sHorario2.
											'<tr><td><b>Tipo de Cita</b></td><td>'.$dataInfo['N_TIPO'].'</td></tr>'.	
											'<tr><td><b>Unidad</b></td><td>'.$dataInfo['N_UNIDAD'].'</td></tr>'.		
											'<tr><td><b>Informaci&oacute;n de la Unidad</b></td><td>'.$dataInfo['INFORMACION_UNIDAD'].'</td></tr>'.
											'<tr><td><b>Comentarios</b></td><td>'.$dataInfo['COMENTARIO'].'</td></tr>'.			
											'<tr><td><b>Comentarios CCUDA</b></td><td>'.$dataInfo['REVISION'].'</td></tr></table><br/>'.					  
										  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
										  '<a href="http://siames.grupouda.com.mx">Da Click Aqui</a><br/>'.
										  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
										  '<b> http://siames.grupouda.com.mx</b>';
											*/
							$cHtmlMail->acceptAdminSolicitudArrenda($dataInfo,$this->view->dataUser);
							
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por CCUDA',
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);			
						}else{
							$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
							$cHtmlMail->changeSolicitudArrenda($dataInfo,$this->view->dataUser);
							/*
							$sBody  	= 'Se ha revisado la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>';
							$sBody     .= 'La solicitud ha sido modificada con la siguiente informaci&oacute;n, favor de validarla:<br/>'.
										  '<table><tr><td><b>Fecha</b></td><td>'.$dataInfo['FECHA_CITA'].'</td></tr>'.	
											'<tr><td><b>Horario</b></td><td>'.$dataInfo['N_HORARIO'].'</td></tr>'.
											$sHorario2.
											'<tr><td><b>Tipo de Cita</b></td><td>'.$dataInfo['N_TIPO'].'</td></tr>'.	
											'<tr><td><b>Unidad</b></td><td>'.$dataInfo['N_UNIDAD'].'</td></tr>'.		
											'<tr><td><b>Informaci&oacute;n de la Unidad</b></td><td>'.$dataInfo['INFORMACION_UNIDAD'].'</td></tr>'.			
											'<tr><td><b>Comentarios</b></td><td>'.$dataInfo['COMENTARIO'].'</td></tr></table><br/>'.
											'<tr><td><b>Comentarios CCUDA</b></td><td>'.$dataInfo['REVISION'].'</td></tr></table><br/>'.					  
										  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
										  '<a href="http://siames.grupouda.com.mx">Da Click Aqui</a><br/>'.
										  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
										  '<b> http://siames.grupouda.com.mx</b>';
											*/
																		
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones,
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);																		
						}
						/*
						$aMailer    = Array(
							'inputIdSolicitud'	 => $this->idToUpdate,
							'inputDestinatarios' => $dataInfo['N_CONTACTO'],
							'inputEmails' 		 => $dataInfo['EMAIL'],
							'inputTittle' 		 => $sSubject,
							'inputBody' 		 => $sBody,
							'inputLiveNotif'	 => 0,
							'inputFromName' 	 => 'contacto@grupouda.com.mx',
							'inputFromEmail' 	 => 'Siames - Grupo UDA'						
						);	
	
						$cMailing->insertRow($aMailer);*/
						
						$this->resultop = 'okRegister';
						//$this->_redirect('/atn/request/index');
					}					
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}			
			
			$this->view->aTequipos	= $cFunctions->selectDb($aTipoEquipo,$sTequipo);	
			$this->view->aUnidades	= $cFunctions->selectDb($aUnidades,$sUnidad);
			$this->view->aHorarioCita = $cFunctions->selectDb($aHorarios,$sHorario);					
			$this->view->aTipos		= $cFunctions->selectDb($aTipoServicio,$sTipo);
			$this->view->aSucursales= $cFunctions->selectDb($aSucursales,$sSucursal);
			$this->view->sTipos     = $cFunctions->selectDb($aTiposContrato,$sTipoContrato);
			$this->view->aPersonas = $this->processFields($aContactos);						
			$this->view->data 		= $dataInfo; 
			$this->view->logTable   = $aLogs;
			$this->view->errors 	= $this->errors;	
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;
			$this->view->aProtocolo = $aProtocolo;			
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
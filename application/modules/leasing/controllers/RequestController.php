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
			
			
			$aTipoServicio	= $cCitas->getCboTipoServicio(true);
			$aHorarios		= $cHorariosCita->getHorarios();
			$aUnidades		= $cUnidades->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			$aSucursales	= $cSucursales->getCbobyEmp($this->view->dataUser['ID_EMPRESA']);
			
			$sTipo			= '';
			$sHorario		= '';
			$sUnidad		= '';
			$sSucursal		= '';
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
			}
			
			$sSubject = '';
			$sBody    = '';
			$sModificaciones = '';
			if($this->operation=='update'){
				if($this->idToUpdate>-1){
					if($this->dataIn['bOperation']=='accept'){				
						$updated = $classObject->updateRowEmp($this->dataIn);
						if($updated['status']){
							$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
							$sTipo		= $dataInfo['ID_TIPO'];
							$sHorario	= $dataInfo['ID_HORARIO'];	
							$sUnidad	= $dataInfo['ID_UNIDAD'];

							$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
							$sHorarioLog  = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<b>Horario 2</b:'.$dataInfo['N_HORARIO2'].'<br/>': '';							
							
							$sSubject 	= 'Solicitud Aceptada por el usuario';
							$sBody  	= 'Se ha revisado la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>';
							$sBody     .= 'La solicitud ha sido aceptada con la siguiente informaci&oacute;n:<br/>'.
										  '<table><tr><td><b>Fecha</b></td><td>'.$dataInfo['FECHA_CITA'].'</td></tr>'.	
											'<tr><td><b>Horario</b></td><td>'.$dataInfo['N_HORARIO'].'</td></tr>'.
											$sHorario2.
											'<tr><td><b>Tipo de Cita</b></td><td>'.$dataInfo['N_TIPO'].'</td></tr>'.	
											'<tr><td><b>Unidad</b></td><td>'.$dataInfo['N_UNIDAD'].'</td></tr>'.		
											'<tr><td><b>Informaci&oacute;n de la Unidad</b></td><td>'.$dataInfo['INFORMACION_UNIDAD'].'</td></tr>'.
											'<tr><td><b>Comentarios</b></td><td>'.$dataInfo['COMENTARIO'].'</td></tr>'.								  
										  'Para revisarlo, debes de ingresar al siguiente link:<br/>'.
										  '<a href="http://192.168.6.23">Da Click Aqui</a><br/>'.
										  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
										  '<b> http://192.168.6.23</b>';
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

							$sSubject 	= 'Solicitud Modificada por el usuario';
							$sBody     .= 'La solicitud ha sido modificada con la siguiente informaci&oacute;n, favor de validarla:<br/>'.
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
											
							$aLog = Array ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones ,
											'sOrigen'		=> 'USUARIO');
							$cLog->insertRow($aLog);
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
							
							if($sModificaciones!=''){
								$sSubject 	= 'Cambio en Solicitud de Cita';
								$sBody  	= 'El cliente <b>'.$this->view->dataUser['RAZON_SOCIAL'].'</b> ha realizado un cambio en la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>'.
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
				$aDataUnit = $cUnidades->getData($this->dataIn['inputUnidad']);
				$this->dataIn['inputInfo'] = "<b>Ult.reporte:</b> S/N<br/><b>Placas:</b>".$aDataUnit['PLACAS']."<br/><b>Eco:</b> ".$aDataUnit['ECONOMICO']."<br/><b>Ip:</b>".$aDataUnit['IDENTIFICADOR']."<br/><b>Tipo Equipo:</b>".$aDataUnit['TIPO_EQUIPO']."<br/><b>Tipo Unidad:</b>".$aDataUnit['TIPO_VEHICULO']."<br/>";		
				$insert = $classObject->insertRowEmp($this->dataIn);			
		 		if($insert['status']){	
		 			$this->idToUpdate = $insert['id'];	
					$dataInfo   = $classObject->getDataEmp($this->idToUpdate);
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
				$this->_redirect('/leasing/request/index');				
			}

    		if(count($this->errors)>0 && $this->operation!=""){
    			$dataInfo['FECHA_CITA'] 	= $this->dataIn['inputFechaIn'];
    			$sTipo						= $this->dataIn['inputTipo'];
    			$dataInfo['UNIDAD']			= $this->dataIn['inputUnidad'];
    			$dataInfo['COMENTARIO']		= $this->dataIn['inputComment'];    			
			}			
			
			
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
}
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
    		   
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cSolicitudes   = new My_Model_Solicitudes();
			
			$aSucursales 	= "";
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch	= Array(		
								array("id"=>"-1",'name'=>'Todos' ),
								array("id"=>"1" ,'name'=>'Pendiente' ),
								array("id"=>"2" ,'name'=>'Aceptado'  ),
								array("id"=>"6" ,'name'=>'Por Atender'),
								array("id"=>"7" ,'name'=>'Atendida') );
			$bType 			= -1;
			$bStatus		= -1;	

			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				
				/*
				if(isset($this->dataIn['cboInstalacion']) && $this->dataIn['cboInstalacion']>0){
					$aSucursales	= $this->dataIn['cboInstalacion'];
					$idSucursal		= $this->dataIn['cboInstalacion'];	
				}*/
				
				//$idTecnico	= $this->dataIn['inputTecnicos'];
				//
				//$bStatus	= $this->dataIn['inputStatus'];
				$bType		= $this->dataIn['cboTypeSearch'];				
				$bShowUsers=true;
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
				$bShowUsers=true;
				$idSucursal		= "";	
			}			
			
			$dataResume     = $cSolicitudes->getResumeByDay($dFechaIn,$dFechaFin,-1,$bType);	
			$this->view->aTypeSearchs	= $cFunciones->cbo_from_array($aTypeSearch,$bType);		
			$this->view->dataResume	 	= $dataResume;
			$this->view->data 			= $this->dataIn;
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
						
						$sTipo		= @$dataInfo['ID_TIPO'];
						$sHorario	= @$dataInfo['ID_HORARIO'];
						$sHorario2	= @$dataInfo['ID_HORARIO2'];
						$sUnidad	= @$dataInfo['ID_UNIDAD'];	

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
			}else if($this->operation=='close'){
				$updated = $classObject->updateClose($this->dataIn,7);				
				if($updated){
					$this->redirect("/atn/request/index");	
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

	public function exportallAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();	

			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cSolicitudes   = new My_Model_Solicitudes();

			$dFechaIn	= $this->dataIn['inputFechaIn'];
			$dFechaFin	= $this->dataIn['inputFechaFin'];
			$bType		= $this->dataIn['cboTypeSearch'];
						
			$dataResume     = $cSolicitudes->getResumeByDay($dFechaIn,$dFechaFin,-1,$bType);	
			
	   	 	if(count($dataResume)>0){
				// PHPExcel  
				require_once 'PHPExcel.php';	

				$objPHPExcel = new PHPExcel();
 					
				$objPHPExcel->getProperties()->setCreator("UDA")
										 ->setLastModifiedBy("UDA")
										 ->setTitle("Office 2007 XLSX")
										 ->setSubject("Office 2007 XLSX")
										 ->setDescription("Reporte del Viaje")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Reporte del Viaje");
				
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);											 
				$sHeaderBig   	 = new PHPExcel_Style();
				$stylezebraTable = new PHPExcel_Style();  
				$sHeaderOrange 	 = new PHPExcel_Style();
				$sTittleTable 	 = new PHPExcel_Style();
						
				$stylezebraTable->applyFromArray(array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
					)
				));		

				$sHeaderBig->applyFromArray(array(
					'fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => 'FFFFFF')
			        ),
			        'font'  => array(
				        'bold'  => true,
				        'color' => array('rgb' => '000000'),
				        'size'  => 16,
				        'name'  => 'Arial'
				    ),
					  'borders' => array(
					    'allborders' => array(
					      'style' => PHPExcel_Style_Border::BORDER_NONE
					    )
					  )				        
				));			

				$sHeaderOrange->applyFromArray(array(
					'fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => 'FFFFFF')
			        ),
			        'font'  => array(
				        'bold'  => true,
				        'color' => array('rgb' => 'FF8000'),
				        'size'  => 10,
				        'name'  => 'Arial'
				    ),
					  'borders' => array(
					    'allborders' => array(
					      'style' => PHPExcel_Style_Border::BORDER_NONE
					    )
					  )
				));		

				$sTittleTable->applyFromArray(array(
					'fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => 'FF8000')
			        ),
			        'font'  => array(
				        'bold'  => true,
				        'color' => array('rgb' => 'FFFFFF'),
				        'size'  => 10,
				        'name'  => 'Arial'
				    ),
					  'borders' => array(
					    'allborders' => array(
					      'style' => PHPExcel_Style_Border::BORDER_NONE
					    )
					  )
				));						
			
				 //
				 // Header del Reporte
				 //					
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
				$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBig, 'B3:J3');
				
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				
				$objDrawing->setPath($this->realPath.'/logoUDA.jpg');
				$objDrawing->setWidth(70);
				$objDrawing->setHeight(90);
				//$objDrawing->setOffsetX(10);
				$objDrawing->setCoordinates('I2');
				
				$objPHPExcel->getActiveSheet()->getRowDimension('I2')->setRowHeight(150);										
				$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('SOLICITUDES SIN ATENDER'));
				$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');												
								
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Fecha');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Tipo Servicio');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Horario');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Tipo Equipo');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Tipo Equipo');	
				
				
				/*
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Folio Cita');
				
													
				//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Cliente');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Fecha Programada');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', 'Hora Programada');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', 'Hora Inicio');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', 'Hora Terminado');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', 'Tecnico Asignado');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', 'Direccion Cita');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleTable, 'A7:J7');	*/													
				
				$rowControl		= 8;
				$zebraControl  	= 0;
				
					foreach($dataResume as $key => $items){	
						/*					
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['N_TIPO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);								
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['NOMBRE_CLIENTE']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['F_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['H_PROGRAMADA']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['FECHA_INICIO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_TERMINO']);								
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['NOMBRE_TECNICO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControl), $items['DIRECCION']);

						if($zebraControl++%2==1){
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':J'.$rowControl);			
						}
						$rowControl++;*/
					}						
	
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);			
						
					$filename  = "Reporte_Solicitudes_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header("Content-Disposition: attachment; filename=$filename"); 
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
												
			}else{
				echo "No Hay informaciÑn";
			}
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}	
}
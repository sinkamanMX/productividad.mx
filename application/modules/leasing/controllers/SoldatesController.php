<?php 

class leasing_SoldatesController extends My_Controller_Action
{
	protected $_clase = 'msoldates';
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
			$cSolicitudes   = new My_Model_Soleasing();
			
			$this->view->dataTableEmp    = $cSolicitudes->getDataTable($this->view->dataUser['ID_EMPRESA'],'1,4');
			$this->view->dataTableEmpOk  = $cSolicitudes->getDataTable($this->view->dataUser['ID_EMPRESA'],'2');
			$this->view->dataTableEmpOk  = $cSolicitudes->getDataTable($this->view->dataUser['ID_EMPRESA'],'6,7');
        }catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
        
    public function getinfoempAction(){    
    	try{    		 
			$dataInfo = Array();			
			$classObject 	= new My_Model_Soleasing();
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
			$cHtmlMail		= new My_Controller_Maileasing();
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
						
						if($this->dataIn['inputrequest']!=$dataInfo['REVISION']){
							$sModificaciones .= 'Comentario:'.$this->dataIn['inputrequest'].'<br/>';							
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
							$cHtmlMail->acceptAdminSolicitudArrenda($dataInfo,$this->view->dataUser);
							
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por CCUDA',
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);
								
							$updateSol = $classObject->updateStatus($this->idToUpdate,6);
							
						}else{
							$sHorario2    = (isset($dataInfo['ID_HORARIO2']) && $dataInfo['ID_HORARIO2']!="") ? '<tr><td><b>Horario 2</b></td><td>'.$dataInfo['N_HORARIO2'].'</td></tr>': '';
							$cHtmlMail->changeSolicitudArrenda($dataInfo,$this->view->dataUser);
							
																		
							$aLog = Array  ('idSolicitud' 	=> $this->idToUpdate,
											'sAction' 		=> 'Cambio en la Solicitud',
											'sDescripcion' 	=> 'Modificaciones : <br>'.$sModificaciones,
											'sOrigen'		=> 'CCUDA');
							$cLog->insertRow($aLog);																		
						}

						
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
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

			$this->view->dataTable   = $cSolicitudes->getDataTable(0);
			$this->view->dataTableOk = $cSolicitudes->getDataTable(1);
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

			if($this->idToUpdate >-1){
				$dataInfo   = $classObject->getData($this->idToUpdate);
				$sEstatus	= $dataInfo['ID_ESTATUS'];
			}
			
    		if($this->operation=='update'){	  		
				if($this->idToUpdate>-1){
					$updated = $classObject->updateAtencion($this->dataIn);
					if($updated['status']){
						$dataInfo   = $classObject->getData($this->idToUpdate);
						$sEstatus	= $dataInfo['ID_ESTATUS'];
						$sSubject 	= 'Revision de Solicitud de Cita';
						$sBody  	= 'Se ha revisado la solicitud de cita (con el #'.$this->idToUpdate.') en el sistema de Siames<br/>';
						
						if($dataInfo['ID_ESTATUS']==2){
							$sBody .= 'La solicitud de cita ha sido aceptada, y se realizara el dia '.$dataInfo['FECHA_CONFIRMADA'].' en un Horario de '.$dataInfo['HORA_INICIO'].' a '.$dataInfo['HORA_FIN'].' <br/>';							
						}elseif($dataInfo['ID_ESTATUS']==3){
							$sBody .= 'La solicitud de cita ha sido rechazada <br/>';
						}
						 						
						$sBody .= 'Comentarios: '.$dataInfo['REVISION'];
						
						$aMailer    = Array(
							'emailTo' 	=> $dataInfo['EMAIL'],
							'nameTo' 	=> $dataInfo['N_CONTACTO'],
							'subjectTo' => ($sSubject),
							'bodyTo' 	=> $sBody,
						);	
						
						$cFunctions->sendMailSmtp($aMailer);
						$this->resultop = 'okRegister';
						$this->_redirect('/atn/request/index');
					}
				}else{
					$this->errors['status'] = 'no-info';
				}	
			}			
						
			$this->view->aEstatus   = $cFunctions->selectDb($aEstatus,$sEstatus);
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
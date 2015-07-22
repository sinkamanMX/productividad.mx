<?php 

class atn_DashboardController extends My_Controller_Action
{
	protected $_clase = 'mdashboard';
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
			$this->view->dataUser['allwindow'] = true;
			$cResumen  = new My_Model_Resumen();
			
			$aTecnicos 		= $cResumen->getTecnicos();			
			$aCitas    		= $cResumen->getCitasPendientes();
			$aDataProcess 	= $this->processInfo($aTecnicos, $aCitas);

			$this->view->aData 		= $aDataProcess;
			$this->view->aEstatus 	= $cResumen->getStatus();
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function processInfo($aTecnicos,$aCitas){
    	$aResult = Array();
    	
    	foreach($aTecnicos as $key => $items){
    		$aDataCitas = Array();
    		foreach($aCitas as $key => $itemCita){
    			if($items['ID_USUARIO'] == $itemCita['ID_USUARIO']){
    				$dateSinicio = strtotime(date($itemCita['FECHA_INICIO']));  
    				$dateSfin	 = strtotime(date($itemCita['FECHA_FIN']));  				
    				$itemCita['fechaSin'] 	= str_pad($dateSinicio,13,"0",STR_PAD_RIGHT);
    				$itemCita['fechaSfin'] 	= str_pad($dateSfin,13,"0",STR_PAD_RIGHT);
    				
    				$aDataCitas[] = $itemCita;
    			}
    		}
    		
    		$items['citas'] = $aDataCitas;
    		$aResult[] = $items;
    	}
    	
    	return $aResult;
    }
}
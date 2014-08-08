<?php

class atn_ServicesController extends My_Controller_Action
{	
	protected $_clase = 'mservices';
	public $dataIn;	
	public $aService;
		
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
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

    public function indexAction()
    {
    	try{
    		$this->view->dataUser['allwindow'] = true;   
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;		
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);

			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				$idSucursal	= $this->dataIn['cboInstalacion'];
				$idTecnico	= $this->dataIn['inputTecnicos'];
				$bShowUsers=true;
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
				$idSucursal	= $this->view->dataUser['ID_SUCURSAL'];
				$bShowUsers=true;
			}
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($idSucursal);
			$dataResume     = $cCitas->getResumeByDay($idSucursal,$dFechaIn,$dFechaFin,$idTecnico);
			$dataProcess	= $cFunciones->setResume($dataResume);
			
			$this->view->cInstalaciones 	= $cFunciones->selectDb($dataCenter,$idSucursal);
			$this->view->aTecnicos 			= $cFunciones->selectDb($aTecnicos,$idTecnico);	
			$this->view->data 				= $this->dataIn;
			$this->view->dataResume 	 	= $dataProcess;
			$this->view->dataResumeTotal 	= $dataProcess['TOTAL'];
			$this->view->showUsers			= $bShowUsers;
			$this->view->aResume 			= $dataResume;
			unset($this->view->dataResume['TOTAL']);	
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getlastpAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
				$cTecnicos  = new My_Model_Tecnicos();				
				$allInputs  = implode(',', $this->dataIn['strInput']);				
				$dataPos    = $cTecnicos->getLastPositions($allInputs);		
				foreach ($dataPos as $key => $items){
					if($items['ID']!="" && $items['ID']!="NULL")
					$result .= ($result!="") ? "!" : "";
					$result .=  $items['ID']."|".
								$items['FECHA_GPS']."|".
                                $items['EVENTO']."|".
                                $items['LATITUD']."|".
                                $items['LONGITUD']."|".
                                round($items['VELOCIDAD'],2)."|".
                                round($items['NIVEL_BATERIA'],2)."|".
                                $items['TIPO_GPS']."|".
                                $items['ANGULO']."|".
                                $items['UBICACION'];
				}
			}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }     	
    }
    
    public function getinformationAction(){
		$this->view->layout()->setLayout('blank');
		
		if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
			$cTecnicos		= new My_Model_Tecnicos();
			$cFunctions 	= new My_Controller_Functions();
			$cCitas			= new My_Model_Citas();
			$dToday			= Date("Y-m-d");
			$dToday			= '2014-08-20';
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($this->dataIn['strInput']);
			$this->view->aTecnicos = $cFunctions->selectDb($aTecnicos);
			
			$dataResume     = $cCitas->getResumeByDay($this->dataIn['strInput'],$dToday);
			$dataProcess	= $cFunctions->setResume($dataResume);
			$this->view->dataResume 	 = $dataProcess;
			$this->view->dataResumeTotal = $dataProcess['TOTAL'];
			unset($this->view->dataResume['TOTAL']);
		}
    	
    	$this->view->data = $this->dataIn;
    }

}
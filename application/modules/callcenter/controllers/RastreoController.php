<?php

class callcenter_RastreoController extends My_Controller_Action
{	
	protected $_clase = 'mrastreo';
	public $dataIn;	

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
    
    public function indexAction(){
    	try{
    		$cActivos = new My_Model_Activos();
    		
    		$dataTable = $cActivos->getTableSearch($this->dataIn);
    		
    		$this->view->datatTable = $dataTable;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }	
    
    public function mapaAction(){
		$this->view->dataUser['allwindow'] = true;    	
		
		if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
			$cActivos = new My_Model_Activos();
			$IdObject = $this->dataIn['strInput'];
			
			$this->view->data = $cActivos->getAllData($IdObject);
			$this->view->data['strInput'] = $IdObject;
			
			$this->view->recorridoToday = $cActivos->getHistoryByDay($IdObject,true);
			$this->view->recorridoYest  = $cActivos->getHistoryByDay($IdObject,false);
		}else{
			$this->_redirect('/callcenter/rastreo/index');	
		}
    }
    
    public function lpositionAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
				$cActivos = new My_Model_Activos();
				$IdObject = $this->dataIn['strInput'];
				
				$dataActivo = $cActivos->getLasPosition($IdObject);
				
				$result = 	$dataActivo['ID_ACTIVO']."|".
							$dataActivo['EVENTO']."|".
							$dataActivo['LATITUD']."|".
							$dataActivo['LONGITUD']."|".
							round($dataActivo['VELOCIDAD'],2)."|".
							$dataActivo['UBICACION']."|".
							$dataActivo['ANGULO']."|".
							$dataActivo['BATERIA']."|".
							$dataActivo['FECHA_GPS'];
			}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }   	
    }
}
<?php 

class atn_ConsultaController extends My_Controller_Action
{
	protected $_clase = 'mconsulta';
	public $dataIn;	
	public $aService;
		
    public function init()
    {
    	try{	
			$sessions = new My_Controller_Auth();
			$this->dataIn 			= $this->_request->getParams();
					
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function validacionAction(){
    	try{
			$this->view->layout()->setLayout('layout_blank');						 		
			//$cCitas     = new My_Model_Formresult();
			$cCitas = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$aDataInfo  = Array();

			if(isset($this->dataIn['idCita']) && $this->dataIn['idCita']!="" && 
			   isset($this->dataIn['idFormulario']) && $this->dataIn['idFormulario']!="" ){
			   	$idCita 	  =	$this->dataIn['idCita'];
			   	$idFormulario = $this->dataIn['idFormulario'];
				$aDataInfo    = $cCitas->getDataSendbyForms($idCita,$idFormulario);	
			}   

			$this->view->aDataInfo = $aDataInfo;    		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }   
    }
}
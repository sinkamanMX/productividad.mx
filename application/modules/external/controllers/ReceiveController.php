<?php

class external_ReceiveController extends My_Controller_Action
{	
    public function init()
    {
					
    }
    
    public function acceptAction(){
    	try{
    		$this->view->layout()->setLayout('blank');
    		
    		$cSolicitudes	= new My_Model_Solicitudes();    		
    		$cHtmlMail		= new My_Controller_Htmlmailing();
    		$cLog			= new My_Model_LogSolicitudes();
    		$dataInfo		= Array();
    		$this->_dataIn	= $this->_request->getParams();
    		
    		if(isset($this->_dataIn['strSSkeyInput']) && $this->_dataIn['strSSkeyInput']!=""){
    			$sKeySolicitud = $this->_dataIn['strSSkeyInput'];
    			
    			$validateSol = $cSolicitudes->validateKey($sKeySolicitud);
    			if(isset($validateSol['ID_SOLICITUD']) && $validateSol['ID_SOLICITUD']!=""){
    				if($validateSol['ID_CLIENTE']!=""){
    					$aDataValid = Array();
    					$aDataValid['bOperation'] = 'accept';
    					$aDataValid['catId']	  = $validateSol['ID_SOLICITUD'];
    					$aDataValid['sskeyValid'] = $sKeySolicitud;
    					
    					$updateSol = $cSolicitudes->updateRow($aDataValid);
    					$dataInfo  = $cSolicitudes->getData($aDataValid['catId']);
    					
						$aLog = Array  ('idSolicitud' 		=> $aDataValid['catId'],
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por el usuario',
											'sOrigen'		=> 'USUARIO');
						$cLog->insertRow($aLog);    

						$aDataUser = Array();
						$aDataUser['N_USER']  	= $dataInfo['N_CONTACTO'];
						$aDataUser['EMAIL']   	= $dataInfo['EMAIL'];
						$aDataUser['N_CLIENTE'] = $dataInfo['N_CLIENTE'];
									
    					$cHtmlMail->acceptuserExternalSolicitud($dataInfo,$aDataUser);
    					$sResult = 'ok';
    				}else{
    					$aDataValid = Array();
    					$aDataValid['bOperation'] = 'accept';
    					$aDataValid['catId']	  = $validateSol['ID_SOLICITUD'];
    					$aDataValid['sskeyValid'] = $sKeySolicitud;
    					$updateSol = $cSolicitudes->updateRowEmp($aDataValid);    					
    					$dataInfo  = $cSolicitudes->getDataEmp($aDataValid['catId']);
    					
						$aLog = Array  ('idSolicitud' 		=> $aDataValid['catId'],
											'sAction' 		=> 'Solicitud Aceptada',
											'sDescripcion' 	=> 'La solicitud ha sido aceptada por el usuario',
											'sOrigen'		=> 'USUARIO');
						$cLog->insertRow($aLog);
						
						$aDataUser = Array();
						$aDataUser['N_USER']  	= $dataInfo['N_CONTACTO'];
						$aDataUser['EMAIL']   	= $dataInfo['EMAIL'];
						$aDataUser['N_EMPRESA'] = $dataInfo['N_CLIENTE'];
												
    					$cHtmlMail->acceptuserSolicitud($dataInfo,$aDataUser);
    					$sResult = 'ok';
    				}
    			}else{
    				$sResult = 'key-novalid';	
    			}
    		}else{
    			$this->_redirect('http://siames.grupouda.com.mx');	
    		}
    		
    		$this->view->aDataSol = $dataInfo;
    		$this->view->aResult  = $sResult;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	    		
    }
}
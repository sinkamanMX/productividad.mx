<?php

class main_MainController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_login');
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
    }

    public function indexAction()
    {
		try{
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	            $this->_redirect('/main/main/inicio');		
			}
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function loginAction(){
		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
	        $answer = Array('answer' => 'no-data');
			$data = $this->_request->getParams();
	        if(isset($data['usuario']) && isset($data['contrasena'])){
	            $usuarios = new My_Model_Usuarios();
				$validate = $usuarios->validateUser($data);            
				if($validate){
					 $dataUser = $usuarios->getDataUser($validate['ID_USUARIO']);
				     $sessions = new My_Controller_Auth();
	                 $sessions->setContentSession($dataUser);
	                 $sessions->startSession();
	                 $usuarios->setLastAccess($dataUser);
				     $answer = Array('answer' => 'logged');
				}else{ 
				    $answer = Array('answer' => 'no-perm'); 
				}
	        }else{
	            $answer = Array('answer' => 'problem');	
	        }
	        echo Zend_Json::encode($answer);   
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function logoutAction(){
		$mysession= new Zend_Session_Namespace('gtpSession');
		$mysession->unsetAll();
		
		Zend_Session::namespaceUnset('gtpSession');
		Zend_Session::destroy();
		
		$this->_redirect('/');
    }  
    
    public function inicioAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();    
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
            $profile = new My_Model_Perfiles();
            $dataUser = $sessions->getContentSession();
            if($dataUser['ID_PERFIL']!="" && $dataUser['ID_PERFIL']!="NULL"){
                $default = $profile->getModuleDefault($dataUser['ID_PERFIL']);	
	            if(count($default)>0){
	            	$this->_redirect($default['SCRIPT']);
	            }else{
	            	$this->_redirect('/main/dashboard/index');	
	            }
            }else{
            	$this->_redirect('/main/main/errorprofile');
            }           
		}		   	
    }
    
    public function recoveryAction(){
    	try{   	
			$data   = $this->_request->getParams();
			$this->view->layout()->setLayout('layout_blank');
			$this->view->data = $data;
		
			if(isset($data['onaction'])){
				$errors = Array();
				
				$validateAlpha	= new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
				
				if(!$validateAlpha->isValid($data['inputPassword'])){
					$errors['passwordPresent'] = 1;
				}
				
				if(!$validateAlpha->isValid($data['inputNewPass'])){
					$errors['passwordNew'] = 1;
				}

				if(!$validateAlpha->isValid($data['inputRepPass'])){
					$errors['passwordRepeat'] = 1;
				}			
				
				if($data['inputNewPass'] != $data['inputRepPass']){
					$errors['passwordRepeat'] = 1;
				}
				
				if(count($errors)==0){
					$this->view->dataUser['VPASSWORD'] = $data['inputPassword'];
					$usuarios = new My_Model_Usuarios();
					$validatePass = $usuarios->validatePassword($this->view->dataUser);
					if(count($validatePass)>0){
						$this->view->dataUser['NPASSWORD'] = $data['inputRepPass'];
						$update = $usuarios->changePass($this->view->dataUser);
						if($update){
							$this->view->changed = 1;		
						}else{
							$errors['noupdate'] = 1;
						}
					}else{
						$errors['noPerm'] = 1;
					}
				}
				
				$this->view->errors = $errors;				
				$this->view->data	= $data;
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function errorprofileAction(){
    	
    }
    
    public function validatemonitorAction(){    	
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			   
			$answer = Array('answer' => 'no-data');
			
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	        	$dataUser = $sessions->getContentSession();
	        	$cMailing = new My_Model_Mailing();
	        	if($dataUser['ID_PERFIL']==1 || $dataUser['ID_PERFIL']==2 || $dataUser['ID_PERFIL']==5 || $dataUser['ID_PERFIL']==3){
	            	$aDataNotif= $cMailing->getNotifications();
	            	if(count($aDataNotif)>0){
	            		$answer = Array('answer' => 'pendings',
	            						'notifs' => $aDataNotif);
	            	}
	        	}else if($dataUser['ID_PERFIL']==19){
	        		$aDataNotif= $cMailing->getNotificationsBroker($dataUser['ID_EMPRESA']);
	            	if(count($aDataNotif)>0){
	            		$answer = Array('answer' => 'pendings',
	            						'notifs' => $aDataNotif);
	            	}
	        	}
			}
			
	        echo Zend_Json::encode($answer);   			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }  

    public function readnotificationAction()
    {
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			$aDataIn = $this->_request->getParams();			
			$cMailing = new My_Model_Mailing();			
			
					
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	        	$dataUser = $sessions->getContentSession();
	        	$cMailing = new My_Model_Mailing();
	        	$sRead = $cMailing->readNotification($aDataIn);	        	
				if($sRead['status']){
					if($dataUser['ID_PERFIL']==1 || $dataUser['ID_PERFIL']==2 || $dataUser['ID_PERFIL']==5 || $dataUser['ID_PERFIL']==3){
						$cSolicitud = new My_Model_Soleasing();
						$aDataInfo  = $cSolicitud->getDataEmp($aDataIn['catId']);
						if($aDataInfo['ID_ORIGEN']==3){
							$this->redirect('/atn/request/getinfoemp?catId='.$aDataIn['catId']);
						}else{
							$this->redirect('/atn/request/getinfo?catId='.$aDataIn['catId']);	
						}							
		        	}else if($dataUser['ID_PERFIL']==19){
						$this->redirect('/leasing/soldates/getinfoemp?catId='.$aDataIn['catId']);
		        	}			
				}	        	

			}			
			
		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }    
}

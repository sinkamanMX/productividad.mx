<?php

class external_LoginController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('contacto_login');
		
		$sessions = new My_Controller_AuthContact();
		if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();
		}			
    }

    public function indexAction()
    {
		$this->view->layout()->setLayout('contacto_login');    	
		try{
			$sessions = new My_Controller_AuthContact();
			if($sessions->validateSession()){
	            $this->_redirect('/external/login/inicio');		
			}
			
			$sessionsArr = new My_Controller_Auth();
	    	if($sessionsArr->validateSession()){
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
	                
	        $answer   = Array('answer' => 'no-data');
			$data 	  = $this->_request->getParams();
			$usuarios = new My_Model_Contactos();
			$cUsuarios= new My_Model_Usuarios();
			
	        if(isset($data['usuario']) && isset($data['contrasena'])){
	        	$validate = $usuarios->validateUser($data); 
	        	$bAnswer  = false;
	        	
	        	if(isset($validate['ID_CONTACTO_QR'])){
					 $dataUser = $usuarios->getDataUser($validate['ID_CONTACTO_QR']);
				     $sessions = new My_Controller_AuthContact();
	                 $sessions->setContentSession($dataUser);
	                 $sessions->startSession();
	                 $usuarios->setLastAccess($dataUser);
				     $answer = Array('answer' => 'logged','source'=>'contact');
				     
				     $bAnswer= true;				     
				}else{
					$bValidateUser = $cUsuarios->validateUserArrendadora($data);  
					if($bValidateUser){
						 $dataUser = $cUsuarios->getDataUser($bValidateUser['ID_USUARIO']);
					     $sessions = new My_Controller_Auth();
		                 $sessions->setContentSession($dataUser);
		                 $sessions->startSession();
		                 $cUsuarios->setLastAccess($dataUser);
					     $answer = Array('answer' => 'logged','source'=>'system');
					     $bAnswer= true;
					}
				}
				
				if(!$bAnswer){
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
		$mysession= new Zend_Session_Namespace('contactSesson');
		$mysession->unsetAll();
		
		Zend_Session::namespaceUnset('contactSesson');
		Zend_Session::destroy();
		
		$this->_redirect('/external/login/index');
    }  
    
    public function inicioAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();    
		
		$sessions = new My_Controller_AuthContact();
        if($sessions->validateSession()){
            $profile = new My_Model_Perfiles();
            $dataUser = $sessions->getContentSession();
            if($dataUser['ID_PERFIL']!="" && $dataUser['ID_PERFIL']!="NULL"){
                $default = $profile->getModuleDefault($dataUser['ID_PERFIL']);	
	            if(count($default)>0){
	            	$this->_redirect($default['SCRIPT']);
	            }else{
	            	$this->_redirect('/login/main/index');	
	            }
            }else{
            	$this->_redirect('/login/main/index');
            }           
		}		   	
    }
    
    /*
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
    */
}

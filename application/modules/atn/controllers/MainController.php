<?php

class atn_MainController extends My_Controller_Action
{	
    public function init()
    {
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
		
		
    }

    public function indexAction()
    {
		try{

			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
}

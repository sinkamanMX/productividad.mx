<?php

class callcenter_NewserviceController extends My_Controller_Action
{	
	protected $_clase = 'mcallcenter';
	public $dataIn;	
	public $aService;
    public $aOptions = Array(
		array("id"=>"1",'name'=> 'Centro de Instalaci&oacuten' ),
		array("id"=>"2",'name'=>'Otro domicilio' )    
    );    	

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
				
			$functions = new My_Controller_Functions();
			$estados   = new My_Model_Estados();
			$aEstados  = $estados->getCbo();
			$aMunicipios=new My_Model_Municipios();
			$aColonias = new My_Model_Colonias();
			 
			$aNamespace = new Zend_Session_Namespace("sService");
			$this->view->estados= $functions->selectDb($aEstados);
			$this->view->genero = $functions->cboGenero();
			$this->view->mismoDomicilio = $functions->cboOptions();
			$this->view->dirDomicilio   = $functions->cbo_from_array($this->aOptions,"1");
			
			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->service)){
					unset($aNamespace->service);
				}
				
				$aNamespace->service = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/instalation');	
			}
			
			if(isset($aNamespace->service)){
				$this->view->data   = $aNamespace->service;
				$this->view->estados= $functions->selectDb($aEstados,$aNamespace->service['inputEstado']);
				$this->view->genero = $functions->cboGenero($aNamespace->service['inputGenero']);
				$this->view->mismoDomicilio = $functions->cboOptions($aNamespace->service['inputDom']);
				
				$dMunicipios = $aMunicipios->getCbo($aNamespace->service['inputEstado']);
				$this->view->municipios = $functions->selectDb($dMunicipios,$aNamespace->service['inputMunicipio']);
				$dColonia    = $aColonias->getCbo($aNamespace->service['inputMunicipio']);
				$this->view->colonias       = $functions->selectDb($dColonia,$aNamespace->service['inputcolonia']);
				$this->view->dirDomicilio   = $functions->cbo_from_array($this->aOptions,$aNamespace->service['inputDirDom']);
			}
			
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function instalationAction(){
		try{
		    $aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service)){
				$this->_redirect('/callcenter/newservice/index');				
			}
			
			$this->view->dataService = $aNamespace->service;
			$cEstados   = new My_Model_Estados();
			$cMunicipios=new My_Model_Municipios();
			$cColonias = new My_Model_Colonias();			

			$estado 	= $cEstados->getData($aNamespace->service['inputEstado']);
			$municipio 	= $cMunicipios->getData($aNamespace->service['inputMunicipio'],$aNamespace->service['inputEstado']);
			$colonia 	= $cColonias->getData($aNamespace->service['inputcolonia'],$aNamespace->service['inputMunicipio']);
			
			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->direction)){
					unset($aNamespace->direction);
				}
				$aNamespace->direction = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/cardetail');	
			}

			if(isset($aNamespace->direction)){
				$this->view->data   = $aNamespace->direction;
			}
			
			if($aNamespace->service['inputDom'] == 1){
				$this->view->direccion = "Mexico,".$estado['NOMBRE'].",".$municipio['NOMBRE'].",".$colonia['NOMBRE'].", CP:".$aNamespace->service['inputCP'].",".$aNamespace->service['inputStreet'];	
			}else{
				if($aNamespace->service['inputDirDom']=="2"){
					$estado 	= $cEstados->getData($aNamespace->service['inputEstadoO']);
					$municipio 	= $cMunicipios->getData($aNamespace->service['inputMunicipioO'],$aNamespace->service['inputEstadoO']);
					$colonia 	= $cColonias->getData($aNamespace->service['inputcoloniaO'],$aNamespace->service['inputMunicipioO']);					
					$this->view->direccion = "Mexico,".$estado['NOMBRE'].",".$municipio['NOMBRE'].",".$colonia['NOMBRE']." CP:,".$aNamespace->service['inputCPO'].",".$aNamespace->service['inputStreetO'];		
				}
			}
			
			$cinstalaciones = new My_Model_Cinstalaciones();
			$this->view->cInstalaciones = $cinstalaciones->getAll($this->view->dataUser['ID_EMPRESA']);	
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
    
    public function cardetailAction(){
		try{
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}

			if(isset($this->dataIn['optReg'])){
				if(isset($aNamespace->carDetail)){
					unset($aNamespace->carDetail);
				}
				$aNamespace->carDetail = $this->dataIn;
	            $this->_redirect('/callcenter/newservice/datefinish');	
			}

			if(isset($aNamespace->carDetail)){
				$this->view->data   = $aNamespace->carDetail;
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	    	
    }
    
    public function datefinishAction(){
    	try{
    		$errors 	= 0;
    		$iCliente 	= 0;
    		$iCita		= 0;
    		$iDomicilio = 0;
    		$iEmpresa   = $this->view->dataUser['ID_EMPRESA'];
    		$iUsuario   = $this->view->dataUser['ID_USUARIO'];
    		
			$aNamespace = new Zend_Session_Namespace("sService");
			if(!isset($aNamespace->service) && !isset($aNamespace->direction)){
				$this->_redirect('/callcenter/newservice/index');				
			}

			if(isset($this->dataIn['optReg'])){
				$aClienteData 	= $aNamespace->service;
				$aInstalacion 	= $aNamespace->direction;
				$aCarDetail	  	= $aNamespace->carDetail;
				
				$cEstados   	= new My_Model_Estados();
				$cMunicipios	= new My_Model_Municipios();
				$cColonias 		= new My_Model_Colonias();						
				$cClientes 		= new My_Model_Clientes();			
				$cCitas			= new My_Model_Citas();

				$estado 	= $cEstados->getData($aClienteData['inputEstado']);
				$municipio 	= $cMunicipios->getData($aClienteData['inputMunicipio'],$aClienteData['inputEstado']);
				$colonia 	= $cColonias->getData($aClienteData['inputcolonia'],$aClienteData['inputMunicipio']);					
				
				$aClienteData['sEstado'] 	= $estado['NOMBRE'];
				$aClienteData['sMunicipio'] = $municipio['NOMBRE'];
				$aClienteData['scolonia'] 	= $colonia['NOMBRE'];

				$aClienteData['sLatitud']	= 0.000000; 
				$aClienteData['sLongitud']	= 0.000000;	
								
				if($aClienteData['inputDom']==1 || $aClienteData['inputDirDom']==2){
					$aClienteData['sLatitud']	= $aInstalacion['inputLatitude'];
					$aClienteData['sLongitud']	= $aInstalacion['inputLongitude'];
				}
				/*
				 * 1.-Se inserta el cliente
				 */

				$insertCliente = $cClientes->insertRow($aClienteData);
				if(!$insertCliente['status']){
					Zend_Debug::dump("error al insertar el cliente");
					$errors++;
				}
				
				$iCliente = $insertCliente['id'];	
				/*
				 * 2.-Se inserta el domicilio del Cliente
				 */
				if($errors==0){			
					$aClienteData['IdCLiente']  = $iCliente;
					
					$insertDireccion = $cClientes->insertDomCliente($aClienteData);
					if(!$insertDireccion['status']){
						Zend_Debug::dump("error al insertar el domicilio del cliente");
						$errors++;
					}	

					$iDomicilio = $insertDireccion['id'];
				}

				/*
				 * 3.-Se inserta la cita
				 */
				if($errors==0){
					$this->dataIn['ID_EMPRESA']  = $iEmpresa;
					$this->dataIn['ID_USUARIO']  = $iUsuario;
					$this->dataIn['idDomicilio'] = $iDomicilio;
					$insertCita = $cCitas->insertRow($this->dataIn);
					if(!$insertCita['status']){
						Zend_Debug::dump("error al insertar la cita");
						$errors++;
					}
					$iCita = $insertCita['id'];						
				}
				
				/*
				 * 4.-Se inserta el domicilio de la cita
				 */	
				if($errors==0){
					$aClienteData['idCita']    = $iCita;
					$aClienteData['idCliente'] = $iCliente;	
									
					if($aClienteData['inputDom']==1){
						$insertaDomCita = $cCitas->insertDomCita($aClienteData);
						if(!$insertaDomCita['status']){
							Zend_Debug::dump("error al insertar el domicilio de la cita.");
							$errors++;
						}							
					}else{
						if($aClienteData['inputDirDom']==2){
							$estado 	= $cEstados->getData($aClienteData['inputEstadoO']);
							$municipio 	= $cMunicipios->getData($aClienteData['inputMunicipioO'],$aClienteData['inputEstadoO']);
							$colonia 	= $cColonias->getData($aClienteData['inputcoloniaO'],$aClienteData['inputMunicipioO']);					
							
							$aClienteData['sEstado'] 	= $estado['NOMBRE'];
							$aClienteData['sMunicipio'] = $municipio['NOMBRE'];
							$aClienteData['scolonia'] 	= $colonia['NOMBRE'];		
												
							$insertaDomCita = $cCitas->insertDomCitaOther($aClienteData);
							if(!$insertaDomCita['status']){
								Zend_Debug::dump("error al insertar el domicilio de la cita.");
								$errors++;
							}								
						}else if($aClienteData['inputDirDom']==1){
							/*Aqui se busca el centro de instalacion y se inserta*/
						}						 
						
					}
				}			

				/*
				 * 5.-Se inserta los valores extra de la cita
				 */
				if($errors==0){
					$aCarDetail['idCita'] = $iCita;
					$insertExtra = $cCitas->insertExtraCitas($aCarDetail);
					if(!$insertExtra){
						Zend_Debug::dump("error al insertar extras de la cita.");
						$errors++;
					}						
				}				

				/*
				 * 6.-Se inserta el formulario para la cita
				 */				
				if($errors==0){
					$aCarDetail['idCita'] = $iCita;
					$insertForm = $cCitas->insertaFormCita($aCarDetail);
					if(!$insertForm){
						Zend_Debug::dump("error al insertar el formulario.");
						$errors++;
					}						
				}	
								
				if($errors==0){
					$aNamespace = new Zend_Session_Namespace("sService");
		
		    		if(isset($aNamespace->service)){
						unset($aNamespace->service);
					}			
		    	    if(isset($aNamespace->direction)){
						unset($aNamespace->direction);
					}			
					if(isset($aNamespace->direction)){
						unset($aNamespace->direction);
					}
		    		if(isset($aNamespace->carDetail)){
						unset($aNamespace->carDetail);
					}					
	            	$this->_redirect('/callcenter/newservice/finish');		
				}else{
					$this->view->error = true;
				}
			}   	

    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	 
    }
    
    public function cancelAction(){
    	try{
			$aNamespace = new Zend_Session_Namespace("sService");

    		if(isset($aNamespace->service)){
				unset($aNamespace->service);
			}			
    	    if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}			
			if(isset($aNamespace->direction)){
				unset($aNamespace->direction);
			}
    		if(isset($aNamespace->carDetail)){
				unset($aNamespace->carDetail);
			}			
			$this->_redirect('/callcenter/newservice/index');				
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	     	
    }
    
    public function finishAction(){
    	
    }    
}
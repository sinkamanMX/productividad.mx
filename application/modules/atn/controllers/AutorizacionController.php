<?php 

class atn_AutorizacionController extends My_Controller_Action
{
	protected $_clase = 'mautorizacion';
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
			$cFunciones		= new My_Controller_Functions();			
			$cCitas			= new My_Model_Citas();

			$dataResume     	= $cCitas->getPendientesbyEmpresa($this->view->dataUser['ID_EMPRESA']);
			$this->view->aResume= $dataResume;			

        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
	public function citadetalleAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			 			
			$cCitas     = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$cUsuarios  = new My_Model_Usuarios();
			
			$dataStatus = $cCitas->getCboStatus();
			$dataUsers  = $cUsuarios->getCbOperadores();
			$dataDate = Array();
			$dataStat = '';
			$opAsign  = '';
			$statusOpr = false;
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$iIdCita	= $this->dataIn['strInput'];					
				if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=="opSearch"){
					$sRandomNumber = $this->getCodeValidate();					
					$this->dataIn['codeValidate']= $sRandomNumber;
					$this->dataIn['ID_USUARIO']  = $this->view->dataUser['ID_USUARIO'];
							
					$bDataUpdate  = $cCitas->validateDate($this->dataIn);
					if($bDataUpdate){
						$dataDate   = $cCitas->getCitasDet($iIdCita);
						
						$cHttpService = new Zend_Http_Client();
						$sUrl = "http://192.168.6.116/siames/sap_update_monitoreo.php?folio=".$dataDate['FOLIO'];
						$cHttpService->setUri($sUrl);
						$response = $cHttpService->request();

						$cHtmlMail		= new My_Controller_Htmlmailing();							
						$cHtmlMail->notif_autorizacion($dataDate,$this->view->dataUser);
						
						$statusOpr = true;				
					}
				}				
				$dataDate   = $cCitas->getCitasDet($iIdCita);
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus,$dataStat);
			$this->view->personal = $funtions->selectDb($dataUsers,$opAsign);
			$this->view->data     = $dataDate;
			$this->view->statusOpr= $statusOpr;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	}    
	
	public function getCodeValidate(){
		$sCodeRandom = ''; 
		$cCitas     = new My_Model_Citas();
		$funtions   = new My_Controller_Functions();
		$sRandomNumber = $funtions->getRandomCode();
		
		$validateRandom = $cCitas->getValidFolioAut($sRandomNumber,$this->view->dataUser['ID_EMPRESA']);
		if($validateRandom){
			$sCodeRandom = $this->getCodeValidate();
		}else{
			$sCodeRandom = $sRandomNumber;
		}
		
		return $sCodeRandom;
	}
	/*
	 * 1.- Que tenga folio de sap
	 * 2.- Que este terminada
	 * 3.- Que las fotos existan.
	 * 4.- Copiar a carpeta 
	 * 
	 * agregar en tabla de citas campo que diga enviado_sap
	 * 
	 * */
	
	public function updateServices($codFolio){
    	try{   			    

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
	}
	
	public function formvalidationAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			
			$cFormularios = new My_Model_Formularios();			 			
			$cCitas     = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$cUsuarios  = new My_Model_Usuarios();
			$cResultados= new My_Model_Formresult();
			
			$dataStatus = $cCitas->getCboStatus();
			$dataUsers  = $cUsuarios->getCbOperadores();
			$dataDate = Array();
			$dataStat = '';
			$opAsign  = '';
			$statusOpr = false;
			$aElementos= array();
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$iIdCita	= $this->dataIn['strInput'];		
				$dataDate   = $cCitas->getCitasDet($iIdCita);
				$idFormulario= 14;
				$aElementos	= $cFormularios->getElementosResult($idFormulario);
				
				if(@$this->dataIn['optReg']=='sendForm'){
					$this->dataIn['inputUser'] = $this->view->dataUser['ID_USUARIO'];
					$this->dataIn['inputFechaFin'] = Date("Y-m-d H:i:s");
					
					//1.-PROD_FORM_RESULTADO
					$bInsert = $cResultados->insertaRespuestas($this->dataIn);
					if($bInsert['status']){
						$idResultado = $bInsert['id'];
						$this->dataIn['idResultado'] = $idResultado;
						//2.-update_cita
						$bUpdateCita = $cResultados->updateCita($this->dataIn);
						if($bUpdateCita['status']){
							//3.-inserta_valores_respuesta
							$bInsertResults = $this->processResult($aElementos,$this->dataIn); 		
							if($bInsertResults){
								
								$sRandomNumber = $this->getCodeValidate();					
								$this->dataIn['codeValidate']= $sRandomNumber;
								$this->dataIn['ID_USUARIO']  = $this->view->dataUser['ID_USUARIO'];
										
								$bDataUpdate  = $cCitas->validateDate($this->dataIn);
								if($bDataUpdate){
									$this->redirect('/atn/autorizacion/citadetalle?strInput='.$this->dataIn['strInput']);
																		
									/*
									$dataDate   = $cCitas->getCitasDet($iIdCita);
									
									$cHttpService = new Zend_Http_Client();
									$sUrl = "http://192.168.6.116/siames/sap_update_monitoreo.php?folio=".$dataDate['FOLIO'];
									$cHttpService->setUri($sUrl);
									$response = $cHttpService->request();
			
									$cHtmlMail		= new My_Controller_Htmlmailing();							
									$cHtmlMail->notif_autorizacion($dataDate,$this->view->dataUser);
									
									$statusOpr = true;	*/
												
								}else{
									$this->aErrors['iError'] = 'noinRespuestas';
								}
							}else{
								$this->aErrors['iError'] = 'noinRespuestas';
							}
						}else{
							$this->aErrors['iError'] = 'noUpdateCita';	
						}
					}else{
						$this->aErrors['iError'] = 'noInsertResp';
					}
				}
			}
			
			$this->view->idFormulario = $idFormulario;
			$this->view->fechaInicio= Date("Y-m-d H:i:s"); 
			$this->view->aElements	= $this->processFields($aElementos);					
			$this->view->data       = $dataDate;
			$this->view->statusOpr  = $statusOpr;			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
	}
	
	public function processResult($aElementos,$dataIn){
		$aResultValidate = false;				
		$mFunctions 	 = new My_Controller_Functions();
		$cResultados     = new My_Model_Formresult();
		$iTotalVal       = 0;
		$iTotalok		 = 0;
		
		foreach($aElementos as $key => $items){
			$inputName 	= 'input_'.$items['ID_ELEMENTO'];			
			if($items['ID_TIPO']=='3' || $items['ID_TIPO']=='2'){
				$iTotalVal++;
				
				$sValueInput = @$dataIn[$inputName];
				$bInsert = $cResultados->inserRespuesta($dataIn['idResultado'],$items['ID_ELEMENTO'],$sValueInput);
				if($bInsert){
					$iTotalok++;
				}
			}			
		}
		
		if($iTotalok == $iTotalVal){
			$aResultValidate = true;
		}
		
		return $aResultValidate;
	}		
	
	public function processFields($aElements){
		$cFunctions 	= new My_Controller_Functions();
		$cFormularios 	= new My_Model_Formularios();
		$cTipos			= new My_Model_TipoFormularios();
		$aShowon	  = Array(array('id'=>'1','name'=>'Dispositivo'),
							  array('id'=>'0','name'=>'Web'),
							  array('id'=>'2','name'=>'Ambos'));		
		$aResult = Array();
				
		foreach($aElements as $key => $items){
			$inputName = 'input_'.$items['ID_ELEMENTO'];
			if($items['ID_TIPO']=='0' || $items['ID_TIPO']=='1'){
				
			}else if($items['ID_TIPO']=='2'){
				$items['INPUT'] = '<input name="'.$inputName.'" id="'.$inputName.'" type="text" class="span6 m-wrap" value="" >';
			}else if($items['ID_TIPO']=='3'){
				$aValues = explode(",",$items['VALORES_CONFIG']);
				$items['INPUT']	 = '<select class="span6 m-wrap" name="'.$inputName.'" id="'.$inputName.'" >
												<option value="">Selecciona una opci&oacute;n</option>';
				for($i=0;$i<count($aValues);$i++){
					$items['INPUT'] .= '<option value="'.$aValues[$i].'" >'.$aValues[$i].'</option>';	
				}				
				$items['INPUT']	.= '</select>';							
			}else if($items['ID_TIPO']=='4'){

			}else if($items['ID_TIPO']=='5'){				
				
			}else if($items['ID_TIPO']=='6'){

			}else if($items['ID_TIPO']=='7'){

			}else if($items['ID_TIPO']=='8'){
				//$items['INPUT'] = $items['N_ELEMENTO'];
			}else if($items['ID_TIPO']=='9'){
			
			}else if($items['ID_TIPO']=='10'){

			}else if($items['ID_TIPO']=='11'){

			}else if($items['ID_TIPO']=='12'){
				
			}else if($items['ID_TIPO']=='13'){

			}else if($items['ID_TIPO']=='14'){				
				
			}
			
			$aResult[] = $items;
		}
		
		return $aResult;		
	}		
}
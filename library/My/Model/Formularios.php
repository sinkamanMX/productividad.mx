<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Formularios extends My_Db_Table
{
	protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'PROD_FORMULARIO';
	protected $_primary = 'ID_FORMULARIO';

	
	public function getAdminTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		
    	$sql ="SELECT C.RAZON_SOCIAL AS N_CLIENTE, F.*
				FROM PROD_FORMULARIO F
				LEFT JOIN PROD_CLIENTES C ON F.ID_CLIENTE = C.ID_CLIENTE
				ORDER BY DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	/**
	 * 
	 * Devuelve la informacion de un unformulario.
	 * @param int $idObject
	 */
	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT F.*,I.*, IF(F.ID_ICONO IS NOT NULL,I.NOMBRE_IMAGEN,'0') AS ASIGNADO, 
    				F.DESCRIPCION AS N_DESC, F.ID_OVISION
				FROM $this->_name F
				LEFT JOIN  PROD_ICONOS I ON F.ID_ICONO = I.ID_ICONO
				WHERE F.$this->_primary = $idObject LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	/**
	 * 
	 * * Se valida que no existe el mismo titulo.
	 * @param String $stringSearch
	 * @param String $idObject
	 * @param String $idEmpresa
	 * @return Array Resultado del query
	 */	
	public function validateDataBy($stringSearch="", $idObject="",$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		
    	$sql ="SELECT *
				FROM $this->_name
				WHERE 	TITULO 	   = '".$stringSearch."'
				  AND   ID_EMPRESA = $idEmpresa  
				  AND   $this->_primary <> $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	/**
	 * 
	 * Inserta un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sFilter  = (isset($aDataIn['inputIdAssign']) && $aDataIn['inputIdAssign'] !="" ) ? 'ID_ICONO = '.$aDataIn['inputIdAssign'].', ' : '';
        $sFilter  .= (isset($aDataIn['inputIdOvision']) && $aDataIn['inputIdOvision'] !="" ) ? 'ID_OVISION		=  "'.$aDataIn['inputIdOvision'].'", ' : '';
        $sFilter  .= (isset($aDataIn['inputLength']) && $aDataIn['inputLength'] !="" ) ? ' CARACTERES_CAMPO=  '.$aDataIn['inputLength'].', ' : '';
        $sFilter  .= (isset($aDataIn['inputCliente']) && $aDataIn['inputCliente'] !="-1" ) ? ' ID_CLIENTE =  '.$aDataIn['inputCliente'].', ' : ' ID_CLIENTE = NULL,';
        
        $sql="INSERT INTO $this->_name			 
					SET ID_EMPRESA		=  ".$aDataIn['inputEmpresa'].",
						TITULO			= '".$aDataIn['inputTitulo']."',
						DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
						ORDEN			= '".$aDataIn['inputOrden']."',
						ID_USUARIO_CREO	= ".$aDataIn['userRegister'].",
						ID_USUARIO_MODIFICO= ".$aDataIn['userRegister'].",
						FECHA_CREACION	= CURRENT_TIMESTAMP,
						$sFilter
						TIPO_FORMULARIO	= '".$aDataIn['inputTipo']."',
						ACTIVO			= '".$aDataIn['inputEstatus']."'";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }
    
	/**
	 * 
	 * Actualiza un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Boolean Estatus de la operacion
	 */
    public function updateRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sFilter  = (isset($aDataIn['inputIdAssign']) && $aDataIn['inputIdAssign'] !="" ) ? 'ID_ICONO = '.$aDataIn['inputIdAssign'].', ' : '';
		$sFilter  .= (isset($aDataIn['inputIdOvision']) && $aDataIn['inputIdOvision'] !="" ) ? 'ID_OVISION		=  "'.$aDataIn['inputIdOvision'].'", ' : '';
		$sFilter  .= (isset($aDataIn['inputLength']) && $aDataIn['inputLength'] !="" ) ? ' CARACTERES_CAMPO=  '.$aDataIn['inputLength'].', ' : '';
		$sFilter  .= (isset($aDataIn['inputCliente']) && $aDataIn['inputCliente'] !="-1" ) ? ' ID_CLIENTE =  '.$aDataIn['inputCliente'].', ' : ' ID_CLIENTE = NULL,';
        $sql="UPDATE $this->_name			 
				SET TITULO			= '".$aDataIn['inputTitulo']."',
					DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
					ORDEN			= '".$aDataIn['inputOrden']."',
					ID_USUARIO_MODIFICO	= ".$aDataIn['userRegister'].",
					FECHA_MODIFICACION	= CURRENT_TIMESTAMP,
					$sFilter
					TIPO_FORMULARIO	= '".$aDataIn['inputTipo']."',
					ACTIVO			= '".$aDataIn['inputEstatus']."'
				WHERE $this->_primary =".$aDataIn['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }  

	/**
	 * 
	 * Obtiene los elementos de un formulario
	 * @param int $idObject
	 * @param int $idEmpresa
	 */
    public function getElementos($idObject,$idEmpresa){
    	$result     = Array();    	
    	try{ 
    		$sql = "SELECT R.ID_ELEMENTO, R.ORDEN, E.DESCIPCION AS N_ELEMENTO, E.ACTIVO,E.VALORES_CONFIG, E.REQUERIDO, T.`DESCRIPCION` AS TIPO, E.`DEPENDE`, E.`ESPERA`, E.`VALIDAR_LOCAL`,
    				E.ID_TIPO, E.ON_DEVICE
					FROM PROD_FORMULARIO_ELEMENTOS R
					INNER JOIN PROD_ELEMENTOS 	   E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					INNER JOIN PROD_TPO_ELEMENTO   T ON E.`ID_TIPO`   = T.ID_TIPO
					WHERE ID_FORMULARIO = $idObject
					  AND ID_EMPRESA    = $idEmpresa
					ORDER BY R.ORDEN ASC";    		
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query;			
			}	
	        
			return $result;			
    	}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }
    
	/**
	 * 
	 * Inserta un nuevo elemento y lo relaciona con un formulario
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertElement($aDataElement,$idObject,$idEmpresa){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_ELEMENTOS
				SET ID_TIPO			= ".$aDataElement['tipo'].",
					DESCIPCION		='".@$aDataElement['desc']."',
					ACTIVO			='".@$aDataElement['status']."',
					VALORES_CONFIG	='".@$aDataElement['options']."',
					REQUERIDO		='".@$aDataElement['requerido']."',
					VALIDAR_LOCAL	='".@$aDataElement['validacion']."',
					DEPENDE			= ".(($aDataElement['depend']=="") ? 'NULL': $aDataElement['depend']).",
					ESPERA 			='".$aDataElement['when']."',
					ON_DEVICE		='".@$aDataElement['showon']."'";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$sqlRel = "INSERT INTO PROD_FORMULARIO_ELEMENTOS
							SET ID_FORMULARIO	= ".$idObject.",
								ID_EMPRESA		= ".$idEmpresa.",
								ID_ELEMENTO		= ".$query_id[0]['ID_LAST'].",
								ORDEN			= ".$aDataElement['orden'];
				$queryRel   = $this->query($sqlRel,false);
				if(count($queryRel)>0){
					$result['status']  = true;		
				}
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }   

    /**
     * 
     * Elimina un elemento de un formulario
     * @param Array $aDataElement
     * @param int $idObject
     */
    public function deleteRowRel($aDataElement,$idObject){
        $result     = Array();
        $result['status']  = false;  
        
		$sqlDel  = "DELETE FROM PROD_FORMULARIO_ELEMENTOS 
					WHERE ID_ELEMENTO   = ".$aDataElement['id']."
					  AND ID_FORMULARIO = ".$idObject." LIMIT 1";
	    $queryDel   = $this->query($sqlDel,false);    

        $sql="DELETE FROM  PROD_ELEMENTOS
					 WHERE ID_ELEMENTO = ".$aDataElement['id']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	     	
    }    

	/**
	 * 
	 * Actualiza un elemento del formulario
	 * @param Array $aDataIn
	 * @return Boolean Estatus de la operacion
	 */
    public function updateRowRel($aDataElement){
        $result     = Array();
        $result['status']  = false;
        
       $sql="UPDATE PROD_ELEMENTOS
				SET ID_TIPO			= ".$aDataElement['tipo'].",
					DESCIPCION		='".@$aDataElement['desc']."',
					ACTIVO			='".@$aDataElement['status']."',
					VALORES_CONFIG	='".@$aDataElement['options']."',
					REQUERIDO		='".@$aDataElement['requerido']."',
					VALIDAR_LOCAL	='".@$aDataElement['validacion']."',
					DEPENDE			= ".(($aDataElement['depend']=="") ? 'NULL': $aDataElement['depend']).",
					ESPERA 			='".$aDataElement['when']."',
					ON_DEVICE		='".@$aDataElement['showon']."'
			WHERE ID_ELEMENTO = ".$aDataElement['id']." LIMIT 1";				        
		try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }     

    public function deleteRelAction($data){
    	try{    	
       		$result     = Array();
        	$result['status']  = false;
        
			$sql  	= "UPDATE PROD_FORMULARIO SET ID_ICONO = NULL WHERE ID_FORMULARIO = ".$data['catId']." LIMIT 1";
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	         	
    }  

	/**
	 * 
	 * Devuelve la informacion de un unformulario.
	 * @param int $idObject
	 */
	public function getDataByClient($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT IF(F.ID_CLIENTE IS NULL,0,1) AS ASIGNADO, F.TITULO, F.DESCRIPCION,F.ID_OVISION, F.FECHA_CREACION, I.NOMBRE_IMAGEN AS N_IMAGEN, F.ID_FORMULARIO AS ID
				FROM PROD_FORMULARIO F
				LEFT JOIN  PROD_ICONOS I ON F.ID_ICONO = I.ID_ICONO
				WHERE F.TIPO_FORMULARIO = 'M'
				AND (F.ID_CLIENTE IS NULL OR F.ID_CLIENTE = ".$idObject.")
				ORDER BY ASIGNADO DESC, TITULO";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	} 

	/**
	 * 
	 * Actualiza un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Boolean Estatus de la operacion
	 */
    public function registerupdate($idObject,$idUser){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE $this->_name			 
				SET ID_USUARIO_MODIFICO	= ".$idUser.",
					FECHA_MODIFICACION	= CURRENT_TIMESTAMP					
				WHERE $this->_primary =".$idObject." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }  

	/**
	 * 
	 * Obtiene los elementos de un formulario
	 * @param int $idObject
	 * @param int $idEmpresa
	 */
    public function getElementosResult($idObject){
    	$result     = Array();    	
    	try{ 
    		$sql = "SELECT R.ID_ELEMENTO, R.ORDEN, E.DESCIPCION AS N_ELEMENTO, E.ACTIVO,E.VALORES_CONFIG, E.REQUERIDO, T.`DESCRIPCION` AS TIPO, E.`DEPENDE`, E.`ESPERA`, E.`VALIDAR_LOCAL`,
    				E.ID_TIPO, E.ON_DEVICE
					FROM PROD_FORMULARIO_ELEMENTOS R
					INNER JOIN PROD_ELEMENTOS 	   E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					INNER JOIN PROD_TPO_ELEMENTO   T ON E.`ID_TIPO`   = T.ID_TIPO
					WHERE ID_FORMULARIO = $idObject
					  AND E.ON_DEVICE IN (0,2)		
					  AND ACTIVO = 'S'			
					ORDER BY R.ORDEN ASC";    		
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query;			
			}	
	        
			return $result;			
    	}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }    
}
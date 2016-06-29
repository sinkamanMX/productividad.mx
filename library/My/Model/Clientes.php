<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Clientes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CLIENTES';
	protected $_primary = 'ID_CLIENTE';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET  NOMBRE			= '".$data['inputNombre']."',
					APELLIDOS		= '".$data['inputApps']."',
					TELEFONO_FIJO	= '".$data['inputTel']."',
					TELEFONO_MOVIL	= '".$data['inputCel']."',
					EMAIL			= '".$data['inputEmail']."',
					RFC				= '".$data['inputRFC']."',
					RAZON_SOCIAL	= '".$data['inputRazon']."',
					COD_CLIENTE 	= '".$data['inputClave']."',
					TIPO_PERSONA	= '".$data['inputTipo']."',
					GENERO			= '".$data['inputGenero']."'";
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
	
	public function insertDomCliente($data){
        $result     = Array();
        $result['status']  = false;
        
        
        $sql="INSERT INTO PROD_DOMICILIOS_CLIENTE
				SET  ID_CLIENTE	= ".$data['IdCLiente'].",
					ESTADO		= '".$data['sEstado']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					COLONIA		= '".$data['scolonia']."',
					CALLE		= '".$data['inputStreet']."',
					CP			= '".$data['inputCP']."',
					NUMERO_EXT	= '".$data['inputNoExt']."',
					NUMERO_INT	= '".$data['inputNoInt']."',
					REFERENCIAS = '".$data['inputRefs']."', 
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
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
	
	function getData($codCliente){
		$filter = '';
		$result= Array();
		$this->query("SET NAMES utf8",false);		
    	$sql ="SELECT  *
    			FROM $this->_name
				WHERE COD_CLIENTE = '".$codCliente."'";  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	
	function getDataTable($idObject){
	    $this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT C.*,C.ID_CLIENTE AS ID, IF(T.ID_MENSAJE IS NULL,'0','1') AS selected
				FROM PROD_CLIENTES C
				LEFT JOIN MENSAJE_DISPOSITIVOS T ON C.ID_CLIENTE  = T.ID_CLIENTE AND T.ID_MENSAJE = ".$idObject."
				WHERE C.COD_CLIENTE != '' && C.COD_CLIENTE LIKE 'CL%'
				ORDER BY C.COD_CLIENTE ASC";		         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}
        
		return $result;	        
    }
    
	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, RAZON_SOCIAL AS NAME 
    			FROM $this->_name     			
    			ORDER BY RAZON_SOCIAL  ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}    
}
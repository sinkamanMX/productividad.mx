<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Unidades extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_UNIDADES';
	protected $_primary = 'ID_UNIDAD';
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, IDENTIFICADOR AS NAME 
    			FROM $this->_name 
    			WHERE ID_CLIENTE = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
    public function validateUnitByPlaque($sPlaque){
		try{     	        	
			$result= false;
			$this->query("SET NAMES utf8",false); 
	    	$sql ="SELECT  *
	                FROM $this->_name
	                WHERE PLACAS = '$sPlaque' LIMIT 1";	
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = true;			
			}
		}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }        
		return $result;	    	
    }	
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
			  SET ID_CLIENTE		= ".$data['inputIdCliente']." ,
			  		ECONOMICO		='".$data['inputEco']."', 
			  		PLACAS			='".$data['inputPlacas']."',
			  		IDENTIFICADOR	='".$data['inputIden']."',
			  		IDENTIFICADOR_2	='".$data['inputIden2']."',
			  		TIPO_VEHICULO	='".$data['inputVehiculo']."',
			  		TIPO_EQUIPO		='".$data['inputEquipo']."',
			  		REGISTRADO		= CURRENT_TIMESTAMP";
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

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT U.*,C.*
                FROM $this->_name U
                 LEFT JOIN PROD_CLIENTES C ON U.ID_CLIENTE = C.ID_CLIENTE 
                WHERE U.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }  

	public function getUnidades($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idObject
				GROUP BY $this->_primary";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}        

    public function insertNewRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
			  SET   ID_EMPRESA		= ".$data['idEmpresa']." ,
			  		ID_CLIENTE		= -1 ,
			  		ECONOMICO		='".$data['inputEco']."', 
			  		PLACAS			='".$data['inputPlacas']."',
			  		IDENTIFICADOR	='".$data['inputIden']."',
			  		IDENTIFICADOR_2	='".$data['inputIden2']."',
			  		TIPO_VEHICULO	='".$data['inputVehiculo']."',
			  		TIPO_EQUIPO		='".$data['inputEquipo']."',
			  		REGISTRADO		= CURRENT_TIMESTAMP";
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
    
    public function updateRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
 				SET ECONOMICO		='".$data['inputEco']."', 
			  		PLACAS			='".$data['inputPlacas']."',
			  		IDENTIFICADOR	='".$data['inputIden']."',
			  		IDENTIFICADOR_2	='".$data['inputIden2']."',
			  		TIPO_VEHICULO	='".$data['inputVehiculo']."',
			  		TIPO_EQUIPO		='".$data['inputEquipo']."'
				WHERE $this->_primary =".$data['catId']." LIMIT 1";
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

    public function deleteRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  $this->_name
					 WHERE $this->_primary = ".$data['catId']."
					  AND  ID_EMPRESA	   = ".$data['idEmpresa']."  LIMIT 1";
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

	public function getCbobyEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, IDENTIFICADOR AS NAME 
    			FROM $this->_name 
    			WHERE ID_EMPRESA = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}    
}
<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Lugares extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_LUGARES';
	protected $_primary = 'ID_LUGAR';
	
	public function getCbobyEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			WHERE ID_EMPRESA = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	} 	
	
	public function getRowsEmp($idObject){
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
	
	/*
	public function getFilterSucursales($description,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT ID_CLIENTE AS ID
				FROM GTP_CLIENTES
				WHERE ID_SUCURSAL 
				IN (
					SELECT ID_LUGAR
					FROM PROD_LUGARES 
					WHERE DESCRIPCION LIKE '%".$description."%' AND ID_EMPRESA = ".$idEmpresa."
				)";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;   		
	}	*/
	
    public function insertRowRegister($data){
        $result     = Array();
        $result['status']  = false;    

        $sql="INSERT INTO $this->_name	
        		SET	ID_EMPRESA 		=  ".$data['inputIdEmpresa'].",
        			DESCRIPCION		= '".$data['inputDescripcion']."',
        			FECHA_REGISTRO	= CURRENT_TIMESTAMP";       			  
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']  = $query_id[0]['ID_LAST'];  			 	
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
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }    
    

    public function insertRow($aData){
        $result     = Array();
        $result['status']  = false;

        $sql="INSERT INTO  $this->_name
				SET ID_EMPRESA 	=  ".$aData['inputEmpresa'].",
        			DESCRIPCION 	= '".$aData['inputDescripcion']."',        			
        			CALLE			= '".$aData['inputCalle']."',    
        			COLONIA 		= '".$aData['inputColonia']."',    
        			MUNICIPIO		= '".$aData['inputMunicipio']."',    
        			ESTADO			= '".$aData['inputEstado']."',    
        			CP				= '".$aData['inputCP']."',
        			ESTATUS			=  ".$aData['inputEstatus'].",
					FECHA_CREADO    = CURRENT_TIMESTAMP";
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
            echo $sql;
        }
		return $result;	       	
    }      
    
    public function updateRow($aData,$idObject){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE  $this->_name
        		SET	DESCRIPCION 	= '".$aData['inputDescripcion']."',        			
        			CALLE			= '".$aData['inputCalle']."',    
        			COLONIA 		= '".$aData['inputColonia']."',    
        			MUNICIPIO		= '".$aData['inputMunicipio']."',    
        			ESTADO			= '".$aData['inputEstado']."',    
        			CP				= '".$aData['inputCP']."',
        			ESTATUS			=  ".$aData['inputEstatus']."
				WHERE $this->_primary   = ".$idObject;
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    }       
    
    public function deleteRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  $this->_name
					 WHERE $this->_primary = ".$data['catId']." LIMIT 1";
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

	public function updateRowLeasing($aData,$idObject){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE  $this->_name
        		SET	DESCRIPCION 	= '".$aData['inputDescripcion']."',        			
        			CALLE			= '".$aData['inputCalle']."',
        			ENTRE_CALLES	= '".$aData['inputEntreCalles']."',    
        			REFERENCIAS		= '".$aData['inputRefs']."',    
        			CONTACTO		= '".$aData['inputContacto']."',    
        			CONTACTO_TEL	= '".$aData['inputTelCont']."',     
        			COLONIA 		= '".$aData['inputColonia']."',    
        			MUNICIPIO		= '".$aData['inputMunicipio']."',    
        			ESTADO			= '".$aData['inputEstado']."',    
        			CP				= '".$aData['inputCP']."',
        			EMAIL_CONTACTO	= '".$aData['inputMail']."',
        			ID_EMP_CLIENTE	=  ".$aData['inputCliente'].",        			
        			ESTATUS			=  ".$aData['inputEstatus']."
				WHERE $this->_primary   = ".$idObject;
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    }  
    
    public function insertRowLeasing($aData){
        $result     = Array();
        $result['status']  = false;

        $sql="INSERT INTO  $this->_name
				SET ID_EMPRESA 	=  ".$aData['inputEmpresa'].",
        			DESCRIPCION 	= '".$aData['inputDescripcion']."',        			
        			CALLE			= '".$aData['inputCalle']."',    
        			ENTRE_CALLES	= '".$aData['inputEntreCalles']."',    
        			REFERENCIAS		= '".$aData['inputRefs']."',    
        			CONTACTO		= '".$aData['inputContacto']."',    
        			CONTACTO_TEL	= '".$aData['inputTelCont']."',    
        			COLONIA 		= '".$aData['inputColonia']."',    
        			MUNICIPIO		= '".$aData['inputMunicipio']."',    
        			ESTADO			= '".$aData['inputEstado']."',    
        			CP				= '".$aData['inputCP']."',
        			ESTATUS			=  ".$aData['inputEstatus'].",
        			EMAIL_CONTACTO	= '".$aData['inputMail']."',
        			ID_EMP_CLIENTE	=  ".$aData['inputCliente'].",
        			ID_SUCURSAL		=  ".$aData['inputSucursal'].",
					FECHA_CREADO    = CURRENT_TIMESTAMP";
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
            echo $sql;
        }
		return $result;	       	
    }   

    public function getDataTable($idObject,$idSucursal){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($idSucursal!=-1) ? ' AND C.ID_SUCURSAL = '.$idSucursal: '';
    	$sql ="SELECT S.*, C.NOMBRE AS N_CLIENTE,U.DESCRIPCION AS N_SUCURSAL
				FROM PROD_LUGARES S
				INNER JOIN SUCURSALES 	U ON S.ID_SUCURSAL    = U.ID_SUCURSAL
				INNER JOIN EMP_CLIENTES C ON S.ID_EMP_CLIENTE = C.ID_EMP_CLIENTE
				WHERE S.ID_EMPRESA = $idObject
				$sFilter
				ORDER BY S.DESCRIPCION";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;
		}
        
		return $result;    	
    }
    
	public function getCbobyClient($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			WHERE ID_EMP_CLIENTE = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}     
}
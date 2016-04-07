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
    	$sql ="SELECT U.*,C.*, M.DESCRIPCION AS N_MODELO, E.DESCRIPCION AS N_MARCA, L.DESCRIPCION AS N_COLOR
                FROM PROD_UNIDADES U
                 LEFT JOIN AVL_COLORES           L ON U.ID_COLOR   = L.ID_COLOR                
                 LEFT JOIN PROD_CLIENTES 		 C ON U.ID_CLIENTE = C.ID_CLIENTE 
                 LEFT JOIN AVL_MODELO_ACTIVO     M ON U.ID_MODELO  = M.ID_MODELO
                 LEFT JOIN AVL_MARCA_ACTIVO 	 E ON M.ID_MARCA   = E.ID_MARCA
                WHERE U.ID_UNIDAD = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }  

	public function getUnidades($idObject,$idSucursal=-1){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($idSucursal!=-1) ? ' AND U.ID_SUCURSAL = '.$idSucursal: '';		 		
    	$sql ="SELECT U.*, C.NOMBRE  AS N_CLIENTE,S.DESCRIPCION AS N_SUCURSAL
				FROM PROD_UNIDADES U
				LEFT JOIN SUCURSALES 	S ON S.ID_SUCURSAL    = U.ID_SUCURSAL
				INNER JOIN EMP_CLIENTES C ON U.ID_EMP_CLIENTE = C.ID_EMP_CLIENTE
				WHERE U.ID_EMPRESA = $idObject
				$sFilter
				ORDER BY U.PLACAS ASC";    	
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
	
    public function updateRowLeasing($data){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
 				SET ID_EMP_CLIENTE	= ".$data['inputCliente'].",
 					ECONOMICO		='".$data['inputEco']."', 
 					ID_MODELO		= ".$data['inputModelo'].",
			  		PLACAS			='".$data['inputPlacas']."',
			  		IDENTIFICADOR	='".$data['inputIden']."',
			  		IDENTIFICADOR_2	='".$data['inputIden2']."',
			  		ANIO			='".$data['inputAnio']."',
			  		ID_COLOR		= ".$data['inputColor']."			  		
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

    public function insertNewRowLeasing($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
			  SET   ID_EMPRESA		= ".$data['idEmpresa']." ,
			  		ID_CLIENTE		= -1 ,
			  		ID_EMP_CLIENTE	= ".$data['inputCliente'].",
			  		ID_MODELO		= ".$data['inputModelo'].",
			  		ECONOMICO		='".@$data['inputEco']."', 
			  		PLACAS			='".$data['inputPlacas']."',
			  		IDENTIFICADOR	='".@$data['inputIden']."',
			  		IDENTIFICADOR_2	='".$data['inputIden2']."',
			  		ANIO			='".$data['inputAnio']."',
			  		ID_COLOR		= ".$data['inputColor'].",
			  		ID_SUCURSAL		= ".$data['inputSucursal'].",
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

    public function getDataLeasing($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT U.* , M.ID_MARCA             
				FROM PROD_UNIDADES U
				LEFT JOIN AVL_MODELO_ACTIVO M ON U.ID_MODELO = M.ID_MODELO
				WHERE U.ID_UNIDAD = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }  

	public function getCbobyEmpLe($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_UNIDAD AS ID, IDENTIFICADOR AS NAME, M.DESCRIPCION AS N_MODELO, R.DESCRIPCION AS N_MARCA,
				U.PLACAS,U.IDENTIFICADOR,U.ECONOMICO
				FROM PROD_UNIDADES  U
				INNER JOIN AVL_MODELO_ACTIVO M ON U.ID_MODELO = M.ID_MODELO
				INNER JOIN AVL_MARCA_ACTIVO  R ON M.ID_MARCA  = R.ID_MARCA
				LEFT JOIN PROD_CITAS_SOLICITUD S ON U.ID_UNIDAD = S.ID_UNIDAD AND S.ID_ESTATUS IN(1,2,5)
				WHERE S.ID_UNIDAD IS NULL
				  AND U.ID_EMPRESA = $idObject
				  ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}      
	
	public function getCboByEmpCliente($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, CONCAT(IDENTIFICADOR_2,' (',PLACAS,')') AS NAME 
    			FROM $this->_name 
    			WHERE ID_EMP_CLIENTE = $idObject ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
}
<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Clientesint extends My_Db_Table
{
    protected $_schema 	= 'taccsi';
	protected $_name 	= 'EMP_CLIENTES';
	protected $_primary = 'ID_EMP_CLIENTE';
	
	public function getDataTables($idCliente,$iSucursal=-1){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($iSucursal>-1) ? ' AND C.ID_SUCURSAL = '.$iSucursal: '';
    	$sql ="SELECT C.*, S.DESCRIPCION AS N_SUCURSAL
				FROM EMP_CLIENTES C
				INNER JOIN SUCURSALES S ON C.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE C.ID_EMPRESA = ".$idCliente."
				$sFilter
				ORDER BY C.NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
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

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name	
        		SET	ID_EMPRESA		=  ".$data['inputEmpresa'].",
				  	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
				  	ESTATUS			=  ".$data['inputEstatus'].",
				  	ID_SUCURSAL		=  ".$data['inputSucursal'].",
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
	
    public function updateRow($data,$idObject){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE  $this->_name
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
				  	ESTATUS			=  ".$data['inputEstatus']."
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
    
	public function getCbo($idEmpresa,$iSucursal=-1){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($iSucursal>-1) ? ' AND ID_SUCURSAL = '.$iSucursal : '';		
    	$sql ="SELECT ID_EMP_CLIENTE AS ID, NOMBRE AS NAME
				FROM $this->_name
				WHERE ID_EMPRESA = ".$idEmpresa."
				$sFilter
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}	
}
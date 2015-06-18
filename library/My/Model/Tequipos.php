<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Tequipos extends My_Db_Table
{
    protected $_schema 	= 'DB_SIAMES';
	protected $_name 	= 'EQUIPOS_UDA';
	protected $_primary = 'ID_EQUIPO';

	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE,DESCRIPCION,   
    			IF(ESTATUS=0,'Inactivo','Activo') AS N_ESTATUS, NO_PARTE			
				FROM $this->_name 
				ORDER BY NOMBRE ASC";   	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT   *
				FROM $this->_name 
				WHERE $this->_primary = $idObject LIMIT 1";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	
	
    public function updateRow($data){
       $result     = Array();
        $result['status']  = false;
        $sql="UPDATE $this->_name
				SET NO_PARTE		=  ".$data['inputNoPart'].",
					NOMBRE			= '".$data['inputNombre']."',
					DESCRIPCION		= '".$data['inputDesc']."',
					ESTATUS			=  ".$data['inputEstatus']."
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

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT $this->_name
				SET NO_PARTE		=  ".$data['inputNoPart'].",
					NOMBRE			= '".$data['inputNombre']."',
					DESCRIPCION		= '".$data['inputDesc']."',
					ESTATUS			=  ".$data['inputEstatus'].",	
					CREADO			=  CURRENT_TIMESTAMP";
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

	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE AS NAME 
    			FROM $this->_name
    			WHERE ESTATUS = 1 
    			ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}    
	
}
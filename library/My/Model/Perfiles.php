<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Perfiles extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PERFILES';
	protected $_primary = 'ID_PERFIL';
	
	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
	
	public function getModuleDefault($idProfile){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				WHERE MP.ID_PERFIL = $idProfile
				 AND  MP.INICIO    = 1 AND M.ACTIVO = 1 LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	  		
	}   

	public function getModules($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT M.*, M.DESCRIPCION AS M_DESCRIPCION,N.DESCRIPCION AS N_DESCRIPCION,N.ID_MENU AS IDMENU, N.*, M.SCRIPT AS S_MODULE
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				INNER JOIN MENU    N ON M.ID_MENU    = N.ID_MENU 
				WHERE MP.ID_PERFIL = ".$idObject." AND M.ACTIVO = 1
				ORDER BY N.ID_MENU ASC, M.DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getDataModule($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MODULOS
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
	
	public function getDataMenu($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MENU
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}	
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT   ID_PERFIL,
						DESCRIPCION						
				FROM PERFILES
				ORDER BY DESCRIPCION ASC";
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
				FROM PERFILES
				WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	
    	
}
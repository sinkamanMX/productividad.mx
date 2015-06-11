<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Iconos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_ICONOS';
	protected $_primary = 'ID_ICONO';
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT	
    			$this->_primary,
    			NOMBRE_IMAGEN,
    			DESCRIPCION
				FROM $this->_name
				WHERE ESTATUS = 1
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	} 
	
}
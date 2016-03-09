<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_TipoFormularios extends My_Db_Table
{
	protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'PROD_TPO_ELEMENTO';
	protected $_primary = 'ID_TIPO';
	
	public function getCbo($iFilter=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$iFilter = ($iFilter==0) ? ' WHERE MOSTRAR_SIAMES = 1 ' : ' WHERE MOSTRAR_MT = 1 ' ; 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			$iFilter
    			ORDER BY DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
}
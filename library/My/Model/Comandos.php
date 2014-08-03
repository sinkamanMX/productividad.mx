<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Comandos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'AVL_COMANDOS';
	protected $_primary = 'ID_COMANDO';
	
	public function getComandos($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_COMANDO,
				       A.DESCRIPCION,
				       A.PAQUETE
				FROM AVL_COMANDOS A
				  INNER JOIN AVL_COMANDOS_EQUIPOS B ON A.ID_COMANDO = B.ID_COMANDO
				WHERE B.ID_EQUIPO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
}
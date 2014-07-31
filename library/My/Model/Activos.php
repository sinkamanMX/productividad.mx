<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Activos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'AVL_ACTIVO';
	protected $_primary = 'ID_ACTIVO';
	
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT
				E.ID_EQUIPO,
				A.NOMBRE AS MARCA,
				M.NOMBRE AS MODELO,
				E.DESCRIPCION,
				E.IMEI,
				E.IP
				FROM AVL_EQUIPOS E
				INNER JOIN AVL_MODELO_EQUIPOS M ON E.ID_MODELO = M.ID_MODELO
				INNER JOIN AVL_MARCA_EQUIPOS A ON M.ID_MARCA   = A.ID_MARCA
				ORDER BY E.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}

	public function getDataNoAssign(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_ACTIVO,
					M.DESCRIPCION AS MODELO,
					C.DESCRIPCION AS MARCA,
					A.DESCRIPCION,
					A.IDENTIFICADOR1 AS PLACAS,
					SERIE1  AS SERIE
					FROM AVL_ACTIVO A
					INNER JOIN AVL_MODELO_ACTIVO M ON A.ID_MODELO = M.ID_MODELO
					INNER JOIN AVL_MARCA_ACTIVO  C ON M.ID_MARCA  = C.ID_MARCA
					INNER JOIN AVL_TIPO_ACTIVO   T ON A.ID_TIPO   = T.ID_TIPO
					WHERE ID_ACTIVO NOT IN(
						SELECT ID_ACTIVO 
						FROM AVL_EQUIPO_ACTIVO	
					)
				ORDER BY A.DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}
}
<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Horarios extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_HORARIO';
	protected $_primary = 'ID_HORARIO';
	
	public function getHorarios($aSucursales,$fecha){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_HORARIO,CONCAT(HORA,'-',HORA_FIN ) AS HORARIOS, HORA, HORA_FIN
			 	FROM PROD_HORARIO 
			 	WHERE ID_SUCURSAL IN ($aSucursales)";
		$query   = $this->query($sql);
		if(count($query)>0){
			foreach($query AS $key => $items){
				$assign 	= $this->getAsignados($items['ID_HORARIO'], $fecha);
				$disponibles= $this->getDisponibles($items['ID_HORARIO'], $fecha);
				
				$items['DISPONIBLES']	= (isset($disponibles['DISPONIBLES']) && $disponibles['DISPONIBLES']!="") ? $disponibles['DISPONIBLES'] : 0;
				$items['ASINGADOS']		= (isset($assign['ASIGNADOS'])   && $assign['ASIGNADOS']!="") ? $assign['ASIGNADOS'] : 0;
				$result[] = $items;		
			}			
		}	
		return $result;	
	}
	
	public function getDisponibles($idHorario,$fecha){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT COUNT(ID_USUARIO) AS DISPONIBLES
				FROM PROD_HORARIO_USUARIO
				WHERE ID_USUARIO NOT IN
				(
					SELECT ID_USUARIO
					FROM PROD_HORARIO_ASIGNADO
					WHERE ID_HORARIO = $idHorario 
					  AND DIA  = '$fecha'
				)
				AND ID_HORARIO = $idHorario  LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}
	
	public function getAsignados($idHorario,$fecha){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT COUNT(ID_ASIGNACION) AS ASIGNADOS
				FROM PROD_HORARIO_ASIGNADO
				WHERE ID_HORARIO = $idHorario 
				  AND DIA = '$fecha' LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}
	
    public function getUserAssign($fecha,$idHorario){
		$result= -1;
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT U.ID_USUARIO
				 FROM PROD_HORARIO_USUARIO U
				 INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				 WHERE U.ID_USUARIO NOT IN 
				 (
					 SELECT A.ID_USUARIO
					FROM PROD_HORARIO H
					INNER JOIN PROD_HORARIO_ASIGNADO A ON H.ID_HORARIO = A.ID_HORARIO
					WHERE A.DIA = '$fecha'
					  AND H.ID_HORARIO =  $idHorario
				 )
				 ORDER BY U.ID_USUARIO ASC LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['ID_USUARIO'];			
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
        
        $sql=" INSERT INTO PROD_HORARIO_ASIGNADO
				 SET ID_USUARIO	= ".$data['uAssign'].",
				 ID_HORARIO		= ".$data['inputhorario'].",
				 DIA			= '".$data['inputDate']."'";
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
}
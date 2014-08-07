<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Telefonos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_TELEFONOS';
	protected $_primary = 'ID_TELEFONO';
	
	public function getReporte($data){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT P.ID_TELEFONO, P.FECHA_TELEFONO, P.TIPO_GPS, P.LATITUD, P.LONGITUD,P.VELOCIDAD, P.NIVEL_BATERIA,P.UBICACION, E.DESCRIPCION_EVENTO AS EVENTO
				FROM PROD_HISTORICO_POSICION P
				INNER JOIN PROD_EVENTOS E ON P.ID_EVENTO = E.ID_EVENTO
				WHERE P.ID_TELEFONO = ".$data['strInput']."
				 AND  P.FECHA_TELEFONO BETWEEN '".$data['inputFechaIn']."'
				 						   AND '".$data['inputFechaFin']."'
				 ORDER BY P.FECHA_TELEFONO ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	

	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.DESCRIPCION, T.IDENTIFICADOR AS IMEI, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS ASIGNADO, M.DESCRIPCION AS MODELO, P.DESCRIPCION AS MARCA
			FROM PROD_TELEFONOS T
			INNER JOIN PROD_USR_TELEFONO R ON T.ID_TELEFONO = R.ID_TELEFONO
			INNER JOIN USUARIOS          U ON R.ID_USUARIO  = U.ID_USUARIO
			INNER JOIN PROD_MODELO_TELEFONO M ON T.ID_MODELO = M.ID_MODELO
			INNER JOIN PROD_MARCA_TELEFONO  P ON M.ID_MARCA  = P.ID_MARCA
			WHERE T.ID_TELEFONO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
}
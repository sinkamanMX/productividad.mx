<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Citas extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CITAS';
	protected $_primary = 'ID_CITA';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET ID_TIPO		= 1,		
					ID_EMPRESA  = ".$data['ID_EMPRESA'].",
					ID_ESTATUS  = 1,
					ID_USUARIO_CREO = ".$data['ID_USUARIO'].",
					FECHA_CITA		= '".$data['inputDate']."',
					HORA_CITA		= '".$data['inputhorario']."',		 					 
					FECHA_MODIFICACION 	= CURRENT_TIMESTAMP";
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
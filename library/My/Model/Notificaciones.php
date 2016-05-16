<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Notificaciones extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PUSH_NOTIFICATIONS';
	protected $_primary = 'ID_NOTIFICACION';
	
    public function insertRow($idSolicitud,$sMsg){
        $result     	   = Array();
        $result['status']  = false;
                
        $sql="INSERT INTO PUSH_NOTIFICATIONS SET
					ID_TIPO_PUSH 	=  1 ,
					ID_SOLICITUD    = ".$idSolicitud.",
  					MENSAJE			= '".$sMsg."', 
  					CREADO   	    = CURRENT_TIMESTAMP";
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
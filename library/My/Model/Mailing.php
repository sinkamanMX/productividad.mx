<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Mailing extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'SYS_MAILING';
	protected $_primary = 'ID_MAILING';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
		$this->query("SET NAMES utf8",false);         
        $sql="INSERT INTO $this->_name
				SET NOMBRES_DESTINATARIOS	= '".$data['inputDestinatarios']."', 
					DESTINATARIOS			= '".$data['inputEmails']."',
					TITULO_MSG			 	= '".$data['inputTittle']."',
					CUERPO_MSG				= '".$data['inputBody']."',
					REMITENTE_NOMBRE		= '".$data['inputFromName']."',
					REMITENTE_EMAIL			= '".$data['inputFromEmail']."',
					FECHA_CREADO			= CURRENT_TIMESTAMP,
					ESTATUS 				= 0";
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
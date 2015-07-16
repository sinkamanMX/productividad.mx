<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_MutUnidades extends My_Db_Table
{
	protected $_schema 	= 'SIAMES';
	protected $_name 	= 'MT_UNIDADES';
	protected $_primary = 'ID_UNIDAD';

	public function getDataTable($codCliente){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE CLIENTE_SAP = '$codCliente'
				ORDER BY FLOTA ASC,IMEI ASC ";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}
	
    public function validateUnitByimei($sImei){
		try{     	        	
			$result= Array();
			$result['status']=false;
			$this->query("SET NAMES utf8",false); 
	    	$sql ="SELECT  *
	                FROM $this->_name
	                WHERE IMEI = '$sImei' LIMIT 1";	
			$query   = $this->query($sql);
			if(count($query)>0){		 
				$result['data']   = $query[0]; 
				$result['status'] = true;			
			}
		}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }        
		return $result;	    	
    }
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;           
        $sql="INSERT INTO $this->_name
			  SET 	CLIENTE_SAP		='".$data['codCliente']."',
			  		FLOTA			='".$data['fleet']."', 
			  		IMEI			='".$data['imei']."', 
			  		IP				='".$data['ip']."', 
			  		DEVICE_NAME		='".$data['devicename']."', 
			  		DEVICE_DESC		='".$data['devicedesc']."', 
			  		EVENTO			='".$data['event']."', 
 					GPS_DATETIME	='".$data['gpsdate']."',	
	               	ID_EVENTO		= ".$data['eventid']." , 
	               	LATITUD			= ".$data['latitud']." , 
	               	LONGITUD		= ".$data['longitud']." , 
	               	VELOCIDAD		= ".$data['speed']." , 
	               	ANGULO			='".$data['heading']."'";
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

    public function updateRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
 				SET FLOTA			='".$data['fleet']."', 
			  		IMEI			='".$data['imei']."', 
			  		IP				='".$data['ip']."', 
			  		DEVICE_NAME		='".$data['devicename']."', 
			  		DEVICE_DESC		='".$data['devicedesc']."', 
			  		EVENTO			='".$data['event']."', 
 					GPS_DATETIME	='".$data['gpsdate']."',	
	               	ID_EVENTO		= ".$data['eventid']." , 
	               	LATITUD			= ".$data['latitud']." , 
	               	LONGITUD		= ".$data['longitud']." , 
	               	VELOCIDAD		= ".$data['speed']." , 
	               	ANGULO			='".$data['heading']."'
				WHERE $this->_primary =".$data['idUnit']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    }     
}
<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Solicitudes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CITAS_SOLICITUD';
	protected $_primary = 'ID_SOLICITUD';
	
    public function getDataTablebyClient($idCliente){
      	$this->query("SET NAMES utf8",false);         
		$result= Array();
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.NOMBRE AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				WHERE S.ID_CLIENTE = $idCliente";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	        
    }
    
    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.RAZON_SOCIAL AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS,
				CONCAT(Q.NOMBRE,' ',Q.APELLIDOS) AS N_CONTACTO , Q.EMAIL
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				INNER JOIN PROD_QR_CONTACTOS Q ON S.ID_CONTACTO_QR = Q.ID_CONTACTO_QR
                WHERE S.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql=" INSERT INTO $this->_name SET 
        		ID_CLIENTE		= ".$data['inputCliente'].",
				ID_TIPO			= ".$data['inputTipo'].",
				ID_ESTATUS		= 1,
				UNIDAD			= '".$data['inputUnidad']."' ,
				COMENTARIO		= '".$data['inputComment']."',		
				FECHA_CITA		= '".$data['inputFechaIn']."',
				FECHA_CREADO 	=  CURRENT_TIMESTAMP";
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

        $sql="UPDATE $this->_name SET
				UNIDAD			= '".$data['inputUnidad']."' ,
				COMENTARIO		= '".$data['inputComment']."',		
				FECHA_CITA		= '".$data['inputFechaIn']."'
				WHERE $this->_primary =".$data['catId']." LIMIT 1";
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
    
    
    public function getDataTable($sOptions=0){
      	$this->query("SET NAMES utf8",false);         
		$result= Array();
		$sFilter = ($sOptions==0) ?  ' = 1' : ' > 1';
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.RAZON_SOCIAL AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				WHERE S.ID_ESTATUS $sFilter ";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	      	
    }
    
    public function updateAtencion($data){
       $result     = Array();
        $result['status']  = false;
        $sql="UPDATE $this->_name SET
        		ID_ESTATUS		=  ".$data['inputEstatus']." ,
        		FECHA_CONFIRMADA= '".$data['inputFechaIn']."' ,
        		HORA_INICIO		= '".$data['inputTimeBegin']."' ,
        		HORA_FIN		= '".$data['inputTimeEnd']."' ,
        		REVISION		= '".$data['inputRevision']."'
        		WHERE $this->_primary =".$data['catId']." LIMIT 1";
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
<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Formresult extends My_Db_Table
{
	protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'PROD_FORMULARIO';
	protected $_primary = 'ID_FORMULARIO';

	
	public function insertaRespuestas($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_FORM_RESULTADO 
        		SET ID_FORMULARIO			=  ".$data['inputFormulario'].",
            		ID_EQUIPO				=  -1,	
            		ID_USUARIO_CONTESTO		=  ".$data['inputUser'].",
            		FECHA_CAPTURA_EQUIPO	= '".$data['inputFecha']."',
            		FECHA_CAPTURA_SERVIDOR  = CURRENT_TIMESTAMP,
            		FECHA_FIN_CAPTURA		= '".$data['inputFechaFin']."'";        
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
	
	public function updateCita($data){
       $result     = Array();
        $result['status']  = false;

	    $sql = "UPDATE PROD_CITA_FORMULARIO
	              SET ID_RESULTADO  = ".$data['idResultado'].",
	              	  RESPONDIO     = 'W'
	            WHERE ID_FORMULARIO = ".$data['inputFormulario']." 
	              AND ID_CITA       = ".$data['strInput']." LIMIT 1";            
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
    
    public function citaTerminada($idCita){
		$result= false;
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT COUNT(*) AS PENDIENTES
	            FROM  PROD_CITA_FORMULARIO
	            WHERE ID_RESULTADO IS NULL AND
	                  ID_CITA = ".$idCita;
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];	
			if($result['PENDIENTES']== 0){
				$result = true;
			}		
		}	
        
		return $result;	
    }   
    
	public function inserRespuesta($idResultado,$idElemento,$sResultado){
        $result     = Array();
        $result['status']  = false;
      	$sql="INSERT INTO PROD_FORM_DETALLE_RESULTADO
	      		 SET ID_RESULTADO  = ".$idResultado.",
	              	 ID_ELEMENTO   = ".$idElemento.",
	                 CONTESTACION  ='".$sResultado."'";        
        try{            
    		$query   = $this->query($sql,false);
    		if($query){
    			$result['status']  = true;
    		}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result['status'];			
	}    
}
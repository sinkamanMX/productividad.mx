<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Mensajes extends My_Db_Table
{
	protected $_schema 	= 'taccsi';
	protected $_name 	= 'MENSAJES';
	protected $_primary = 'ID_MENSAJE';
	
    public function getDataTable(){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT M.*, 
				IF(M.TIPO_ENVIO=0,'En General','Seleccionados') AS ENVIAR_A,
				IF(M.TIPO_DISPOSITIVOS=0,'Todos', IF(M.TIPO_DISPOSITIVOS=1,'Android','IOS')) AS DISPOSITIVOS,
				IF(M.REENVIAR=0,'Una Vez',CONCAT('ENVIAR CADA ',M.ENVIAR_CADA, 
					IF(M.UNIDAD_REPETICION='M',' Minuto(s)', IF(M.UNIDAD_REPETICION='H',' Hora(s)',' Dia(s)'))
				)) AS REENVIO,
				IF(M.REENVIAR=0,M.FECHA_ENVIO,CONCAT(M.FECHA_ENVIO,' - ',M.FECHA_FIN)) AS FECHA_ENVIAR,
				E.DESCRIPCION AS N_ESTATUS,
				T.DESCRIPCION AS N_MOTIVO
				FROM MENSAJES M
				INNER JOIN MENSAJES_ESTATUS E ON M.ID_ESTATUS = E.ID_ESTATUS
				INNER JOIN MENSAJES_TIPOS   T ON M.ID_TIPO    = T.ID_TIPO
				ORDER BY M.ID_MENSAJE DESC";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}
        
		return $result;	        
    }
    
    public function getRowInfo($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT M.*, E.DESCRIPCION AS N_ESTATUS
				FROM MENSAJES M
				INNER JOIN MENSAJES_ESTATUS E ON M.ID_ESTATUS = E.ID_ESTATUS
				WHERE M.ID_MENSAJE = $idObject
				LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}
        
		return $result;	    	
    }
    
    public function getClientsBySms($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT ID_CLIENTE 
				FROM MENSAJE_DISPOSITIVOS
				WHERE ID_MENSAJE = $idObject";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}
        
		return $result;	        
    }  

    public function getTipos(){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT ID_TIPO AS ID, DESCRIPCION AS NAME
				FROM MENSAJES_TIPOS
				ORDER BY DESCRIPCION ASC";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}
        
		return $result;    	
    }
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO MENSAJES		 
					SET ID_ESTATUS  		=  1,
						MENSAJE				= '".$data['txaMensaje'] ."',
				        TIPO_ENVIO			=  ".$data['cboSendto']  .",
				        TIPO_DISPOSITIVOS 	=  ".$data['cboSendtoSo'].",
				        FECHA_ENVIO			= '".$data['txtFecha']	 ."',
				        REENVIAR			=  ".$data['cboReSend']	 .",
				        FECHA_FIN			= '".$data['txtFechaFin']."',
				        ENVIAR_CADA			=  ".$data['cboEach']	 .",
				        UNIDAD_REPETICION	= '".$data['cboTime']	 ."',
				        ID_TIPO				=  ".$data['cboTipo']	 .",
				        ORIGEN				= 1,
				        ID_MENSAJE_APP      =  ".$data['inputApp']	 .",
				        CREADO				= CURRENT_TIMESTAMP";
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

        $sql="UPDATE MENSAJES		 
				SET MENSAJE				= '".$data['txaMensaje'] ."',
			        TIPO_ENVIO			=  ".$data['cboSendto']  .",
			        TIPO_DISPOSITIVOS 	=  ".$data['cboSendtoSo'].",
			        FECHA_ENVIO			= '".$data['txtFecha']	 ."',
			        REENVIAR			=  ".$data['cboReSend']	 .",
			        FECHA_FIN			= '".$data['txtFechaFin']."',
			        ENVIAR_CADA			=  ".$data['cboEach']	 .",
			        UNIDAD_REPETICION	= '".$data['cboTime']	 ."',
			        ULT_ACTUALIZACION	= CURRENT_TIMESTAMP
				WHERE ID_MENSAJE =".$data['catId']." LIMIT 1";        
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
    
	public function setRelations($data){
        $result     = Array();
        $result['status']  = false;
        $sql="INSERT INTO MENSAJE_DISPOSITIVOS		 
					SET ID_MENSAJE 	=  ".$data['idMsg'].",
						ID_CLIENTE 	=  ".$data['idRelation'];
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

    public function delRelations($idObject){
        $result     = Array();
        $result['status']  = false;  
        
        $sql="DELETE FROM  MENSAJE_DISPOSITIVOS
					 WHERE ID_MENSAJE = $idObject";   
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
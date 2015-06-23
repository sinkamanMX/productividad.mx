<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Protocolos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROTOCOLOS';
	protected $_primary = 'ID_PROTOCOLO';

	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  $this->_name
                WHERE ID_SOLICITUD = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}
        
		return $result;			
	}
	
	public function getTipo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT ID_TIPO_CONTRATO AS ID, DESCRIPCION AS NAME
				FROM  PROT_TIPO_CONTRATO
                ORDER BY DESCRIPCION ASC";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;			
	}	
	
	public function getPersonas($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  PROTOCOLOS_CONTACTOS
                 WHERE ID_PROTOCOLO = $idObject
                 ORDER BY ORDEN ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;			
	}	
	
    public function insertRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name			 
					SET ID_SOLICITUD	= ".$aDataIn['idSolicitud'].",
						ID_AGENCIA		= ".$aDataIn['idAgencia'].",
						ID_TIPO_CONTRATO= ".$aDataIn['inputContrato'].", 
						FECHA_CREADO	= CURRENT_TIMESTAMP,
						RAZON_SOCIAL	= '".$aDataIn['inputName']."',
						RFC				= '".$aDataIn['inputRfc']."',
						NOMBRE_FLOTA	= '".$aDataIn['inputFlota']."',
						FOLIO_CONTRATO	= '".$aDataIn['inputFolio']."',
						ASESOR_COMERCIAL= '".$aDataIn['inputAsesor']."',
						OBSERVACIONES	= '".$aDataIn['inputObservaciones']."'";
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
    
    public function updateRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sFilter  = (isset($aDataIn['inputIdAssign']) && $aDataIn['inputIdAssign'] !="" ) ? 'ID_ICONO = '.$aDataIn['inputIdAssign'].', ' : '';
		$sFilter  .= (isset($aDataIn['inputIdOvision']) && $aDataIn['inputIdOvision'] !="" ) ? 'ID_OVISION		=  "'.$aDataIn['inputIdOvision'].'", ' : '';
		
        $sql="UPDATE $this->_name			 
					SET ID_TIPO_CONTRATO=  ".$aDataIn['inputContrato'].", 
						RAZON_SOCIAL	= '".$aDataIn['inputName']."',
						RFC				= '".$aDataIn['inputRfc']."',
						NOMBRE_FLOTA	= '".$aDataIn['inputFlota']."',
						FOLIO_CONTRATO	= '".$aDataIn['inputFolio']."',
						ASESOR_COMERCIAL= '".$aDataIn['inputAsesor']."',
						OBSERVACIONES	= '".$aDataIn['inputObservaciones']."'
				WHERE $this->_primary =".$aDataIn['idProtocolo']." LIMIT 1";
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

    public function insertElement($aDataElement,$idObject){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROTOCOLOS_CONTACTOS
				SET	  ID_PROTOCOLO			= $idObject,
					  NOMBRE_COMPLETO 		='".@$aDataElement['nombre']."',
					  CLAVE_IDENTIFICACION  ='".@$aDataElement['clave']."',
					  EMPRESA				='".@$aDataElement['empresa']."', 
					  PUESTO				='".@$aDataElement['puesto']."',
					  EMAIL					='".@$aDataElement['email']."',
					  FECHA_NACIMIENTO		='".@$aDataElement['fecnac']."',		
					  TEL_24HRS				='".@$aDataElement['movil24hrs']."',	
					  TEL_OFICINA			='".@$aDataElement['ofna']."',
					  TEL_MOVIL				='".@$aDataElement['telmovil']."',
					  NEXTEL_ID				='".@$aDataElement['nextid']."',	
					  CREADO				= CURRENT_TIMESTAMP,
					  EVENTOS_PRIORIDAD		= ".$aDataElement['ispriori'].",
					  SOLICITAR_POSICION	= ".$aDataElement['isposc'].",
					  ORDEN					='".@$aDataElement['orden']."'";
        try{            
    		$query   = $this->query($sql,false);
			$result['status']  = true;					
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }      
    
    public function updateRowRel($aDataElement){
        $result     = Array();
        $result['status']  = false;
        
       $sql="UPDATE PROTOCOLOS_CONTACTOS
				SET   NOMBRE_COMPLETO 		='".@$aDataElement['nombre']."',
					  CLAVE_IDENTIFICACION  ='".@$aDataElement['clave']."',
					  EMPRESA				='".@$aDataElement['empresa']."', 
					  PUESTO				='".@$aDataElement['puesto']."',
					  EMAIL					='".@$aDataElement['email']."',
					  FECHA_NACIMIENTO		='".@$aDataElement['fecnac']."',		
					  TEL_24HRS				='".@$aDataElement['movil24hrs']."',	
					  TEL_OFICINA			='".@$aDataElement['ofna']."',
					  TEL_MOVIL				='".@$aDataElement['telmovil']."',
					  NEXTEL_ID				='".@$aDataElement['nextid']."',	
					  CREADO				= CURRENT_TIMESTAMP,
					  EVENTOS_PRIORIDAD		= ".$aDataElement['ispriori'].",
					  SOLICITAR_POSICION	= ".$aDataElement['isposc'].",
					  ORDEN					='".@$aDataElement['orden']."'
			WHERE ID_CONTACTO_PROTOCOLO = ".$aDataElement['id']." LIMIT 1";				        
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

    public function deleteRowRel($aDataElement,$idObject){
        $result     = Array();
        $result['status']  = false;  

		$sql  = "DELETE FROM PROTOCOLOS_CONTACTOS 
					WHERE ID_CONTACTO_PROTOCOLO   = ".$aDataElement['id']."
					  AND ID_PROTOCOLO  = ".$idObject." LIMIT 1";
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
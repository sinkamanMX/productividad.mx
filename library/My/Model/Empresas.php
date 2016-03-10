<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Empresas extends My_Db_Table
{
    protected $_schema 	= 'taccsi';
	protected $_name 	= 'EMPRESAS';
	protected $_primary = 'ID_EMPRESA';
	
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;

        $idEstado    = (isset($data['inputEstadoF'])    && $data['inputEstadoF']    > 0) ? $data['inputEstadoF']      : 0;
        $idMunicipio = (isset($data['inputMunicipioF']) && $data['inputMunicipioF'] > 0) ? $data['inputMunicipioF']   : 0;        
        $sUserUda 	 = (isset($data['inputUserUda'])    && $data['inputUserUda']  !="")     ? $data['inputUserUda']    : '';
        $sPassUda 	 = (isset($data['inputPasswordUda']) && $data['inputPasswordUda'] !="") ? $data['inputPasswordUda']: '';
        
        $sql="INSERT INTO $this->_name	
        		SET	ID_BROKER		=  ".$data['idBroker'].",
        			NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					COD_CLIENTE		= '".$data['inputSap']."',
					ID_TIPO_EMPRESA =  ".$data['inputTipo'].",
					NO_TECNICOS		=  ".$data['inputTecnicos'].",  					
        			FECHA_REGISTRO	= CURRENT_TIMESTAMP";       			  
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']  = $query_id[0]['ID_LAST'];  			 	
				$result['status']  = true;	
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }  	
    
  	public function validateExist($sRfc){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE RFC = '".$sRfc."' LIMIT 1";		         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;			
	}    

    public function setActivate($idEmpresa){
        $result  = false;

        $sql="UPDATE $this->_name
				SET ESTATUS         = 1					 
			  WHERE $this->_primary = ".$idEmpresa." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    }	

	public function getDataTables($idBroker=-1){
		$result= Array();
		$filter = ($idBroker!=-1) ? 'AND ID_BROKER = '.$idBroker: '';
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_name.*, EMPRESAS_TIPO.DESCRIPCION AS N_TIPO
				FROM $this->_name
				INNER JOIN EMPRESAS_TIPO ON $this->_name.ID_TIPO_EMPRESA = EMPRESAS_TIPO.ID_TIPO_EMPRESA
				WHERE $this->_name.ID_TIPO_EMPRESA NOT IN (1,4)
				$filter
				ORDER BY RAZON_SOCIAL ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
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
    
    
    public function updateRow($data,$idObject){
        $result     = Array();
        $result['status']  = false;
                
        $sUserUda 	 = (isset($data['inputUserUda'])    && $data['inputUserUda']  !="")     ? $data['inputUserUda']    : '';
        $sPassUda 	 = (isset($data['inputPasswordUda']) && $data['inputPasswordUda'] !="") ? $data['inputPasswordUda']: '';
        
        $sql="UPDATE $this->_name
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					COD_CLIENTE		= '".$data['inputSap']."',
					/*CLIENTE_UDA		=  ".$data['inputClienteUDA'].",					
					USUARIO_UDA  	= '".$sUserUda."',
					PASSWORD_UDA	= '".$sPassUda."',
					COBRAR_VIAJES	=  ".$data['inputCobro']."*/
					ID_TIPO_EMPRESA =  ".$data['inputTipo'].",
					NO_TECNICOS		=  ".$data['inputTecnicos']." 
				WHERE $this->_primary   = ".$idObject;
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    } 

	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_EMPRESA AS ID, NOMBRE AS NAME
				FROM $this->_name
				ORDER BY RAZON_SOCIAL ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}   

	public function getCboTipos($filter=false){
		$result= Array();
		$sFilter = ($filter==0) ? 'WHERE ON_ADMIN = 1': 'WHERE ON_BROKER = 1';
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_TIPO_EMPRESA AS ID, DESCRIPCION AS NAME
				FROM EMPRESAS_TIPO
				$sFilter
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}

	public function getBrokers(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_name.*, EMPRESAS_TIPO.DESCRIPCION AS N_TIPO
				FROM $this->_name
				INNER JOIN EMPRESAS_TIPO ON $this->_name.ID_TIPO_EMPRESA = EMPRESAS_TIPO.ID_TIPO_EMPRESA
				WHERE $this->_name.ID_TIPO_EMPRESA = 4 
				ORDER BY RAZON_SOCIAL ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}
	
    
    public function getTotalAgents($idObject){
		$result=1;
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT SUM(NO_TECNICOS) AS TOTAL
				FROM EMPRESAS 
				WHERE ID_TIPO_EMPRESA NOT IN (1,4) 
				  AND ID_BROKER = $idObject";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['TOTAL'];			
		}	
        
		return $result;	    	
    }

    public function getDataByCode($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT GROUP_CONCAT(U.`USUARIO` SEPARATOR ',')  AS N_MAILS
				FROM USUARIOS U
				INNER JOIN USR_EMPRESA R ON U.`ID_USUARIO` = R.`ID_USUARIO`
				INNER JOIN SUCURSALES  S ON R.`ID_SUCURSAL`= S.`ID_SUCURSAL`
				WHERE S.ID_EMPRESA = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['N_MAILS'];			
		}	
        
		return $result;	    	
    }	    
}
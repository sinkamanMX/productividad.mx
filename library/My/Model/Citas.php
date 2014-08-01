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
				SET ID_TPO				= 1,		
					ID_EMPRESA  		= ".$data['ID_EMPRESA'].",
					ID_ESTATUS  		= 1,
					ID_CLIENTE          = ".$data['ID_CLIENTE'].",
					ID_USUARIO_CREO 	= ".$data['ID_USUARIO'].",
					FECHA_CITA			= '".$data['inputDate']."',
					HORA_CITA			= '".$data['inputhorario']."',
					CONTACTO 			= '".$data['inputContacto']."',
					TELEFONO_CONTACTO   = '".$data['inputTelContacto']."', 		 					 
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
	
	public function insertDomCita($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_CITA_DOMICILIO
				SET ID_CITA		=  ".$data['idCita'].",
					CALLE		= '".$data['inputStreet']."',
					COLONIA		= '".$data['scolonia']."',
					NO_EXT		= '".$data['inputNoExt']."',
					NO_INT		= '".$data['inputNoInt']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					CP			= '".$data['inputCP']."',
					ESTADO		= '".$data['sEstado']."',
					REFERENCIAS	= '".$data['inputRefs']."',
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
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
	
	public function insertExtraCitas($data){
        $result     = false;            
        $sql = "INSERT INTO PROD_CITA_EXTRAS 
				VALUES (".$data['idCita'].",'".utf8_encode('Tarjeta Circulaci—n')."','".$data['inputTdc']."'),
				(".$data['idCita'].",'Licencia de Manejo','".$data['inputLicencia']."'),
				(".$data['idCita'].",'Vigencia de la Licencia','".$data['inputVigencia']."'),
				(".$data['idCita'].",'".utf8_encode('Lugar de emisi—n')."','".$data['inputEmision']."'),
				(".$data['idCita'].",'Marca','".$data['sMarca']."'),
				(".$data['idCita'].",'Modelo','".$data['sModelo']."'),
				(".$data['idCita'].",'".utf8_encode('A–o')."','".$data['inputAno']."'),
				(".$data['idCita'].",'Color','".$data['sColor']."'),
				(".$data['idCita'].",'Placas','".$data['inputPlacas']."'),
				(".$data['idCita'].",'No. de Serie','".$data['inputSerie']."'),
				(".$data['idCita'].",'No. de Motor','".$data['inputMotor']."')";  
        try{
    		$query   = $this->query($sql,false);
    		if($query){
    			$result= true;	
    		}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}

	public function insertaFormCita($data){
        $result	= false;
        $sql="INSERT INTO PROD_CITA_FORMULARIO
				SET ID_CITA			=  ".$data['idCita'].",
					ID_FORMULARIO 	= 1";
        try{
    		$query   = $this->query($sql,false);
    		if($query){
    			$result= true;	
    		}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;		
	}
	
	public function insertDomCitaOther($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_CITA_DOMICILIO
				SET ID_CITA		=  ".$data['idCita'].",
					CALLE		= '".$data['inputStreetO']."',
					COLONIA		= '".$data['scolonia']."',
					NO_EXT		= '".$data['inputNoExtO']."',
					NO_INT		= '".$data['inputNoIntO']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					CP			= '".$data['inputCPO']."',
					ESTADO		= '".$data['sEstado']."',
					REFERENCIAS	= '".$data['inputRefsO']."',
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
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
	
	public function getCitasPendientes(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT  'false' AS allday ,
				PROD_ESTATUS_CITA.COLOR AS borderColor,
				PROD_ESTATUS_CITA.COLOR AS color,
				CONCAT(CALLE,' #',NO_EXT,', Col.',COLONIA,', ',MUNICIPIO,', ',ESTADO) AS description,
				CONCAT(FECHA_CITA,' ',HORA_CITA) AS end,
				CONCAT(FECHA_CITA,' ',HORA_CITA) AS start ,
				CONCAT(CONTACTO,' - ',TELEFONO_CONTACTO) AS title,
				CONTACTO AS cliente,
				TELEFONO_CONTACTO AS telefono,
				PROD_CITAS.ID_CITA AS id,
				PROD_ESTATUS_CITA.DESCRIPCION AS estatus
    			FROM PROD_CITAS
    			INNER JOIN PROD_CITA_DOMICILIO ON PROD_CITAS.ID_CITA = PROD_CITA_DOMICILIO.ID_CITA
    			INNER JOIN PROD_ESTATUS_CITA  ON PROD_CITAS.ID_ESTATUS  = PROD_ESTATUS_CITA.ID_ESTATUS
    			WHERE PROD_CITAS.ID_ESTATUS IN (1,2,5)";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	
	public function getCboStatus(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_ESTATUS AS ID, DESCRIPCION AS NAME
				FROM PROD_ESTATUS_CITA
				WHERE ACTIVO = 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCitasDet($idCita){
		$filter = '';
		$result= Array();
		
		if($idCita>-1){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_CITA = '.$idCita;
		}		

    	$sql ="SELECT A.ID_CITA,
  	               A.FECHA_CITA,
  	               A.HORA_CITA,
  	               A.CONTACTO,
  	               A.TELEFONO_CONTACTO,
  	               A.FOLIO,
  	               CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CITA,
  	               B.REFERENCIAS AS REF_CITA,
  	               B.CP AS CP_CITA,
  	               B.LATITUD AS LAT_CITA,
  	               B.LONGITUD AS LON_CITA,
  	               D.COD_CLIENTE,
  	               CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,
                   D.TELEFONO_FIJO,
                   D.TELEFONO_MOVIL,
                   D.EMAIL,
                   CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CLIENTE,
                   E.REFERENCIAS,
                   E.LATITUD,
                   E.LONGITUD ,
                   S.DESCRIPCION AS ESTATUS ,
                   A.ID_ESTATUS,
                   IF(U.NOMBRE IS NULL ,'No asignado' ,CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS OPERADOR,
                   U.ID_USUARIO AS ID_OPERADOR
  	        FROM PROD_CITAS A
  	           INNER JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
  	           INNER JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
  	           INNER JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
  	           INNER JOIN PROD_ESTATUS_CITA       S ON A.ID_ESTATUS = S.ID_ESTATUS	  	           
  	           LEFT JOIN PROD_CITA_USR            C ON C.ID_CITA    = A.ID_CITA
  	           LEFT JOIN USUARIOS            	  U ON C.ID_USUARIO = U.ID_USUARIO
  	           ".$filter;  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}		
	
	public function getCitasSearch($dateIn,$dateFin,$keySearch,$Status,$idCita=-1){
		$filter = '';
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		
		if($keySearch!=''){
			$filter .= ' WHERE A.CONTACTO LIKE "%'.$keySearch.'%" OR A.FOLIO LIKE"%'.$keySearch.'%" OR  D.NOMBRE LIKE "%'.$keySearch.'%"';	
		}
		
		if($Status!=''){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_ESTATUS IN ('.$Status.') ';
		}
		
		if($idCita>-1){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_CITA = '.$idCita;
		}		
		
		if($dateIn!='' && $dateFin!=""){
			$filter .= ($filter=='') ? ' WHERE ': ' OR ';
			$filter .= 'A.FECHA_CITA BETWEEN "'.$dateIn.'" AND "'.$dateFin.'"';
		}
    	$sql ="SELECT A.ID_CITA,
  	               A.FECHA_CITA,
  	               A.HORA_CITA,
  	               A.CONTACTO,
  	               A.TELEFONO_CONTACTO,
  	               A.FOLIO,
  	               CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CITA,
  	               B.REFERENCIAS AS REF_CITA,
  	               B.CP AS CP_CITA,
  	               B.LATITUD AS LAT_CITA,
  	               B.LONGITUD AS LON_CITA,
  	               D.COD_CLIENTE,
  	               CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,
                   D.TELEFONO_FIJO,
                   D.TELEFONO_MOVIL,
                   D.EMAIL,
                   CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CLIENTE,
                   E.REFERENCIAS,
                   E.LATITUD,
                   E.LONGITUD ,
                   S.DESCRIPCION AS ESTATUS ,
                   A.ID_ESTATUS,
                   IF(U.NOMBRE IS NULL ,'No asignado' ,CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS OPERADOR,
                   U.ID_USUARIO AS ID_OPERADOR
  	        FROM PROD_CITAS A
  	           INNER JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
  	           INNER JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
  	           INNER JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
  	           INNER JOIN PROD_ESTATUS_CITA       S ON A.ID_ESTATUS = S.ID_ESTATUS	  	           
  	           LEFT JOIN PROD_CITA_USR            C ON C.ID_CITA    = A.ID_CITA
  	           LEFT JOIN USUARIOS            	  U ON C.ID_USUARIO = U.ID_USUARIO
  	           ".$filter;    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function setRow($data){
        $result  = false;
        $options = '';
        $idInput		= $data['strInput'];
		$inputfechaCita = explode(" ", $data['inputFecha'] );
		$inputEstatus   = $data['inputEstatus']; 
		$changeDate     = $data['inputChangeDate'];
		
		if($changeDate==1){
			$date = $inputfechaCita[0]; 
			$time = $inputfechaCita[1];			
			$options = "FECHA_CITA			= '".$date."',
					   HORA_CITA			= '".$time.":00',";
		}
		       
        $sql="UPDATE $this->_name
				SET  ID_ESTATUS 			= ".$inputEstatus.",
					".$options."
					 ID_USUARIO_MODIFICO	= ".$data['ID_USUARIO']." , 
					 FECHA_MODIFICACION		= CURRENT_TIMESTAMP  
				WHERE ID_CITA =	$idInput";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}
	
	public function changePersonal($data){
        $result = false;
        $idInput		= $data['strInput'];        
	
        if($data['ID_OPERADOR']!= NULL && $data['ID_OPERADOR']!=""){
        	$sql="UPDATE PROD_CITA_USR
				SET  ID_USUARIO =  ".$data['inputPersonal']."
					 WHERE ID_CITA = $idInput LIMIT 1";	
        }if($data['ID_OPERADOR'] == NULL){
        	$sql="INSERT INTO PROD_CITA_USR
				SET  ID_USUARIO = ".$data['inputPersonal'].",
				     ID_CITA    = $idInput";        	
        }else{
        	$sql="DELETE FROM PROD_CITA_USR
					 WHERE ID_CITA = $idInput LIMIT 1";        	
        }
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	   		
	}
	
	public function insertActPrev($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO AVL_ACTIVOS_PREVIO
				SET ID_CLIENTE		= ".$data['idCliente'].",
					DESCRIPCION		= '".$data['nCliente']."-".$data['inputPlacas']."',
					IDENTIFICADOR1	= '".$data['inputPlacas']."',
					SERIE1			= '".$data['inputSerie']."',
					MODELO			= ".$data['idCliente'].",
					TIPO_VEHICULO	= 1,
					COLOR			= ".$data['inputColor']; 
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
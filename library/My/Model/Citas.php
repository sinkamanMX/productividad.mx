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
					ID_DOMICILIO		= ".$data['idDomicilio'].",
					ID_ESTATUS  		= 1,
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
        
        $sql="INSERT INTO PROD_CITA_DOMICLIO
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
				(".$data['idCita'].",'Marca','".$data['inputMarca']."'),
				(".$data['idCita'].",'Modelo','".$data['inputModelo']."'),
				(".$data['idCita'].",'".utf8_encode('A–o')."','".$data['inputAno']."'),
				(".$data['idCita'].",'Color','".$data['inputColor']."'),
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
	
}
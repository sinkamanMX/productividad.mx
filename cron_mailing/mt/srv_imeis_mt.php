<?php

   //require_once('./lib/nusoap.php'); 
  
  function busca_unidad($con,$imei){
    $res = -1;
    $sql = "SELECT COUNT(*) AS EXISTE
            FROM MT_UNIDADES
            WHERE IMEI = '".$imei."'";
    if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      $res = $row->EXISTE;
      mysqli_free_result($qry);
    }
    return $res;
  } 

  function guarda_posiciones($con,
                             $gpsdate,
                             $lat,
                             $lon,
                             $vel,
                             $angulo,
                             $id_evento,
                             $evento,   
                             $imei,     
                             $motor,    
                             $flota,    
                             $ip,       
                             $devname,  
                             $devdesc,  
                             $location,
                             $customerpass){
    if (busca_unidad($con,$imei) == 0){
      $sql = "INSERT INTO MT_UNIDADES(
                CLIENTE_SAP,
                FLOTA,
                IMEI,
                IP,
                DEVICE_NAME,
                DEVICE_DESC,
                EVENTO,
                GPS_DATETIME,
                ID_EVENTO,
                LATITUD,
                LONGITUD,
                VELOCIDAD,
                ANGULO
              ) VALUES (
                '".$customerpass."',
                '".$flota."',
                '".$imei."',
                '".$ip."',
                '".$devname."',
                '".$devdesc."',
                '".$evento."',
                '".$gpsdate."',
                ".$id_evento.",
                ".$lat.",
                ".$lon.",
                ".$vel.",
                '".$angulo."'
              )";
    } else {
      $sql = "UPDATE MT_UNIDADES
              SET CLIENTE_SAP = '".$customerpass."',
                FLOTA = '".$flota."',
                IP = '".$imei."',
                DEVICE_NAME = '".$devname."',
                DEVICE_DESC = '".$devdesc."',
                EVENTO = '".$evento."',
                GPS_DATETIME = '".$gpsdate."',
                ID_EVENTO = ".$id_evento.",
                LATITUD = ".$lat.",
                LONGITUD = ".$lon.",
                VELOCIDAD = ".$vel.",
                ANGULO = '".$angulo."'
              WHERE IMEI = '".$imei."'";
    }
    echo $sql;
    $result  = mysqli_query($con, $sql);
    if(!$result > 0){
      throw new Exception($result);
    }
  }

  function principal($con){
    $sql = "SELECT COD_CLIENTE
            FROM PROD_CLIENTES
            WHERE COD_CLIENTE like 'CL%'";
    if ($qry = mysqli_query($con,$sql)){
      while ($row = mysqli_fetch_object($qry)){
        $cliente = $row->COD_CLIENTE;
        extrae_unidades_cliente_ovision($con,$cliente);
      }
      mysqli_free_result($qry);
    } 
  }

  
  function extrae_unidades_cliente_ovision($con,$customerpass){
    $soap_client = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
    //$soap_client  = new SoapClient("http://ws.grupouda.com.mx/wsUDAHistoryGetByPlate.asmx?WSDL");
    $param = array('sLogin'          => 'wbs_admin@grupouda.com.mx',
                   'sPassword'       => 'w3b4dm1n',
                   'strCustomerPass' => $customerpass);
     sleep(18);
    $result=$soap_client->HistoyDataLastLocationByCustomerPass($param); 
    print_r($result); 
    if (is_object($result)){
      $x = get_object_vars($result);
      $y = get_object_vars($x['HistoyDataLastLocationByCustomerPassResult']);
      $xml = $y['any'];
      if($xml2 = simplexml_load_string($xml)){
        $cuenta = count($xml2->Response->Plate);
        //echo "elementos ".$cuenta;
        $c = 0;
        for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
          $gpsdate   = (string) $xml2->Response->Plate[$i]->hst->DateTimeGPS;
          $lat       = (string)$xml2->Response->Plate[$i]->hst->Latitude;
          $lon       = (string)$xml2->Response->Plate[$i]->hst->Longitude;
          $vel       = (string)$xml2->Response->Plate[$i]->hst->Speed;
          $angulo    = (string)$xml2->Response->Plate[$i]->hst->Heading;
          $id_evento = (string)$xml2->Response->Plate[$i]->hst->EventID;
          $evento    = (string)$xml2->Response->Plate[$i]->hst->Event;
          $imei      = (string)$xml2->Response->Plate[$i]->hst->Imei;
          $motor     = (string)$xml2->Response->Plate[$i]->hst->IgnitionState;
          $flota     = (string)$xml2->Response->Plate[$i]->hst->Fleet;
          $ip        = (string)$xml2->Response->Plate[$i]->hst->IP;
          $devname   = (string)$xml2->Response->Plate[$i]->hst->DeviceName;    
          $devdesc   = (string)$xml2->Response->Plate[$i]->hst->DeviceDesc; 
          $location  = (string)$xml2->Response->Plate[$i]->hst->Location;                                                                                                                              
          try {
            guarda_posiciones($con,
                              $gpsdate,
                              $lat,
                              $lon,
                              $vel,
                              $angulo,
                              $id_evento,
                              $evento,   
                              $imei,     
                              $motor,    
                              $flota,    
                              $ip,       
                              $devname,  
                              $devdesc,  
                              $location,
                              $customerpass);
          } catch (Exception $e) {
              print_r($e->getMessage()."\n");
          }
         $c = $c+1;
        }
      } else {
        echo 'Not XML';
      }
    } else {
      echo "no es";
    }
    echo " total enviados: ".$c;
  }
    //if ($xml = simplexml_load_string($result)) {

  $con = mysqli_connect("localhost","root","1shteedg2","SIMA");
  if ($con){
    principal($con);
    mysqli_close($con);
  }

?>

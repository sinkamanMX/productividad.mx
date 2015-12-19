<?php
  error_reporting(E_ALL);
  $conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));

  $pathReporte = '/var/www/vhosts/sima/htdocs/public/reportes/';
  $sql = "SELECT *
      FROM PROD_CITAS
      WHERE ENVIO_SAP = 3"; 
  $query = mysqli_query($conexion, $sql);
  while($result = mysqli_fetch_array($query)){
    $nameFile = $result['FOLIO'].".pdf";

    if(file_exists($pathReporte.$nameFile)){
      $updated =  setMarkReporte($result['ID_CITA']);
      if($updated){
        unlink($pathReporte.$nameFile);
      }
    }else{
      $updated =  setMarkReporte($result['ID_CITA']);
    }
  }  

  function setMarkReporte($idOject){
    global $conexion;
    $result = false;
      $sql ="UPDATE PROD_CITAS 
          SET ENVIO_SAP = 4
        WHERE ID_CITA = $idOject";
    $query  = mysqli_query($conexion, $sql);
    if($query){
      $result= true;
    }
    return $result;   
  }  
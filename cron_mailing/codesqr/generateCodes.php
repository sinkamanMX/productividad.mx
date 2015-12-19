<?php
  require_once 'phpqrcode.php'; 
  error_reporting(E_ALL);
  $conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));

  $pathCodes = '/var/www/vhosts/sima/htdocs/public/movi/';

  $sql = "SELECT *
        FROM PROD_CLIENTES_QR
        WHERE IMAGEN IS NULL LIMIT 30"; 
    var_dump($sql);
  $query = mysqli_query($conexion, $sql);
  while($result = mysqli_fetch_array($query)){
    $nameFile    = $result['CADENA_QR'];
    $nameImageQr = $pathCodes.$nameFile;
    $errorCorrectionLevel   = 'L'; 
    $matrixPointSize        = 4;
    
    QRcode::png($nameFile, $nameImageQr.'.png', $errorCorrectionLevel, $matrixPointSize, 2);   
    $string = $result['CADENA_QR'];

    $font   = 10;
    $width  = imagefontwidth($font) * strlen($string);
    $height = imagefontheight($font);

    $image = imagecreatetruecolor ($width,$height);
    $white = imagecolorallocate ($image,255,255,255);
    $black = imagecolorallocate ($image,0,0,0);
    imagefill($image,0,0,$white);

    imagestring ($image,$font,0,0,$string,$black);

    imagepng ($image,$pathCodes.'txt_'.$result['CADENA_QR'].'.png');
    imagedestroy($image);


            
    if(file_exists($nameImageQr.'.png')){    
      $updated =  setMarkCodigoQr($result['ID_QR'],$nameFile.'.png');
      if($updated){
        echo "se creo";
      }      
    }else{
      echo "no se pudo crear la imagen";
    }
    /*
    if(file_exists($pathReporte.$nameFile)){
      $updated =  setMarkReporte($result['ID_CITA']);
      if($updated){
        unlink($pathReporte.$nameFile);
      }
    }else{
      $updated =  setMarkReporte($result['ID_CITA']);
    }*/

  }  


  function setMarkCodigoQr($idOject,$nameImage){
    global $conexion;
    $result = false;
      $sql ="UPDATE PROD_CLIENTES_QR 
            SET  IMAGEN = '$nameImage'
            WHERE ID_QR = $idOject
            LIMIT 1";
    $query  = mysqli_query($conexion, $sql);
    if($query){
      $result= true;
    }
    return $result;   
  }  
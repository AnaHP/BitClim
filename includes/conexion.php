<?php
  #Valores tomados del archivo config/db.php
  # conectare la base de datos

  include_once 'config.php';

  $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){

        die("Imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }




    function desconectar($con){
      mysqli_close($con);
    }


    function ejecutaQuery($con, $consulta){
      $query = mysqli_query($con, $consulta) or die (mysqli_error($con));
      return $query;

    }



?>

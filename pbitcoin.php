<!--Tabla de clima de queretaro-->
<div class="col-lg-12 col-md-12">
  <div class="card">
    <div class="card-header card-header-warning">
      <h4 class="card-title"> &nbsp  Resultados</h4>
      <br>
      <br>
    </div>
    <div class="card-body table-responsive">

      <?php

              if(isset($_POST['submit'])){
                    $KvalorB = $_POST['KvalorB'];
                    $JvalorB = $_POST['JvalorB'];
                    $_SESSION['eleCK'] = $KvalorB;
                    $_SESSION['eleCJ'] = $JvalorB;

                    $MvariableB = $_POST['MvalorB'];
                    $AvariableB = $_POST['AvalorB'];
                    $variableSE2 = $_POST['suavizado2'];

                    $_SESSION['eleCM'] = $MvariableB;
                    $_SESSION['eleCAlfa'] = $AvariableB;
                    $_SESSION['eleCSE'] = $variableSE2;


                    $Consulta = "SELECT * FROM bitcoin";
                    //print($Consulta);

                    $rQuery= ejecutaQuery($con, $Consulta);

                    $n= mysqli_num_rows($rQuery);

                    //print("Numero de filas : ".$n);
                    for($f = 0; $f < $n+1; $f++){
                      $fila = mysqli_fetch_row($rQuery);
                        $Matriz[0][$f] = $fila[1];//año

                        $Matriz[1][$f] = $fila[2];//mes

                        $Matriz[2][$f] = $fila[3];//temp

                    }


                    /*Ponostico Promedio Simple*/

                    $Matriz[3][0] = "";
                    $Matriz[4][0] = "";

                    $acumulador = 0; //Suma de los valores reales anteriores

                    for ($f=1; $f < $n+1 ; $f++) {
                       //Calculo de PS
                       $acumulador = $acumulador + $Matriz[2][$f-1];
                       $ps = round(($acumulador/$f),2);
                       $Matriz[3][$f] = $ps;
                       //Error absolute
                       if($Matriz[2][$f] != ""){
                         $Matriz[4][$f] =abs($Matriz[3][$f]-$Matriz[2][$f]);
                       }else {
                         $Matriz[4][$f] = "";
                       }
                    }

                    /*Promedio Movil Simple*/

                    /* Filas con 0 Para PMS Y EPMS */
                    for ($i=0; $i <$KvalorB ; $i++) {
                      $Matriz[5][$i] = "";
                      $Matriz[6][$i] = "";
                    }
                    /*Valores que si van hacer calculados para K*/
                    $valor = 0;

                    for($z = $KvalorB; $z < $n+1; $z ++ ){
                     //$valor = (($Matriz[2][$z-1] + $Matriz[2][$z-2]));
                      for ($t=1; $t <= $KvalorB ; $t++) {
                          $valor += ($Matriz[2][$z-$t]);
                      }

                     $pms = round(($valor/$KvalorB), 2);
                     $Matriz[5][$z] = $pms;
                     /**Error PMS*/
                     if($Matriz[2][$z] != ""){
                       $Matriz[6][$z] = round(abs($Matriz[5][$z]-$Matriz[2][$z]),2);
                     }else {
                       $Matriz[6][$z] = "";
                     }

                    $valor=0;

                    }


                    /*Promedio Movil Doble*/
                    /* Filas con 0 Para PMD Y EPMD */
                    /*Valores con 0 para J*/
                    $sumaKJ = $KvalorB + $JvalorB;
                    for ($j=0; $j < $sumaKJ ; $j++) {
                      $Matriz[7][$j] = "";
                      $Matriz[8][$j] = "";
                    }
                    /*Valores calculados para J*/
                    $valor2 = 0;

                    for($q = $sumaKJ; $q < $n+1; $q ++ ){
                      //$valor2 = ($Matriz[5][$q-3] + $Matriz[5][$q-2] + $Matriz[5][$q-1]);

                      for ($v=1; $v <= $JvalorB ; $v++) {
                          $valor2 += ($Matriz[5][$q-$v]);
                      }

                      $pmd = round(($valor2 / $JvalorB),2);
                      /*Error Promedio Movil Doble*/
                      $Matriz[7][$q] = $pmd;
                      if($Matriz[2][$q] != ""){
                        $Matriz[8][$q] = abs($Matriz[7][$q]-$Matriz[2][$q]);
                      }else{
                        $Matriz[8][$q] = "";
                      }

                      $valor2 = 0;
                    }

                    /**Promedio Doble Ajustado*/
                    /**A = (2pms) - PMD
                      B = 2(|PMS - PMS|) / n -1
                    */




                    for ($g=0; $g < $sumaKJ ; $g++) {
                      $Matriz[9][$g] = "";
                      $Matriz[10][$g] = "";
                      $Matriz[11][$g] = "";
                      $Matriz[12][$g] = "";
                    }
                    for($w = $sumaKJ; $w < $n+1; $w ++ ){

                      $Matriz[9][$w] = round(2*($Matriz[5][$w])-$Matriz[7][$w] , 2);
                      $Matriz[10][$w] = round(abs(2*($Matriz[5][$w]-$Matriz[7][$w]) / 99),2);
                      $Matriz[11][$w] = $Matriz[9][$w] + $Matriz[10][$w] * $MvariableB;
                      /*Error Promedio Doble Ajustado*/
                      if($Matriz[2][$w] != ""){
                        $Matriz[12][$w] = round(abs($Matriz[11][$w]-$Matriz[2][$w]),2);
                      }else{
                        $Matriz[12][$w] = "";
                      }
                    }

                    /**TASA MEDIA DE CRECIMIENTO**/
                    $Matriz[13][0] = "";
                    $tmac = 0;


                    for($b = 1; $b < $n+1; $b ++ ){

                          $tmac = round(((($Matriz[2][$b] / $Matriz[2][$b-1]) - 1) * 100),2);
                          $Matriz[13][$b] = $tmac;

                          $tmac = 0;

                    }

                    /**PROMEDIO TASA MEDIA DE CRECIMIENTO**/
                    $Matriz[14][0] = "";
                    $Matriz[14][1] = "";

                     $Matriz[15][0] = "";
                     $Matriz[15][1] = "";
                    $ptmac = 0;


                    for($x = 2; $x < $n+1; $x ++ ){

                        $ptmac = $Matriz[2][$x-1] + ($Matriz[2][$x-1] * ($Matriz[13][$x-1] / 100)) ;
                        $Matriz[14][$x] = round($ptmac, 2);

                        $ptmac = 0;

                     /**Error PMS*/
                     if($Matriz[2][$x] != ""){
                       $Matriz[15][$x] = round(abs($Matriz[14][$x]-$Matriz[2][$x]),2);
                     }else {
                       $Matriz[15][$x] = "";
                     }

                   }


                   switch ($variableSE2) {
                       case 'SEPS':
                           //echo 'this is value1<br/>';
                           /*SUAVIZACION EXPONENCIAL PS**/
                           $ConstanteAlfa = $AvariableB;
                           $clave1=2;
                            for ($d=0; $d < $clave1 ; $d++) {
                              $Matriz[16][$d] = "";
                              $Matriz[17][$d] = "";

                            }

                           for ($e=$clave1; $e < $n+1 ; $e++) {
                             //Calculo de PS
                             $seps = $Matriz[3][$e-1] + ($ConstanteAlfa * ($Matriz[2][$e-1] - $Matriz[3][$e-1]));
                             $Matriz[16][$e] = round($seps,2);
                             //Error absolute
                             if($Matriz[2][$e] != ""){
                               $Matriz[17][$e] = abs($Matriz[16][$e]-$Matriz[2][$e]);
                             }else {
                               $Matriz[17][$e] = "";
                             }
                          }

                           break;
                       case 'SEPMS':
                           //echo 'value2<br/>';
                           /*SUAVIZACION EXPONENCIAL PMS*/
                          $ConstanteAlfa = $AvariableB;
                           for ($o=0; $o < $KvalorB+1 ; $o++) {
                             $Matriz[18][$o] = "";
                             $Matriz[19][$o] = "";

                           }

                          for ($m=$KvalorB+1; $m < $n+1 ; $m++) {
                            //Calculo de PS
                            $sepms = $Matriz[5][$m-1] + ($ConstanteAlfa * ($Matriz[2][$m-1] - $Matriz[5][$m-1]));
                            $Matriz[18][$m] = round($sepms,2);
                            //Error absolute
                            if($Matriz[2][$m] != ""){
                              $Matriz[19][$m] = abs($Matriz[18][$m]-$Matriz[2][$m]);
                            }else {
                              $Matriz[19][$m] = "";
                            }
                         }
                           break;
                       case 'SEPMD':
                            //echo 'value2<br/>';
                            /*SUAVIZACION EXPONENCIAL PMD*/
                            $ConstanteAlfa = $AvariableB;
                              for ($u=0; $u < $sumaKJ+1 ; $u++) {
                                $Matriz[20][$u] = "";
                                $Matriz[21][$u] = "";

                              }

                             for ($r=$sumaKJ+1; $r < $n+1 ; $r++) {
                               //Calculo de PS
                               $sepmd = $Matriz[7][$r-1] + ($ConstanteAlfa * ($Matriz[2][$r-1] - $Matriz[7][$r-1]));
                               $Matriz[20][$r] = $sepmd;
                               //Error absolute
                               if($Matriz[2][$r] != ""){
                                 $Matriz[21][$r] = abs($Matriz[20][$r]-$Matriz[2][$r]);
                               }else {
                                 $Matriz[21][$r] = "";
                               }
                            }
                            break;
                        case 'SEPDA':
                            //echo 'value2<br/>';
                            /*SUAVIZACION EXPONENCIAL PDA*/
                            $ConstanteAlfa = $AvariableB;
                           for ($a=0; $a < $sumaKJ+1 ; $a++) {
                             $Matriz[22][$a] = "";
                             $Matriz[23][$a] = "";

                           }

                          for ($ch=$sumaKJ+1; $ch < $n+1 ; $ch++) {
                            //Calculo de PS
                            $sepda = $Matriz[11][$ch-1] + ($ConstanteAlfa * ($Matriz[2][$ch-1] - $Matriz[11][$ch-1]));
                            $Matriz[22][$ch] = $sepda;
                            //Error absolute
                            if($Matriz[2][$ch] != ""){
                              $Matriz[23][$ch] = abs($Matriz[22][$ch]-$Matriz[2][$ch]);
                            }else {
                              $Matriz[23][$ch] = "";
                            }
                         }
                            break;
                        case 'SEPTMAC':

                            /*SUAVIZACION EXPONENCIAL PTMAC*/
                            $ConstanteAlfa = $AvariableB;
                           for ($jh=0; $jh < 3 ; $jh++) {
                             $Matriz[24][$jh] = "";
                             $Matriz[25][$jh] = "";

                           }

                          for ($va=3; $va < $n+1 ; $va++) {
                            //Calculo de PS
                            $septmac = $Matriz[14][$va-1] + ($ConstanteAlfa * ($Matriz[2][$va-1] - $Matriz[14][$va-1]));
                            $Matriz[24][$va] = $septmac;
                            //Error absolute
                            if($Matriz[2][$va] != ""){
                              $Matriz[25][$va] = abs($Matriz[24][$va]-$Matriz[2][$va]);
                            }else {
                              $Matriz[25][$va] = "";
                            }
                         }
                            break;
                          default:

                           break;
                   }


                  /*SUAVIZACION EXPONENCIAL PS**/
                  /*$ConstanteAlfa = $AvariableB;
                  $clave1=2;
                   for ($d=0; $d < $clave1 ; $d++) {
                     $Matriz[16][$d] = "";
                     $Matriz[17][$d] = "";

                   }

                  for ($e=$clave1; $e < $n+1 ; $e++) {
                    //Calculo de PS
                    $seps = $Matriz[3][$e-1] + ($ConstanteAlfa * ($Matriz[2][$e-1] - $Matriz[3][$e-1]));
                    $Matriz[16][$e] = round($seps,2);
                    //Error absolute
                    if($Matriz[2][$e] != ""){
                      $Matriz[17][$e] = abs($Matriz[16][$e]-$Matriz[2][$e]);
                    }else {
                      $Matriz[17][$e] = "";
                    }
                 }

                 /*SUAVIZACION EXPONENCIAL PMS*/

                 /*for ($o=0; $o < $KvalorB+1 ; $o++) {
                   $Matriz[18][$o] = "";
                   $Matriz[19][$o] = "";

                 }

                for ($m=$variableK+1; $m < $n+1 ; $m++) {
                  //Calculo de PS
                  $sepms = $Matriz[5][$m-1] + ($ConstanteAlfa * ($Matriz[2][$m-1] - $Matriz[5][$m-1]));
                  $Matriz[18][$m] = round($sepms,2);
                  //Error absolute
                  if($Matriz[2][$m] != ""){
                    $Matriz[19][$m] = abs($Matriz[18][$m]-$Matriz[2][$m]);
                  }else {
                    $Matriz[19][$m] = "";
                  }
               }

               /*SUAVIZACION EXPONENCIAL PMD*/

               /*for ($u=0; $u < $sumaKJ+1 ; $u++) {
                 $Matriz[20][$u] = "";
                 $Matriz[21][$u] = "";

               }

              for ($r=$sumaKJ+1; $r < $n+1 ; $r++) {
                //Calculo de PS
                $sepmd = $Matriz[7][$r-1] + ($ConstanteAlfa * ($Matriz[2][$r-1] - $Matriz[7][$r-1]));
                $Matriz[20][$r] = $sepmd;
                //Error absolute
                if($Matriz[2][$r] != ""){
                  $Matriz[21][$r] = abs($Matriz[20][$r]-$Matriz[2][$r]);
                }else {
                  $Matriz[21][$r] = "";
                }
             }

              /*SUAVIZACION EXPONENCIAL PDA*/

             /*for ($a=0; $a < $sumaKJ+1 ; $a++) {
               $Matriz[22][$a] = "";
               $Matriz[23][$a] = "";

             }

            for ($ch=$sumaKJ+1; $ch < $n+1 ; $ch++) {
              //Calculo de PS
              $sepda = $Matriz[11][$ch-1] + ($ConstanteAlfa * ($Matriz[2][$ch-1] - $Matriz[11][$ch-1]));
              $Matriz[22][$ch] = $sepda;
              //Error absolute
              if($Matriz[2][$ch] != ""){
                $Matriz[23][$ch] = abs($Matriz[22][$ch]-$Matriz[2][$ch]);
              }else {
                $Matriz[23][$ch] = "";
              }
           }

            /*SUAVIZACION EXPONENCIAL PTMAC*/

           /*for ($jh=0; $jh < 3 ; $jh++) {
             $Matriz[24][$jh] = "";
             $Matriz[25][$jh] = "";

           }

          for ($va=3; $va < $n+1 ; $va++) {
            //Calculo de PS
            $septmac = $Matriz[14][$va-1] + ($ConstanteAlfa * ($Matriz[2][$va-1] - $Matriz[14][$va-1]));
            $Matriz[24][$va] = $septmac;
            //Error absolute
            if($Matriz[2][$va] != ""){
              $Matriz[25][$va] = abs($Matriz[24][$va]-$Matriz[2][$va]);
            }else {
              $Matriz[25][$va] = "";
            }
         }*/






                    ?>
                    <!--Tabla de clima de queretaro-->
                    <div class="col-lg-12 col-md-12">
                      <div class="card">
                        <div class="card-body table-responsive">
                          <table id="example" class="table table-hover">
                            <thead class="text-warning" style="font-weight:bold;">
                              <th>ID</th>
                              <th>Año</th>
                              <th>Mes</th>
                              <th>Temp.</th>
                              <th>PS</th>
                              <th>EPS</th>
                               <th><?php print( " PMS ( Valor K = ".$KvalorB.")");  ?></th>
                              <th>EPMS</th>
                              <th> <?php print( "PMD ( Valor J = ".$JvalorB.")");  ?></th>
                              <th>EPMD</th>
                              <th>VALOR A</th>
                              <th>VALOR B</th>
                              <th>PDA</th>
                              <th>EPDA</th>
                              <th>TMAC</th>
                              <th>PTMAC</th>
                              <th>EPTMAC</th>
                              <th><?php print($variableSE2) ?></th>
                              <th>EA <?php print($variableSE2)?></th>
                              <!--<th>SE PMS</th>
                              <th>EA SE PMS</th>
                              <th>SE PMD</th>
                              <th>EA SE PMD</th>
                              <th>SE PDA</th>
                              <th>EA SE PDA</th>
                              <th>SE PTMAC</th>
                              <th>EA SE PTMAC</th>-->


                            </thead>
                            <tbody>"

                              <?php
                              for($f = 0; $f < mysqli_num_rows($rQuery)+1 ; $f++){
                                  echo "<tr>";
                                  echo"<td>".$f."</td>";
                                  echo"<td>".$Matriz[0][$f]."</td>";
                                  echo"<td>".$Matriz[1][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #fdc002; '>".$Matriz[2][$f]."</td>";
                                  echo"<td style='font-weight:bold; color: #04ce16 ;'>".$Matriz[3][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[4][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #5c03c0 ;'>".$Matriz[5][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[6][$f]."</td>";
                                  echo"<td style='font-weight:bold; color: #042cce ;'>".$Matriz[7][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[8][$f]."</td>";
                                  echo"<td>".$Matriz[9][$f]."</td>";
                                  echo"<td>".$Matriz[10][$f]."</td>";
                                  echo"<td style='font-weight:bold; color: #09dfcb ;'>".$Matriz[11][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[12][$f]."</td>";
                                  echo"<td>".$Matriz[13][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[14][$f]."</td>";
                                  echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[15][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[16][$f]."</td>";
                                  if ($variableSE2 == "SEPS") {
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[16][$f]."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[17][$f]."</td>";
                                  }elseif ($variableSE2 == "SEPMS") {
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[18][$f]."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[19][$f]."</td>";
                                  }elseif ($variableSE2 == "SEPMD") {
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[20][$f]."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[21][$f]."</td>";

                                  }elseif ($variableSE2 == "SEPDA") {
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[22][$f]."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[23][$f]."</td>";
                                  }elseif ($variableSE2 == "SEPTMAC") {
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[24][$f]."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[25][$f]."</td>";
                                  }else{
                                    echo"<td style='font-weight:bold; color:  #f50ace;'>"."</td>";
                                    echo"<td style='font-weight:bold; color:  #656363; '>"."</td>";
                                  }

                                  // echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[17][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[18][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[19][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[20][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[21][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[22][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[23][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #f50ace;'>".$Matriz[24][$f]."</td>";
                                  // echo"<td style='font-weight:bold; color:  #656363; '>".$Matriz[25][$f]."</td>";
                                  echo "</tr>";




                              }
                              ?>

                              <!--ERRORES PROMEDIOS FINALES-->
                              <tr style="font-weight: bold;">
                                <td></td>
                                <td>Promedio de Errores</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?php
                                    $errorAVGPS = 0;
                                    $cont1 = 0;
                                    for ($i=0; $i < $n+1 ; $i++) {
                                        if ($Matriz[4][$i] != "") {
                                          $errorAVGPS +=  (($Matriz[4][$i]));
                                          $cont1 ++;
                                        }
                                        $Matriz[4][$i] = "";
                                      }
                                      $EAVGproS = round(($errorAVGPS / $cont1),2);
                                ?>
                                <td><?php printf($EAVGproS);?></td>
                                <td></td>
                                <?php
                                    $errorAVGPMS = 0;
                                    $cont2 = 0;
                                    for ($i=0; $i < $n+1 ; $i++) {
                                        if ($Matriz[6][$i] != "") {
                                          $errorAVGPMS += (($Matriz[6][$i]));
                                          $cont2 ++;
                                        }
                                        $Matriz[6][$i] = "";
                                      }
                                      $EAVGproMS = round(($errorAVGPMS / $cont2),2);
                                ?>
                                <td><?php printf($EAVGproMS) ?></td>
                                <td></td>
                                <?php
                                    $errorAVGPMD = 0;
                                    $cont3 = 0;
                                    for ($i=0; $i < $n+1 ; $i++) {
                                        if ($Matriz[8][$i] != "") {
                                          $errorAVGPMD += (($Matriz[8][$i]));
                                          $cont3 ++;
                                        }
                                        $Matriz[8][$i] = "";
                                      }
                                       $EAVGproMD = round(($errorAVGPMD / $cont3),2);


                                ?>
                                <td><?php printf($EAVGproMD) ?></td>
                                <!--EPDA--->
                                <td></td>
                                <td></td>
                                <td></td>
                               <?php
                                   $errorAVGPDA = 0;
                                   $cont4 = 0;
                                   for ($i=0; $i < $n+1 ; $i++) {
                                       if ($Matriz[12][$i] != "") {
                                         $errorAVGPDA += (($Matriz[12][$i]));
                                         $cont4 ++;
                                       }
                                       $Matriz[12][$i] = "";
                                     }
                                      $EAVGproDA = round(($errorAVGPDA / $cont4),2);


                               ?>
                               <td><?php printf($EAVGproDA) ?></td>
                               <!--EPTMAC--->
                               <td></td>
                               <td></td>
                              <?php
                                  $errorAVGPTMAC = 0;
                                  $cont5 = 0;
                                  for ($i=0; $i < $n+1 ; $i++) {
                                      if ($Matriz[15][$i] != "") {
                                        $errorAVGPTMAC += (($Matriz[15][$i]));
                                        $cont5 ++;
                                      }
                                      $Matriz[15][$i] = "";
                                    }
                                     $EAVGproTMAC = round(($errorAVGPTMAC / $cont5),2);


                              ?>
                              <td><?php printf($EAVGproTMAC) ?></td>
                              <!--SE PS -->
                              <?php if ($variableSE2 == "SEPS") { ?>
                              <td></td>
                             <?php
                                 $errorAVGSEPS = 0;
                                 $cont6 = 0;
                                 for ($i=0; $i < $n+1 ; $i++) {
                                     if ($Matriz[17][$i] != "") {
                                       $errorAVGSEPS += (($Matriz[17][$i]));
                                       $cont6 ++;
                                     }
                                     $Matriz[17][$i] = "";
                                   }
                                    $EAVGprSEPS = round(($errorAVGSEPS / $cont6),2);
                             ?>
                             <td><?php printf($EAVGprSEPS) ?></td>
                           <?php   }elseif($variableSE2 == "SEPMS"){ ?>
                             <!--SE PMS-->
                             <td></td>
                            <?php
                                $errorAVGSEPMS = 0;
                                $cont7 = 0;
                                for ($i=0; $i < $n+1 ; $i++) {
                                    if ($Matriz[19][$i] != "") {
                                      $errorAVGSEPMS += (($Matriz[19][$i]));
                                      $cont7 ++;
                                    }
                                    $Matriz[19][$i] = "";
                                  }
                                   $EAVGprSEPMS = round(($errorAVGSEPMS / $cont7),2);
                            ?>
                            <td><?php printf($EAVGprSEPMS) ?></td>
                          <?php   }elseif($variableSE2 == "SEPMD"){ ?>
                            <!--SE PMD--->
                            <td></td>
                           <?php
                               $errorAVGSEPMD = 0;
                               $cont8 = 0;
                               for ($i=0; $i < $n+1 ; $i++) {
                                   if ($Matriz[21][$i] != "") {
                                     $errorAVGSEPMD += (($Matriz[21][$i]));
                                     $cont8++;
                                   }
                                   $Matriz[21][$i] = "";
                                 }
                                  $EAVGprSEPMD = round(($errorAVGSEPMD / $cont8),2);


                           ?>
                           <td><?php printf($EAVGprSEPMD) ?></td>
                         <?php   }elseif($variableSE2 == "SEPDA"){ ?>
                           <!--SE PDA-->
                           <td></td>
                          <?php
                              $errorAVGSEPDA = 0;
                              $cont9 = 0;
                              for ($i=0; $i < $n+1 ; $i++) {
                                  if ($Matriz[23][$i] != "") {
                                    $errorAVGSEPDA += (($Matriz[23][$i]));
                                    $cont9 ++;
                                  }
                                  $Matriz[23][$i] = "";
                                }
                                 $EAVGprSEPDA = round(($errorAVGSEPDA / $cont9),2);


                          ?>
                          <td><?php printf($EAVGprSEPDA) ?></td>
                        <?php   }elseif($variableSE2 == "SEPTMAC"){ ?>
                          <!-- SE PTMAC--->
                          <td></td>
                         <?php
                             $errorAVGSEPTMAC = 0;
                             $cont10 = 0;
                             for ($i=0; $i < $n+1 ; $i++) {
                                 if ($Matriz[25][$i] != "") {
                                   $errorAVGSEPTMAC += (($Matriz[25][$i]));
                                   $cont10 ++;
                                 }
                                 $Matriz[25][$i] = "";
                               }
                                $EAVGprSEPTMAC = round(($errorAVGSEPTMAC / $cont10),2);


                         ?>
                         <td><?php printf($EAVGprSEPTMAC) ?></td>
                       <?php   }else{ echo "Error"; }?>
                              </tr>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
              <!--End Table-->
              <!----->


            <?php } ?>
    </div>
  </div>

</div>
<!--End Tablas-->

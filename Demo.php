<?php
require_once("Equipos.class.php");

$e=new Equipos();
$e->crear(array("id"=>1,"so"=>1,"version"=>'1',"imei"=>'21423123123',"serial"=>'123123123',"centro"=>11,"est"=>11));


//Consulta multiple.
$expresion="WHERE `id`='".$id."'";
$db = new MySQL();
$sql ="SELECT * FROM `equipos` ".$expresion.";";
$consulta=$db->sql_query($sql);
while($fila =$db->sql_fetchrow($consulta)){
  //Cada ves que se repita el ciclo obtendre una nueva fila
  print_r($fila);
};
$db->sql_close();



//Retorna un vector que contiene vectores y cada vector contenido corresponde a un afila de los resultados.
function obtenerTerceros($centro) {
  $db = new MySQL();
  $sql = "SELECT * FROM `rondas`.`personal` WHERE `centro`='" . $centro . "';";
  $consulta = $db->sql_query($sql);
  while($row = $db->sql_fetchrow($consulta)){
    array_push($rows, $row);
  }
  $db->sql_close();
  return($rows);
}
      
print_r(obtenerTerceros("????")); 
//si es una funcion sola
//si esta en una clase seria
print_r($clase->obtenerTerceros("????")); 

      

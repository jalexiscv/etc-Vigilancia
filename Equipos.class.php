<?php
require_once("MySQL.class.php");

class equipos{
		function crear($datos){
			$db = new MySQL();
			$sql ="INSERT INTO `equipos` SET "
				."`id`='".$datos['id']."',"
				."`so`='".$datos['so']."',"
				."`version`='".$datos['version']."',"
				."`imei`='".$datos['imei']."',"
				."`serial`='".$datos['serial']."',"
				."`centro`='".$datos['centro']."',"
				."`est`='".$datos['est']."'"
				.";";
			$db->sql_query($sql);
			$db->sql_close();
		}

		function actualizar($id,$campo,$valor){
			$db = new MySQL();
			$sql ="UPDATE `equipos` "
				 ."SET `".$campo ."`='".$valor . "' "
				 ."WHERE `id`='".$id."';";
			$db->sql_query($sql);
			$db->sql_close();
		}
		function eliminar($id){
			$db = new MySQL();
			$sql ="DELETE FROM `equipos` "
				 ."WHERE `id`='".$id."';";
			$db->sql_query($sql);
			$db->sql_close();
		}
		function consultar($id){
			$db = new MySQL();
			$sql ="SELECT * FROM `equipos` "
				 ."WHERE `id`='".$id."';";
			$consulta=$db->sql_query($sql);
			$fila =$db->sql_fetchrow($consulta);
			$db->sql_close();
			return($fila);
		}
	}

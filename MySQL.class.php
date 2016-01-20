<?php

if (!class_exists('MySQL')) {

  class MySQL {

    var $db_connect_id;
    var $query_result;
    var $row = array();
    var $rowset = array();
    var $num_queries = 0;

    function normalizar($str) {
      return(strtoupper("'" . $str . "'"));
    }

    function normalizarvalor($valor, $tipo, $largo = 0, $definido = "") {
      $valor = (!get_magic_quotes_gpc()) ? addslashes($valor) : $valor;
      switch ($tipo) {
        case "varchar":$valor = (!empty($valor)) ? substr(strtoupper($valor), 0, intval($largo)) : "NULL";
          break;
        case "int":$valor = (!empty($valor)) ? intval($valor) : "NULL";
          break;
        case "tinyint":$valor = (!empty($valor)) ? intval($valor) : "NULL";
          break;
        case "double":$valor = ($valor != "") ? doubleval($valor) : "NULL";
          break;
        case "date":$valor = ($valor != "") ? $valor : "NULL";
          break;
        case "enum":$valor = (!empty($valor)) ? $valor : $definido;
          break;
      }return(strtoupper($valor));
    }

    function MySQL($persistency = true) {
      if ($persistency) {
        $this->db_connect_id = @mysql_pconnect($this->servidor(), $this->usuario(), $this->clave());
      } else {
        $this->db_connect_id = mysql_connect($this->servidor(), $this->usuario(), $this->clave());
      }if ($this->db_connect_id) {
        if ($this->db() != "") {
          $dbselect = mysql_select_db($this->db());
          if (!$dbselect) {
            mysql_close($this->db_connect_id);
            $this->db_connect_id = $dbselect;
          }
        } $this->sql_query("SET NAMES 'utf8'");
        return($this->db_connect_id);
      } else {
        return(false);
      }
    }

    function sql_close() {
      if ($this->db_connect_id) {
        if ($this->query_result) {
          @mysql_free_result($this->query_result);
        }$result = @mysql_close($this->db_connect_id);
        return $result;
      } else {
        return false;
      }
    }

    function sql_query($query = "", $transaction = false) {
      unset($this->query_result);
      if ($query != "") {
        $this->query_result = @mysql_query($query, $this->db_connect_id);
      }if ($this->query_result) {
        unset($this->row[$this->query_result]);
        unset($this->rowset[$this->query_result]);
        return $this->query_result;
      } else {
        return ($transaction == "END_TRANSACTION") ? true : false;
      }
    }

    function sql_numrows($query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        $result = mysql_num_rows($query_id);
        return $result;
      } else {
        return false;
      }
    }

    function sql_affectedrows() {
      if ($this->db_connect_id) {
        $result = mysql_affected_rows($this->db_connect_id);
        return $result;
      } else {
        return false;
      }
    }

    function sql_numfields($query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        $result = mysql_num_fields($query_id);
        return $result;
      } else {
        return false;
      }
    }

    function sql_fieldname($offset, $query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        $result = mysql_field_name($query_id, $offset);
        return $result;
      } else {
        return false;
      }
    }

    function sql_fieldtype($offset, $query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        $result = mysql_field_type($query_id, $offset);
        return $result;
      } else {
        return false;
      }
    }

    function sql_fetchrow($query_id = false) {
      global $cache;
      if ($query_id === false) {
        $query_id = $this->query_result;
      }if (isset($cache->sql_rowset[$query_id])) {
        return $cache->sql_fetchrow($query_id);
      }return ($query_id !== false) ? @mysql_fetch_assoc($query_id) : false;
    }

    function sql_fetchrowset($query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        unset($this->rowset[$query_id]);
        unset($this->row[$query_id]);
        while ($this->rowset[$query_id] = mysql_fetch_array($query_id)) {
          $result[] = $this->rowset[$query_id];
        }return $result;
      } else {
        return false;
      }
    }


    function sql_rowseek($rownum, $query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        $result = mysql_data_seek($query_id, $rownum);
        return $result;
      } else {
        return false;
      }
    }

    function sql_nextid() {
      if ($this->db_connect_id) {
        $result = mysql_insert_id($this->db_connect_id);
        return $result;
      } else {
        return false;
      }
    }

    function sql_freeresult($query_id = 0) {
      if (!$query_id) {
        $query_id = $this->query_result;
      }if ($query_id) {
        unset($this->row[$query_id]);
        unset($this->rowset[$query_id]);
        mysql_free_result($query_id);
        return true;
      } else {
        return(false);
      }
    }

    function sql_error($query_id = 0) {
      $result["message"] = mysql_error($this->db_connect_id);
      $result["code"] = mysql_errno($this->db_connect_id);
      return $result;
    }

    function sql_tablaexiste($tabla) {
      return((!$this->sql_query("DESCRIBE " . $tabla)) ? false : true);
    }

    function sql_campoexiste($tabla, $campo) {
      $consulta = $this->sql_query("SELECT DISTINCT '{$tabla}' AS 'conteo' FROM information_schema.COLUMNS WHERE COLUMN_NAME = '{$campo}';");
      $conteo = $this->sql_fetchrow($consulta);
      if (empty($conteo['conteo']) || $conteo['conteo'] == NULL) {
        return(false);
      } else {
        return(true);
      }
    }

    function sql_numeroderegistros($tabla) {
      $consulta = $this->sql_query("SELECT * FROM " . $tabla);
      return($this->sql_numrows($consulta));
    }

    function evaluar($vector, $dato) {
      if (isset($vector[$dato]) && !empty($vector[$dato])) {
        return("`" . $dato . "`='" . $vector[$dato] . "',");
      } else {
        return("");
      }
    }

    function version() {
      return("1.1.0");
    }

    function local() {
      rturn(true);
    }

    function servidor() {
        return("localhost");
    }

    function usuario() {
      return("rondas_root");
    }

    function clave() {
      return("Prona0129**");
    }

    function db() {
      return("rondas");
    }

  }

}
?>

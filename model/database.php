<?php
/**
 * Controlador DDBB Fusion BBDD Bibliográficas
 * Autor: Fernando Devís y otros.
 */

class database {
	
	protected static $instance = null;
	public static function getInstance(){
		if( is_null(self::$instance) ){
			try{
				$instance = new MysqliExt();
			}catch(Exception $e){
				throw $e;
			}
		}
		return $instance;
	}
	
	
	/**
     * Ejecuta una consulta de inserción.
     */
    public static function queryInsert($table_name, $fields) {
		$database 	= self::getInstance();
        $table_name = $database->qstr($table_name);
        $fields 	= $database->qstrArr($fields);

        $query 		= $database->getInsertSql($table_name, $fields);
        $result 	= $database->query($query);
        if ($result === FALSE) {
            return -1;
        }
        $id = $database->insert_id;
        return $id;
    }
	/**
     * Ejecuta una consulta de actualizacion.
     *
     */
    public static function queryUpdate($table_name, $fields, $key_fields) {
        $database 	= self::getInstance();
		
        $table_name = $database->qstr($table_name);
        $fields 	= $database->qstrArr($fields);
        $key_fields = $database->qstrArr($key_fields);
        
        $query 		= $database->getUpdateSql($table_name, $fields, $key_fields);
        $result 	= $database->query($query);
		
        return $result;
    }
	 /**
     * Ejecuta una consulta de eliminación.
     */
    public static function queryDelete($table_name, $key_fields) {
        $database 	= self::getInstance();
		
        $table_name = $database->qstr($table_name);
        $key_fields = $database->qstrArr($key_fields);
        
        $query 		= $database->getDeleteSql($table_name, $key_fields);
        $result 	= $database->query($query);
		
        return $result;
    }
    /**
     * Quita los puntos que aparecen en los títulos (labores de limpieza)
     */
    public function quitaPuntos($table_name) {
        $database 	= self::getInstance();
        $query      = "UPDATE `$table_name` SET titulo = SUBSTRING(titulo, 1, CHAR_LENGTH(titulo) - 1) WHERE titulo LIKE '%.'";
        $result     = $database->query($query);

        return $result;
    }
    /**
     * Quita las comas que aparecen en los autores en caso de no ser necesarias (labores de limpieza)
     */
    public function quitaComas($table_name) {
        $database 	= self::getInstance();
        $query      = "UPDATE `$table_name` SET otros_aut = SUBSTRING(otros_aut, 2) WHERE otros_aut LIKE ',%'";
        $result     = $database->query($query);

        return $result;
    }

    /**
     * Inserta los resultados de la fusión para dblp
     */
    public function creaFusion_dblp() {
        $database 	= self::getInstance();
        $query      = "INSERT INTO fusion_db (autor, titulo, fecha_pub, publicado_en, otros_aut, tipo_pub, doi, url, pages, volume, bdorigen)
                        SELECT autor, titulo, fecha_pub, publicado_en, otros_aut, tipo_pub, doi, url, pages, volume, bdorigen
                        FROM dblp_results";
        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta los resultados de la fusión para gs
     */
    public function creaFusion_gs() {
        $database 	= self::getInstance();
        $query      = "INSERT INTO fusion_db (autor, titulo, fecha_pub, otros_aut, publicado_en, url_detalle, num_citaciones)
                        SELECT autor, titulo, fecha_pub, otros_aut, publicado_en, url_detalle, num_citaciones
                        FROM gs_results";
        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta los resultados de la fusión para el primer resultado de la fusión
     * Nos ayudamos de la sentencia distinct para no incluir los duplicados
     */
    public function insertaResultados() {
        $database 	= self::getInstance();
        $query      = "INSERT INTO resultado_db	(titulo) SELECT DISTINCT titulo FROM fusion_db";

        $result     = $database->query($query);

        return $result;
    }

    /**
     * Inserta los autores en la tabla de resultados, en caso de que no existan
     */
    public function insertaAutor($autor_name) {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db SET autor = '$autor_name'";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta las fechas en la tabla de GS, en caso de que no existan
     */
    public function insertaFechaGS() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN gs_results AS GS ON RES.titulo=GS.titulo SET RES.fecha_pub=GS.fecha_pub WHERE RES.fecha_pub IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta las fechas en la tabla de dblp, en caso de que no existan
     */
    public function insertaFechaDBLP() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.fecha_pub=DB.fecha_pub WHERE RES.fecha_pub IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta las fechas 0000 en la tabla de resultados, para no tener valores nulos
     * que perjudiquen los filtros
     */
    public function insertaFecha0000() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db SET fecha_pub='0000' WHERE fecha_pub IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta las publicaciones n/d  tabla de resultados, para no tener valores nulos
     * que perjudiquen los filtros
     */
    public function insertaND() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db SET tipo_pub='n/d' WHERE tipo_pub IS NULL";

        $result     = $database->query($query);

        return $result;
    }

    /**
     * Inserta donde se ha publiado en la tabla de gs, en caso de que no existan
     */
    public function insertaPublicadoEnGS() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN gs_results AS GS ON RES.titulo=GS.titulo SET RES.publicado_en=GS.publicado_en WHERE RES.publicado_en IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta donde se ha publiado en la tabla de dblp, en caso de que no existan
     */
    public function insertaPublicadoEnDBLP() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.publicado_en=DB.publicado_en WHERE RES.publicado_en IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta los autores en la tabla de gs, en caso de que no existan
     */
    public function insertaOtrosAutGS() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN gs_results AS GS ON RES.titulo=GS.titulo SET RES.otros_aut=GS.otros_aut WHERE RES.otros_aut IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta los autores en la tabla de dblp, en caso de que no existan
     */
    public function insertaOtrosAutDBLP() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.otros_aut=DB.otros_aut WHERE RES.otros_aut IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta el tipo de publicacion en la tabla de resultados, en caso de que no existan
     */
    public function insertaTipoPub() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.tipo_pub=DB.tipo_pub WHERE RES.tipo_pub IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta el doi en la tabla de resultados, en caso de que no existan
     */
    public function insertaDOI() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.doi=DB.doi WHERE RES.doi IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta la numeración en la tabla de resultados, en caso de que no existan
     */
    public function insertaPages() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.pages=DB.pages WHERE RES.pages IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta la URL en la tabla de resultados, en caso de que no existan
     */
    public function insertaURL() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.url=DB.url WHERE RES.url IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta el volumen en la tabla de resultados, en caso de que no existan
     */
    public function insertaVolume() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN dblp_results AS DB ON RES.titulo=DB.titulo SET RES.volume=DB.volume WHERE RES.volume IS NULL";

        $result     = $database->query($query);

        return $result;
    }

    /**
     * Inserta el número de citaciones en la tabla de resultados, en caso de que no existan
     */
    public function insertaNumCitaciones() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN gs_results AS GS ON RES.titulo=GS.titulo SET RES.num_citaciones=GS.num_citaciones WHERE RES.num_citaciones IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Inserta los detalles de la URL en la tabla de resultados, en caso de que no existan
     */
    public function insertaURLdetalle() {
        $database 	= self::getInstance();
        $query      = "UPDATE resultado_db as RES LEFT JOIN gs_results AS GS ON RES.titulo=GS.titulo SET RES.url_detalle=GS.url_detalle WHERE RES.url_detalle IS NULL";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Vacia la tabla temporal de resultados
     */
    public function limpiarTablaTemporal() {
        $database 	= self::getInstance();
        $query      = "TRUNCATE TABLE resultado_temp";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Vacia la tabla temporal de resultados de organizaciones
     */
    public function limpiarTablaTemporal_org() {
        $database 	= self::getInstance();
        $query      = "TRUNCATE TABLE resultado_org_temp";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Copia la tabla de resultados en la tabla temporal
     */
    public function copiarTablaTemporal() {
        $database 	= self::getInstance();
        $query      = "INSERT INTO resultado_temp SELECT * FROM resultado_db";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Agrega la organización en la tabla de resultados
     */
    public function agregarOrganizaciones($org) {
        $database 	= self::getInstance();
        $query      = "INSERT INTO resultado_org (autor, titulo,organizacion,fecha_pub,otros_aut,publicado_en,volume,pages,tipo_pub,doi,num_citaciones,url,url_detalle) SELECT autor, titulo,'$org',fecha_pub,otros_aut,publicado_en,volume,pages,tipo_pub,doi,num_citaciones,url,url_detalle FROM resultado_temp";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Borra la tabla de la fusión principal inicial
     */
    public function limpiarTablaFusion() {
        $database 	= self::getInstance();
        $query      = "TRUNCATE TABLE fusion_db";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Borra la tabla de los resultados
     */
    public function limpiarTablaResultados() {
        $database 	= self::getInstance();
        $query      = "TRUNCATE TABLE resultado_db";

        $result     = $database->query($query);

        return $result;
    }
    /**
     * Borra la tabla de los resultados de las organizaciones
     */
    public function purgarOrganizaciones() {
        $database 	= self::getInstance();
        $query      = "TRUNCATE TABLE resultado_org";

        $result     = $database->query($query);

        return $result;
    }


    /**
     * Funciones para poder exportar a los diferentes formatos
     */
    public function exportaCSV($tablename) {
        $database 	= self::getInstance();
        $query      = "SELECT *  FROM `$tablename` ";
        $result     = $database->query($query);
        ob_end_clean(); // Para no incluir resto de HTML
        $delimiter  = (chr(9));
        $filename       = "../resultados.csv";
        $f = fopen('php://memory', 'w') or die("Imposible abrir el fichero");;
        $fields = array('id','titulo','fecha','otrosaut','publicacion','tipo','doi','citaciones');
        fputcsv($f,$fields,$delimiter);
        while ($row = $result->fetch_assoc()) {
            $linedata = array($row['id'],$row['titulo'],$row['fecha_pub'],$row['otros_aut'],$row['publicado_en'],$row['tipo_pub'],$row['doi'],$row['num_citaciones']);
            fputcsv($f, array_map('utf8_decode',array_values($linedata)), $delimiter);
        }
        fseek($f,0);
        header("Content-Type: text/csv");
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        echo "\xEF\xBB\xBF";  // BOM header UTF-8
        fpassthru($f);
        exit();
    }


    public function exportaBIBTEX($tablename) {
        $database 	= self::getInstance();
        $query      = "SELECT *  FROM `$tablename` ";
        $result     = $database->query($query);
        ob_end_clean(); // Para no incluir resto de HTML
        $file       = "../resultados.bib";
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        while ($bib_row = $result->fetch_array() ) {
            $citekey=str_replace(' ','-',$bib_row[2]);
            echo '@'.$bib_row[7].'{'.$citekey[0].$bib_row[4].',
            author={'.$bib_row[6].'},
            title={'.$bib_row[3].'},
            journal={'.$bib_row[5].'},
            volume={'.$bib_row[11].'},
            pages={'.$bib_row[10].'},
            url={'.$bib_row[9].'},
            doi={'.$bib_row[8].'},
            biburl={'.$bib_row[12].'},
            year={'.$bib_row[4].'}}';
        }
        die();
        return $result;
    }

    public function exportaENDNOTE() { // Al ser un formato propietario, el mas indicado para las importaciones es el XML
        $database 	= self::getInstance();
        $query      = "SELECT *  FROM resultado_temp";
        $result     = $database->query($query);
        ob_end_clean(); // Para no incluir resto de HTML
        $file       = "../resultados.xml";
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');

        while ($xml_row = $result->fetch_array() ) {
            echo '<record><contributors><authors><author><style face="normal" font="default" size="100%">'.$xml_row[5].'</style></author></authors></contributors><titles><title><style face="normal" font="default" size="100%">'.$xml_row[2].'</style></title><secondary-title><style face="normal" font="default" size="100%">'.$xml_row[4].'</style></secondary-title></titles><periodical><full-title><style face="normal" font="default" size="100%">'.$xml_row[4].'</style></full-title></periodical><pages><style face="normal" font="default" size="100%">'.$xml_row[9].'</style></pages><volume><style face="normal" font="default" size="100%">'.$xml_row[10].'</style></volume><dates><year><style face="normal" font="default" size="100%">'.$xml_row[3].'</style></year></dates><urls><related-urls><url><style face="normal" font="default" size="100%">'.$xml_row[8].'</style></url></related-urls></urls><electronic-resource-num><style face="normal" font="default" size="100%">'.$xml_row[7].'</style></electronic-resource-num></record>';
        }
        die();
        return $result;
    }
    /**
     * Función auxiliar para contar resultados
     */
    public function cuentaResultados($table_name) {
        $database 	= self::getInstance();
        $query      = "SELECT COUNT(*) FROM `$table_name` ";
        $result     = $database->query($query);
        $num_gs = mysqli_fetch_array($result);

        return $num_gs[0];
    }

}



class MysqliExt extends mysqli {

	function __construct() {
		
		// Conexión remota.
		parent::__construct('localhost', 'root', '', 'fusion_db');


        if (mysqli_connect_error()) {
			throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
        }
		$this->set_charset("utf8");
    }
	
	function query($query, $result_mode = NULL){
        $result = parent::query($query);
		
        if( !$result ){
			throw new Exception($this->error, $this->errno);
        }
        return $result;
    }
	
	/**
     * Escapa los caracteres especiales de una cadena para usarla en una
     * sentencia SQL, tomando en cuenta el conjunto de caracteres actual de
     * la conexión.
     * @param mixed $str String o Array.
     * @return String
     */
    public function qstr($str) {
        // El if es por si es un array.
        if (is_array($str)) {
            return $this->qstrArr($str);
        }
        if (is_null($str))
            return NULL;
        return "{$this->real_escape_string($str)}";
    }
    /**
     * Escapa los caracteres especiales de un array para usarla en una
     * sentencia SQL, tomando en cuenta el conjunto de caracteres actual de
     * la conexión.
     * @param Array $arr
     * @return type
     */
    public function qstrArr($arr) {
		if (!is_array($arr)) {
			return $this->qstr($arr);
		}
        foreach ($arr as $key => $value) {
            $arr[$key] = $this->qstr($value);
        }
        return $arr;
    }
	
	/**
     * Crea la SQL de una inserción.
     * @param String $table_name Nombre de la tabla.
     * @param Array $fields Campos y valores.
     * @return String SQL de inserción.
     */
    public function getInsertSql($table_name, $fields) {
        
        $keys_fields = array_keys($fields);
        $values_fields = array_values($fields);
        $count = count($fields);

        for ($i = 0; $i < $count; $i++) {
            $keys[$i] = "`" . $keys_fields[$i] . "`";
            if (is_null($values_fields[$i])) {
                $values[$i] = "NULL";
            } else {
                $values[$i] = "'" . $values_fields[$i] . "'";
            }
        }

        $keys = implode(", ", $keys);
        $values = implode(", ", $values);

        return "INSERT INTO `$table_name` ($keys) VALUES ($values);";
    }
    
	/**
     * Crea la SQL de la actualización.
     * @param String $table_name Nombre de la tabla.
     * @param Array $fields Campos y valores.
     * @param Array $key_fields Campos y valores del identificador.
     * @return String SQL de actualización.
     */
    public function getUpdateSql($table_name, $fields, $key_fields) {
        
        $update = array();
        foreach ($fields as $key => $value) {
			if( is_null($value) ){
				$update[] = "`" . $key . "`=null";
			}else{
				$update[] = "`" . $key . "`='" . $value . "'";
			}
        }

        $update_sql = implode(", ", $update);
		
		$update_keys = array();
        foreach ($key_fields as $key => $value) {
			$update_keys[] = "`" . $key . "`='" . $value . "'";
        }

        $update_keys_sql = implode(" AND ", $update_keys);
		

        return "UPDATE `$table_name` SET $update_sql WHERE $update_keys_sql";
    }




	/**
     * Crea la SQL de eliminación.
     * @param String $table_name Nombre de la tabla.
     * @param Array $key_fields Campos y valores del identificador.
     * @return String SQL de eliminación.
     */
    public function getDeleteSql($table_name, $key_fields) {
        
		$delete_keys = array();
        foreach ($key_fields as $key => $value) {
            $delete_keys[] = "`" . $key . "`='" . $value . "'";
        }

        $delete_keys_sql = implode(" AND ", $delete_keys);
		
        return "DELETE FROM `$table_name` WHERE $delete_keys_sql";
    }
	
}

?>
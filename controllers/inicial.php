<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fusión de Bases de Datos Bibliográficas</title>
    <!-- Inclusión de las librerías para Bootstrap -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>


<?php
include_once("../model/database.php"); // Archivos necesarios para el funcionamiento
include_once("../includes/estructura.php"); // de la base de datos
include '../controllers/simple_html_dom.php'; // y parser para HTML en Scholar.

/** Limpiamos todas las BBDD excepto la de organizaciones
 * ya que es la única a la que le damos persistencia total
 */

$database = database::getInstance();
$limpieza_dblp = $database->query("TRUNCATE TABLE dblp_results");
$limpieza_gs = $database->query("TRUNCATE TABLE gs_results");
$limpieza_fusion = $database->query("TRUNCATE TABLE fusion_db");
$limpieza_db = $database->query("TRUNCATE TABLE resultado_db");
$limpieza_temp = $database->query("TRUNCATE TABLE resultado_temp");

/**
 * Obtención de bibliografías mediante
 * búsqueda única por autor.
 */

if(isset($_POST['inputAutor'])) {
    $autorForm=urlencode($_POST['inputAutor']);
    $url_dblp_buscaAutor = 'https://dblp.org/search/author/api?q='."$autorForm".'&h=1000&format=xml'; // Buscamos la URL en formato XML del autor en DBLP
    $url_gs_buscaAutor = 'https://scholar.google.es/citations?view_op=search_authors&mauthors='."$autorForm".'&hl=es&oi=ao'; // Ídem pero en HTML para Google Scholar

    /**
     * PORCION CODIGO DBLP usando xml parser
     * ya que DBLP dispone de archivos en formato XML
     */
    $html_dblp_autor = file_get_html($url_dblp_buscaAutor);
    $xml_dblp_autor=simplexml_load_file($url_dblp_buscaAutor);
    $url_dblp=$xml_dblp_autor->hits->hit->info->url.'.xml';
    $html_dblp = file_get_html($url_dblp);
    $xml_dblp=simplexml_load_string($html_dblp);
    $publicacion_db['autor']=$xml_dblp['name'];
    $publicacion_db_alt['autor']=$xml_dblp['name'];

    /**
     * PORCION CODIGO Google Scholar usando parseo
     * directo por HTML, ya que no existe versión XML en GS.
     * Para ello usamos la librería externa "simple_html_dom"
     */
    $html_autor_gs = file_get_html($url_gs_buscaAutor);
    $IdPerfil_autor_gs = $html_autor_gs->find('#gsc_sa_ccl',0)->outertext; // Función find de la librería para parsear el autor.
    echo '</br>';

    /**
     * Hacemos uso del modelo DOM en PHP para extraer elementos
     * del HTML obtenido del autor en Google Scholar
     * Básicamente en esta porción vamos a obtener el id incluido en la URL del autor
     * P. ej.: Para Yann Lecun
     * https://scholar.google.es/citations?user=WLN3QrAAAAAJ&hl=es&oi=ao
     * Necesitamos obtener el id, que en este caso es: WLN3QrAAAAAJ
     * desde la web de resultados:
     * https://scholar.google.es/scholar?hl=es&as_sdt=0%2C5&q=yann+lecun&btnG=&oq=yan
     */
    $htmlDom = new DOMDocument;
    @$htmlDom->loadHTML($IdPerfil_autor_gs); // Obtención de variable silenciando errores
    $links = $htmlDom->getElementsByTagName('a'); // obtención de nodos "a" (anchor links) desde el HTML
    $extractedLinks = array();
    foreach($links as $link){
        $linkText = $link->nodeValue;
        $linkHref = $link->getAttribute('href'); // Obtenemos el contenido del HREF
        if(strlen(trim($linkHref)) == 0){ // Ignoramos si está vacío....
            continue;
        }
        if($linkHref[0] == '#'){ //..o es un ancla simple sin ser link
            continue;
        }
        $extractedLinks[] = array(
            'href' => $linkHref
        );
    }

    $url_gs_id =  ($extractedLinks[0]['href']); // Finalmente, solo nos interesa el primer valor del link, que nos da la dirección en Google Scholar del autor
    $url_gs='https://scholar.google.es'.$url_gs_id.'&hl=&view_op=list_works&pagesize=100'; // Así obtenemos el html final del autor (y con 100 resultados)
    $html_gs = file_get_html($url_gs);
    $resultados_gs = $html_gs->find('#gsc_a_t',0);
    try{
        $publicacion_gs['autor']=$html_gs->find('#gsc_prf_in',0)->plaintext;
    }catch(Exception $e){
        $error = true;
        header("Location: index.php?alert=primary&info=".urlencode($e->getMessage()));
    }

    /**
     * Obtención de bibliografías mediante
     * inclusión de las URLS
     */
} else if (isset($_POST['inputURL_GS']) && ($_POST['inputURL_DBLP'])) {
    $url_gs   = ($_POST['inputURL_GS']);
    $url_dblp = ($_POST['inputURL_DBLP']);
    if (stripos($url_dblp, "html") !== false) {
        $url_dblp = str_replace("html","xml",$url_dblp);
    } else {
        $url_dblp = $url_dblp.'.xml'; // Convertimos la URL terminada en HTML de DBLP en la equivalente a XML para parsearla de manera mas cómoda.
    }
    $html_dblp = file_get_html($url_dblp);
    $xml_dblp=simplexml_load_string($html_dblp);
    $publicacion_db['autor']=$xml_dblp['name'];
    $publicacion_db_alt['autor']=$xml_dblp['name'];


    $html_gs = file_get_html($url_gs);
    $resultados_gs = $html_gs->find('#gsc_a_t',0);
    $publicacion_gs['autor']=$html_gs->find('#gsc_prf_in',0)->plaintext;

}
 foreach ($xml_dblp as $row_db) {
         $publicacion_db['tipo_pub'] = $row_db->children()->getName();
         if ($publicacion_db['tipo_pub'] == 'article' ||
             $publicacion_db['tipo_pub'] == 'book' ||
             $publicacion_db['tipo_pub'] == 'booklet' ||
             $publicacion_db['tipo_pub'] == 'conference' ||
             $publicacion_db['tipo_pub'] == 'inbook' ||
             $publicacion_db['tipo_pub'] == 'incollection' ||
             $publicacion_db['tipo_pub'] == 'inproceedings' ||
             $publicacion_db['tipo_pub'] == 'manual' ||
             $publicacion_db['tipo_pub'] == 'masterthesis' ||
             $publicacion_db['tipo_pub'] == 'misc' ||
             $publicacion_db['tipo_pub'] == 'phdthesis' ||
             $publicacion_db['tipo_pub'] == 'proceedings' ||
             $publicacion_db['tipo_pub'] == 'techreport' ||
             $publicacion_db['tipo_pub'] == 'unpublished')
         {
                 $publicacion_db['titulo'] = $row_db->xpath($publicacion_db['tipo_pub']."/title")[0];
                 $publicacion_db['otros_aut'] = "";
                 $publicacion_db['bdorigen'] = 'DBLP';
                 $publicacion_db['fecha_pub'] = $row_db->xpath($publicacion_db['tipo_pub']."/year")[0];
                 if (isset($row_db->xpath($publicacion_db['tipo_pub']."/booktitle")[0])) // Evitamos "Undefined offset en caso de no existir el elemento del array
                 {
                     $publicacion_db['publicado_en'] = $row_db->xpath($publicacion_db['tipo_pub']."/booktitle")[0];
                 }
                 if (!isset($publicacion_db['publicado_en'])) {
                     $publicacion_db['publicado_en'] = $row_db->xpath($publicacion_db['tipo_pub']."/journal")[0];
                    } // Usamos el mismo campo para el origen de cualquier tipo de publicación
                 if (isset($row_db->xpath($publicacion_db['tipo_pub']."/ee")[0])) {
                    $publicacion_db['doi'] = basename($row_db->xpath($publicacion_db['tipo_pub']."/ee")[0]);
                    }
                 foreach ($row_db->xpath($publicacion_db['tipo_pub']."/author") as $auth) {
                     $publicacion_db['otros_aut'] = $publicacion_db['otros_aut'] . "," . $auth; //Concatenamos otros autores
                    }
                 if (isset($row_db->xpath($publicacion_db['tipo_pub']."/ee")[0])) {
                     $publicacion_db['url'] =$row_db->xpath($publicacion_db['tipo_pub']."/ee")[0];
                 }
                 if (isset($row_db->xpath($publicacion_db['tipo_pub']."/pages")[0])) {
                     $publicacion_db['pages'] =$row_db->xpath($publicacion_db['tipo_pub']."/pages")[0];
                 }
                 if (isset($row_db->xpath($publicacion_db['tipo_pub']."/volume")[0]))
                 {
                     $publicacion_db['volume'] =$row_db->xpath($publicacion_db['tipo_pub']."/volume")[0];
                 }

                 database::queryInsert("dblp_results", $publicacion_db);
           //    database::queryInsert("fusion_db", $publicacion_db);

     }
 }


$database = database::getInstance();
$result_dblp = $database->query("SELECT * FROM dblp_results");


set_time_limit(0);
$data = json_decode(file_get_contents('php://input'),true);
$page = 1;
$offset = ($page - 1)* 100;
$cStart = 0+$offset;

if (!empty($resultados_gs)) {
    foreach ($resultados_gs->find('tr.gsc_a_tr') as $row) {
        $papertitulo = $row->find('td.gsc_a_t a', 0)->plaintext;
        $publicacion_gs['titulo'] = $papertitulo;
        $publicacion_gs['bdorigen'] = 'Google Scholar';
        $cited = $row->find('td.gsc_a_c', 0)->plaintext;
        $url_detalle = $row->find('td.gsc_a_t a', 0)->outertext;
        $id_detalle_url = substr($url_detalle, 74, 12);
        $pub_detalle_url = substr($url_detalle, 139, 12);
        $publicacion_gs['url_detalle'] = 'https://scholar.google.es/citations?view_op=view_citation&hl=es&user=' . $id_detalle_url . '&sortby=pubdate&citation_for_view=' . $id_detalle_url . ':' . $pub_detalle_url;

        /*
        --PESE A QUE FUNCIONA CORRECTAMENTE, LO EVITO DEBIDO AL ERROR HTTP/1.0 429 Too Many Requests QUE ARROJA GOOGLE SCHOLLAR
        $html_gs_detalle = file_get_html($publicacion_gs['url_detalle']);
        $resultados_gs_detalle = $html_gs_detalle->find('#gsc_oci_descr',0)->outertext;
        $publicacion_gs['descripcion'] = $resultados_gs_detalle; */


        if ($cited === '') {
            $cited = 0;
        }
        $publicacion_gs['fecha_pub'] = $row->find('td.gsc_a_y', 0)->plaintext;
        if ($publicacion_gs['fecha_pub'] === ' ') {
            $publicacion_gs['fecha_pub'] = 'n/a';
        }
        $publicacion_gs['otros_aut'] = $row->find('td.gsc_a_t .gs_gray', 0)->plaintext;
        if ($publicacion_gs['otros_aut'] === '') {
            $publicacion_gs['otros_aut'] = 'n/a';
        }
        $publicacion_gs['publicado_en'] = $row->find('td.gsc_a_t .gs_gray', 1)->plaintext;
        if ($publicacion_gs['publicado_en'] === '') {
            $publicacion_gs['publicado_en'] = 'n/a';
        }
        $cited = preg_replace('/[\*]+/', '', $cited);
        $publicacion_gs['num_citaciones'] = $cited;

        database::queryInsert("gs_results", $publicacion_gs);
        //   database::queryInsert("fusion_db", $publicacion_gs);
    }
}

// Realizamos algunas operaciones de "limpieza de caracteres" en la base de datos
$result_gs = $database->query("SELECT * FROM gs_results");
database::quitaPuntos("gs_results");
database::quitaPuntos("dblp_results");
database::quitaComas("dblp_results");
database::quitaPuntos("fusion_db");
database::quitaComas("fusion_db");

include_once("../view/vista_inicial.php");
?>





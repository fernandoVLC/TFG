<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fusión de Bases de Datos Bibliográficas</title>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<?php
    include("../includes/estructura.php");
    include_once("../model/database.php");
    include_once("../model/conexion_basica.php");
    $fusion = filter_input(INPUT_GET, 'fusion');
    $database = database::getInstance();
    if (isset($fusion)) {
        database::limpiarTablaFusion();
        database::limpiarTablaResultados();
        database::creaFusion_dblp();
        database::creaFusion_gs();
        database::insertaResultados();
        database::insertaAutor($autor['autor']);
        database::insertafechaGS();
        database::insertaFecha0000(); // Inserta fechas como 0000 para valores nulos, facilita la búsqueda
        database::insertafechaDBLP();
        database::insertaPublicadoEnGS();
        database::insertaPublicadoEnDBLP();
        database::insertaOtrosAutGS();
        database::insertaOtrosAutDBLP();
        database::insertaTipoPub(); // Solo DBLP
        database::insertaND();
        database::insertaDOI(); // Solo DBLP
        database::insertaNumCitaciones(); // Solo GS
        database::insertaPages(); //Sólo DBLP
        database::insertaURL(); //Sólo DBLP
        database::insertaVolume(); // Sólo DBLP
        database::insertaURLdetalle(); // Solo GS
        database::limpiarTablaTemporal();
        database::copiarTablaTemporal(); // Creamos la tabla auxiliar idéntica a los resultados
        database::quitaComas('resultado_temp');
    }

    // Código para poder ordenar resultados en HTML y realizar búsquedas
    $columns = array('id','titulo','fecha_pub','otros_aut','publicado_en','tipo_pub','doi','num_citaciones');
    $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
    $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';
    $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order);
    $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
    $add_class = ' class="highlight"';

    if(isset($_POST['busca_titulo'])
        || isset($_POST['busca_autores'])
        || isset($_POST['busca_desde'])&&($_POST['busca_hasta'])
        || isset($_POST['busca_autores'])
      ) {
        $busca_titulo_boton=urlencode($_POST['busca_titulo']);
        $busca_autores_boton=urlencode($_POST['busca_autores']);
        $busca_desde_boton=urlencode($_POST['busca_desde']);
        $busca_hasta_boton=urlencode($_POST['busca_hasta']);
        $busca_publi_boton=urlencode($_POST['busca_publi']);
        database::limpiarTablaTemporal();
        $result_final = $database->query("INSERT INTO resultado_temp SELECT * FROM resultado_db WHERE titulo LIKE '%".$busca_titulo_boton."%' and 
                                                otros_aut LIKE '%".$busca_autores_boton."%' and
                                                (fecha_pub>='".$busca_desde_boton."' AND fecha_pub<='".$busca_hasta_boton."')and
                                                (tipo_pub LIKE '%".$busca_publi_boton."%' or tipo_pub IS NULL)                                                                                               
                                                ");
        $result_final = $database->query('SELECT * FROM resultado_temp ORDER BY ' . $column . ' '.$sort_order);
    } else {
        $result_final = $database->query('SELECT * FROM resultado_temp ORDER BY ' . $column . ' '.$sort_order);
    }

    // Llamada desde el botón "eliminar" para eliminar el registro de la tabla
    $ref_del = filter_input(INPUT_GET, 'id_del');
    if(isset($ref_del)){
        $error = false;
        try{
            database::queryDelete("resultado_temp", array("id"=>$ref_del));
            database::queryDelete("resultado_db", array("id"=>$ref_del));
        }catch(Exception $e){
            $error = true;
            header("Location: resultados.php?alert=primary&info=".urlencode($e->getMessage()));
        }
        if( !$error ){
            header("Location: resultados.php?alert=danger&info=Eliminación realizada con éxito.");
        }
    }

    // Llamadas desde los botones para cada uno de los tipos de exportación soportados
    $export_csv = filter_input(INPUT_GET, 'csv');
    if(isset($export_csv)){
         database::exportaCSV("resultado_temp");
        }

    $export_bib = filter_input(INPUT_GET, 'bib');
    if(isset($export_bib)){
         database::exportaBIBTEX("resultado_temp");
         }

    $export_end = filter_input(INPUT_GET, 'end');
    if(isset($export_end)){
        database::exportaENDNOTE();
    }

    // Variable que nos informa del número de resultados.
    $num_temp=database::cuentaResultados("resultado_temp");

?>
    <body style="background-color:#e6e6e6;">
        <!-- Botonera superior en HTML que nos permite gestionar las diferentes opciones -->
        <div id="inicior" class="px-3 py-4 my-4 text-center">
            <h2><?php echo ($num_temp)." ";?>Resultados para <?php echo $autor['autor'];?> en  <img src="..\imagenes\logo_fusion.jpg" class="img-fluid"  alt="Logo Fusión"></h2>
            <h5><a href="#export" class="btn btn-outline-success">Gestionar exportación a otros formatos</a>
                <a href="..\view\publicacion.php" class="btn btn-outline-primary">Agregar ficha manual a resultados</a>
                <a href="..\view\resultados.php?fusion=1.php" class="btn btn-outline-info">Eliminar filtros</a>
                <a href="..\view\resultados_org.php?agregar=1" button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#introrg">Incluir resultados en organización</a></h5>
        </div>

        <div class="row">
            <div class="columns small-12">
                <?php
                $alert = filter_input(INPUT_GET, "alert");
                $info = filter_input(INPUT_GET, "info");
                if( isset($info) ){
                    ?>
                    <div class="row">
                        <div class="columns small-12 medium-8 medium-centered">
                            <!--Ventana que saca un alert de Bootsrap en caso de eliminación con éxito-->
                            <div class="alert alert<?php if(isset($alert)){echo "-".$alert;} ?>" role=alert>
                            <?php echo $info; ?>
                            <a href="#" class="close">&times;</a>
                             </div>
                          </div>
                    </div>
                <?php
                    }
                ?>

        <!-- Inputs para los diferentes filtros -->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 card-margin">
                    <form action="resultados.php" method="post">
                        <div class="row">
                            <div class="col-12">
                                <div class="row no-gutters">
                                    <div class="col-lg-8 col-md-6 col-sm-12 p-0">
                                        <label for="busca_titulo">Título:</label>
                                        <input type="text" placeholder="Búsqueda por título..." class="form-control" id="busca_titulo" name="busca_titulo">
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-sm-12 p-0">
                                        <label for="busca_autores">Autores:</label>
                                        <input type="text" placeholder="Búsqueda por autores..." class="form-control" id="busca_autores" name="busca_autores">
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-sm-12 p-0">
                                        <label for="busca_desde">Años: <small>(desde / hasta. Sólo años)</small></label>
                                        <input type="text" placeholder="Fecha desde..." class="form-control" id="busca_desde" name="busca_desde" value="0000" required>
                                        <input type="text" placeholder="Fecha hasta.." class="form-control" id="busca_hasta" name="busca_hasta" value="9999" required>
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-sm-12 p-0">
                                        <label for="busca_publi">Tipo de publicación:</label>
                                        <input type="text" placeholder="Tipo de publicación..." class="form-control" id="busca_publi" name="busca_publi">
                                    </div>
                                    <div class="col-lg-1 col-md-3 col-sm-12 p-0">
                                        <button type="submit" class="btn btn-success">Filtrar resultados</button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Tabla con encabezados con links para ordenación -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><a href="resultados.php?column=id&order=<?php echo $asc_or_desc; ?>">Id. Interna<i class="bi bi-sort-alpha<?php echo $column == 'id' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=titulo&order=<?php echo $asc_or_desc; ?>">Título<i class="bi bi-sort-alpha<?php echo $column == 'titulo' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=fecha_pub&order=<?php echo $asc_or_desc; ?>">Año de publicación<i class="bi bi-sort-alpha<?php echo $column == 'fecha_pub' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=otros_aut&order=<?php echo $asc_or_desc; ?>">Otros autores:<i class="bi bi-sort-alpha<?php echo $column == 'otros_aut' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=publicado_en&order=<?php echo $asc_or_desc; ?>">Publicado en:<i class="bi bi-sort-alpha<?php echo $column == 'publicado_en' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th>Volumen:</th>
                    <th>Páginas:</th>
                    <th><a href="resultados.php?column=tipo_pub&order=<?php echo $asc_or_desc; ?>">Tipo publicación:<i class="bi bi-sort-alpha<?php echo $column == 'tipo_pub' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=doi&order=<?php echo $asc_or_desc; ?>">DOI<i class="bi bi-sort-alpha<?php echo $column == 'doi' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th><a href="resultados.php?column=num_citaciones&order=<?php echo $asc_or_desc; ?>">Número de citaciones<i class="bi bi-sort-alpha<?php echo $column == 'num_citaciones' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                    <th>URL de la publicación:</th>
                    <th>Enlace a ficha de Google Scholar</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
                <tr>
                    <?php while( $row_fusion = $result_final->fetch_array() ){
                        echo '<td>'.$row_fusion["id"].'</td>';
                        echo '<td>'.$row_fusion["titulo"].'</td>';
                        echo '<td>'.$row_fusion["fecha_pub"].'</td>';
                        echo '<td>'.$row_fusion["otros_aut"].'</td>';
                        echo '<td>'.$row_fusion["publicado_en"].'</td>';
                        echo '<td>'.$row_fusion["volume"].'</td>';
                        echo '<td>'.$row_fusion["pages"].'</td>';
                        echo '<td>'.$row_fusion["tipo_pub"].'</td>';
                        if (strlen($row_fusion["doi"])>30) {
                            echo '<td>'.substr($row_fusion["doi"],0,20).'...</td>';
                        } else {
                            echo '<td>'.$row_fusion["doi"].'</td>';
                        }
                       echo '<td>'.$row_fusion["num_citaciones"].'</td>';
                        if (isset($row_fusion["url"])) {
                            echo '<td><a href="'.$row_fusion["url"].'">Ver Publicación</a></td>';
                        }   else {
                            echo '<td>N/D</td>';
                        }
                        if (isset($row_fusion["url_detalle"])) {
                            echo '<td><a href="'.$row_fusion["url_detalle"].'" >Ficha Google Scholar</a></td>';
                        } else {
                            echo '<td>N/D</td>';
                        }
                        echo '<td class="right"><a href="publicacion.php?id='.$row_fusion["id"].'" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Editar</a></td>';
                        echo '<td class="right"><a href="resultados.php?id_del='.$row_fusion["id"].'" button type="button" class="btn btn-danger" "><i class="fa fa-times"></i> Eliminar</a></td>
                </tr>';
                    } ?>
        </table>

        <!-- Botonera para llamar a los diferentes tipos de exportaciones -->
        <div style="text-align: center;" id="export">
            <?php   echo '<td class="right"><a href="resultados.php?csv=1" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Exportar a CSV</a></td>';?>
            <?php   echo '<td class="right"><a href="resultados.php?bib=1?id=101" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Exportar a BibTex</a></td>';?>
            <?php   echo '<td class="right"><a href="resultados.php?end=1" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Exportar a ENDNOTE</a></td>';?>
            <td class="right"><a href="#inicior" button type="button" class="btn btn-outline-primary" "><i class="fa fa-pencil"></i>Volver a resultados</a></td>
        </div>

        <!-- Pie de página -->
        <footer class="pt-3 mt-4 text-muted border-top">
            2021 - Fernando Devís Rodríguez. TFG: "Fusión de Bases de Datos Bibliográficas"
        </footer>
    </body>

    <!-- Modal llamado por el "acerca de" del menú superior fijado -->
    <div class="modal fade" id="introrg" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form action="resultados_org.php?agregar=1" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Introduzca el nombre de la organización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Organización: </span>
                        </div>
                        <input type="text" class="form-control" id="organizacion" name="organizacion" required="required" placeholder="" <?php if(isset($organizacion)){echo 'value="'.$organizacion.'"';} else {echo 'value="ORG"';} ?>/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Incluir</button>
                </div>
            </div>
        </div>
    </div>
    </html>




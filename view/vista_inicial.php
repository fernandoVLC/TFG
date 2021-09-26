<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fusión de Bases de Datos Bibliográficas</title>
    <!-- Bootstrap -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<!-- COMENTARIO PARA COPIAR PEGAR -->
<?php
include_once("../includes/estructura.php");
include_once("../model/conexion_basica.php");

// Eliminar registro.
$ref_dblp = filter_input(INPUT_GET, 'id_dblp');
$ref_gs = filter_input(INPUT_GET, 'id_gs');

if(isset($ref_dblp)){
    $error = false;
    try{
        database::queryDelete("dblp_results", array("id_dblp"=>$ref_dblp));
    }catch(Exception $e){
        $error = true;
        header("Location: vista_inicial.php?alert=primary&info=".urlencode($e->getMessage()));
    }
    if( !$error ){
        header("Location: vista_inicial.php?alert=primary&info=Eliminación realizada con éxito.");
    }
}

if(isset($ref_gs)){
    $error = false;
    try{
        database::queryDelete("gs_results", array("id_gs"=>$ref_gs));
    }catch(Exception $e){
        $error = true;
        header("Location: vista_inicial.php?alert=primary&info=".urlencode($e->getMessage()));
    }
    if( !$error ){
        header("Location: vista_inicial.php?alert=primary&info=Eliminación realizada con éxito.");
    }
}

?>

<body>

<div id="inicio" class="px-3 py-4 my-4 text-center">
    <h5><a href="#dblp">Ver <?php echo ($num_dblp[0])." ";?>resultados <img src="..\imagenes\dblp.png" class="img-fluid" width="15%" alt="Logo DBLP"></h5>
    <h5><a href="#gs">Ver <?php echo ($num_gs[0])." ";?>resultados <img src="..\imagenes\logo_google.png" class="img-fluid" width="15%" alt="Logo Google Scholar"></a></h5>
    <td class="right"><a href="#fusion" button type="button" class="btn btn-success btn-lg" "><i class="fa fa-pencil"></i>Fusionar</a></td>
</div>

<div class="px-3 py-4 my-4 text-center">
<h2 id="dblp">Se han encontrado <?php echo ($num_dblp[0])." ";?>resultados para <?php echo $autor['autor'];?> en  <img src="..\imagenes\dblp.png" class="img-fluid" width="15%" alt="Logo Google Scholar"></h2>
</div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th>Id interna (dblp)</th>
            <th>Título</th>
            <th>Año de publicación</th>
            <th>Otros autores</th>
            <th>Publicado en:</th>
            <th>Volumen:</th>
            <th>Páginas</th>
            <th>Tipo publicación:</th>
            <th>DOI</th>
            <th>URL</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tr>
            <?php while( $row_dblp = $result_dblp->fetch_array() ){
                echo '<td>'.$row_dblp["id_dblp"].'</td>';
                echo '<td>'.$row_dblp["titulo"].'</td>';
                echo '<td>'.$row_dblp["fecha_pub"].'</td>';
                echo '<td>'.$row_dblp["otros_aut"].'</td>';
                echo '<td>'.$row_dblp["publicado_en"].'</td>';
                echo '<td>'.$row_dblp["volume"].'</td>';
                echo '<td>'.$row_dblp["pages"].'</td>';
                echo '<td>'.$row_dblp["tipo_pub"].'</td>';
                if (strlen($row_dblp["doi"])>30) {
                    echo '<td><a href="#" rel="tooltip" title="Lorem ipsum loren ispun consenquiaeflfe" Ver detalles</a></td>';
                } else {
                    echo '<td>'.$row_dblp["doi"].'</td>';
                }
                if (isset($row_dblp["url"])) {
                    echo '<td><a href="'.$row_dblp["url"].'">Ver Publicación</a></td>';
                }   else {
                    echo '<td>N/D</td>';
                }
                echo '<td class="right"><a href="..\view\publicacion_dblp.php?id='.$row_dblp["id_dblp"].'" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Editar</a></td>';
                echo '<td class="right"><a href="..\view\vista_inicial.php?id_dblp='.$row_dblp["id_dblp"].'?bd=1" button type="button" class="btn btn-danger" "><i class="fa fa-times"></i> Eliminar</a></td>
            </tr>';
            } ?>
    </table>



<div class="px-3 py-4 my-4" id="gs">
    <div style="text-align: center;"><h2>Se han encontrado <?php echo ($num_gs[0])." ";?>resultados para <?php echo $autor['autor'];?> en  <img src="..\imagenes\logo_google.png" class="img-fluid" width="15%" alt="Logo Google Scholar"></h2></div>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Id interna (gs)</th>
            <th>Título</th>
            <th>Año de publicación</th>
            <th>Autores</th>
            <th>Publicado en:</th>
            <th>Número de citaciones</th>
            <th>URL Ficha (Google Scholar)</th>
        </tr>
        </thead>
        <tr>
        <?php while( $row_gs = $result_gs->fetch_array() ){
            echo '<td>'.$row_gs["id_gs"].'</td>';
            echo '<td>'.$row_gs["titulo"].'</td>';
            echo '<td>'.$row_gs["fecha_pub"].'</td>';
            echo '<td>'.$row_gs["otros_aut"].'</td>';
            echo '<td>'.$row_gs["publicado_en"].'</td>';
            echo '<td>'.$row_gs["num_citaciones"].'</td>';
            echo '<td><a href="'.$row_gs["url_detalle"].'">Ficha Google Scholar</a></td>';
            echo '<td class="right"><a href="..\view\publicacion_gs.php?id='.$row_gs["id_gs"].'" button type="button" class="btn btn-outline-secondary" "><i class="fa fa-pencil"></i> Editar</a></td>';
            echo '<td class="right"><a href="..\view\vista_inicial.php?id_gs='.$row_gs["id_gs"].'" button type="button" class="btn btn-danger" "><i class="fa fa-times"></i> Eliminar</a></td>
            </tr>';
        } ?></table>

    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 id="fusion" class="display-5 fw-bold">Realizar FUSIÓN DE BASES DE DATOS</h1>
            <p class="col-md-8 fs-4">Posteriormente a la fusión, puede realizar cualquier tipo de edición sobre los resultados.</br> Considere, puesto que también es posible, realizar algun tipo de edición en las bases de datos iniciales.</br>El resultado final puede salvarse y exportarse a diferentes formatos</p>
            <form action="..\view\resultados.php?fusion=1" method="post">
                <button type="submit" class="btn btn-success">Realizar fusión</button>
                <td class="right"><a href="#inicio" button type="button" class="btn btn-outline-primary" "><i class="fa fa-pencil"></i>Volver a resultados</a></td>
            </form>
        </div>
    </div>
    <footer class="pt-3 mt-4 text-muted border-top">
        2021 - Fernando Devís Rodríguez. TFG: "Fusión de Bases de Datos Bibliográficas"
    </footer>


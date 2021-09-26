<?php
include_once("../model/database.php");
include_once("../includes/estructura.php");

$accion = filter_input(INPUT_POST, 'accion');
if( isset($accion) ){
    $error = false;
    $fields = array();
    $fields['titulo'] = filter_input(INPUT_POST, 'titulo');
    $fields['fecha_pub'] = filter_input(INPUT_POST, 'fecha_pub');
    $fields['otros_aut'] = filter_input(INPUT_POST, 'otros_aut');
    $fields['publicado_en'] = filter_input(INPUT_POST, 'publicado_en');
    $fields['num_citaciones'] = filter_input(INPUT_POST, 'num_citaciones');
    $fields['url_detalle'] = filter_input(INPUT_POST, 'url_detalle');

    if($accion=="mod"){
        $key_fields = array();
        $key_fields['id_gs'] = filter_input(INPUT_POST, "id");
        try {
            database::queryUpdate("gs_results", $fields, $key_fields);
        }catch(Exception $e){
            $error = true;
            header("Location: vista_inicial.php?alert=primary&info=".urlencode($e->getMessage()));
        }
        if( !$error ){
            header("Location: vista_inicial.php?alert=primary&info=Registro modificado correctamente.");
        }
    }else{
        header("Location: vista_inicial.php?alert=primary&info=Error al realizar la acción.");
    }
}

$id = filter_input(INPUT_GET, 'id');
if( isset($id) ){
    $database = database::getInstance();
    $result = $database->query("SELECT * FROM gs_results WHERE id_gs = '".$id."'");
    if ($result->num_rows == 1) {
        $row = $result->fetch_array();
    } else {
        header("Location: vista_inicial.php?alert=primary&info=Error al identificar el registro.");
    }
}

?>

<?php ini(); ?>

    <form data-abide action="publicacion_gs.php" method="post">
        <img class="d-block mx-auto mb-4" src="../imagenes/logo_fusion.png" alt="" width="25%" height="25%">
        <p class="display-6 fw-bold">Datos de la publicación: <?php if(isset($row["id_gs"])){echo $row["id_gs"];} ?></p>
        <div class="col-lg-6 mx-auto">
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <input type="hidden" name="accion" value="<?php echo (isset($row["id_gs"]))?"mod":"new"; ?>" />
                <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Identificación: </span>
            </div>
            <input type="text" readonly class="form-control" name="id" required="required" placeholder="" <?php if(isset($row["id_gs"])){echo 'value="'.$row["id_gs"].'"';} ?> />
            <small id="emailHelp" class="form-text text-muted">Este campo se rellena de manera automática.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Título: </span>
            </div>
            <input type="text" class="form-control" name="titulo" required="required" placeholder="" <?php if(isset($row["titulo"])){echo 'value="'.$row["titulo"].'"';} ?> />
            <small id="tituloHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Fecha de la publicación: </span>
            </div>
            <input type="text" class="form-control" name="fecha_pub" required="required" placeholder="" <?php if(isset($row["fecha_pub"])){echo 'value="'.$row["fecha_pub"].'"';} ?> />
            <small id="fechapubHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Otros autores: </span>
            </div>
            <input type="text" class="form-control" name="otros_aut" required="required" placeholder="" <?php if(isset($row["otros_aut"])){echo 'value="'.$row["otros_aut"].'"';} ?> />
            <small id="otrosAutHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Publicado en: </span>
            </div>
            <input type="text" class="form-control" name="publicado_en" required="required" placeholder="" <?php if(isset($row["publicado_en"])){echo 'value="'.$row["publicado_en"].'"';} ?> />
            <small id="publicadoenHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Número de citaciones</span>
            </div>
            <input type="text" class="form-control" name="num_citaciones" required="required" placeholder="" <?php if(isset($row["num_citaciones"])){echo 'value="'.$row["num_citaciones"].'"';} ?> />
            <small id="numCitacionesHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">URL (detalles Google Scholar): </span>
            </div>
            <input type="text" class="form-control" name="url_detalle" required="required" placeholder="" <?php if(isset($row["url_detalle"])){echo 'value="'.$row["url_detalle"].'"';} ?> />
        </div>

        <div style="text-align: center;">
            <?php if( isset($id) ){ ?>
            <div class="columns small-12 medium-6">
                <button class="btn btn-success" type="submit"><i class="fa fa-pencil"></i>Modificar</button>
                <?php }else{ ?>
                    <button class="btn btn-success" type="submit"><i class="fa fa-pencil"></i>Crear</button>
                <?php } ?>
                <a class="btn btn-danger" href="..\view\vista_inicial.php"><i class="fa fa-times"></i>Cancelar</a>
            </div>
    </form>
<?php fin(); ?>
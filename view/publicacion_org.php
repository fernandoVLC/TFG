<?php
include_once("../model/database.php");
include_once("../includes/estructura.php");

$accion = filter_input(INPUT_POST, 'accion');
if( isset($accion) ){
    $error = false;
    $fields = array();
    $fields['organizacion'] = filter_input(INPUT_POST, 'organizacion');
    $fields['titulo'] = filter_input(INPUT_POST, 'titulo');
    $fields['fecha_pub'] = filter_input(INPUT_POST, 'fecha_pub');
    $fields['otros_aut'] = filter_input(INPUT_POST, 'otros_aut');
    $fields['publicado_en'] = filter_input(INPUT_POST, 'publicado_en');
    $fields['tipo_pub'] = filter_input(INPUT_POST, 'tipo_pub');
    $fields['doi'] = filter_input(INPUT_POST, 'doi');
    $fields['volume'] = filter_input(INPUT_POST, 'volume');
    $fields['pages'] = filter_input(INPUT_POST, 'pages');
    $fields['url'] = filter_input(INPUT_POST, 'url');

    if($accion == "new") {
        try {
            database::queryInsert("resultado_org", $fields);
        } catch (Exception $e) {
            $error = true;
            header("Location: resultados_org.php?alert=primary&info=" . urlencode($e->getMessage()));
        }
        if (!$error) {
            header("Location: resultados_org.php?alert=primary&info=Registro añadido correstamente.");
        }
    }elseif($accion=="mod"){
        $key_fields = array();
        $key_fields['id'] = filter_input(INPUT_POST, "id");
        try {
            database::queryUpdate("resultado_org", $fields, $key_fields);
        }catch(Exception $e){
            $error = true;
            header("Location: resultados_org.php?alert=primary&info=".urlencode($e->getMessage()));
        }
        if( !$error ){
            header("Location: resultados_org.php?alert=primary&info=Registro modificado correctamente.");
        }
    }else{
        header("Location: resultados_org.php?alert=primary&info=Error al realizar la acción.");
    }
}




$id = filter_input(INPUT_GET, 'id');
if( isset($id) ){
    $database = database::getInstance();
    $result = $database->query("SELECT * FROM resultado_org WHERE id = '".$id."'");
    if ($result->num_rows == 1) {
        $row = $result->fetch_array();
    } else {
        header("Location: resultados_org.php?alert=primary&info=Error al identificar el registro.");
    }
}

?>

<?php ini(); ?>
    <body style="background-color:#ffffe6;">
    <body class="px-3 py-4 my-4 text-center">
    <form data-abide action="publicacion_org.php" method="post">
        <img class="d-block mx-auto mb-4" src="../imagenes/logo_fusion.jpg" alt="" width="25%" height="25%">
        <p class="display-6 fw-bold">Datos de la publicación: <?php if(isset($row["id"])){echo $row["id"];} ?></p>
        <div class="col-lg-6 mx-auto">
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <input type="hidden" name="accion" value="<?php echo (isset($row["id"]))?"mod":"new"; ?>" />
                <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Identificación: </span>
            </div>
            <input type="text" readonly class="form-control" name="id" required="required" placeholder="" <?php if(isset($row["id"])){echo 'value="'.$row["id"].'"';} ?> />
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
                <span class="input-group-text">Organización o empresa: </span>
            </div>
            <input type="text" class="form-control" name="organizacion" required="required" placeholder="" <?php if(isset($row["organizacion"])){echo 'value="'.$row["organizacion"].'"';} ?> />
            <small id="organizacionHelp" class="form-text text-muted">Este campo es requerido.</small>
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
                <span class="input-group-text">Tipo de publicación</span>
            </div>
            <input type="text" class="form-control" name="tipo_pub" required="required" placeholder="" <?php if(isset($row["tipo_pub"])){echo 'value="'.$row["tipo_pub"].'"';} ?> />
            <small id="tipo_pubHelp" class="form-text text-muted">Este campo es requerido.</small>
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
                <span class="input-group-text">Volumen: </span>
            </div>
            <input type="text" class="form-control" name="volume" required="required" placeholder="" <?php if(isset($row["volume"])){echo 'value="'.$row["volume"].'"';} ?> />
            <div class="input-group-prepend">
                <span class="input-group-text">Páginas: </span>
            </div>
            <input type="text" class="form-control" name="pages" required="required" placeholder="" <?php if(isset($row["pages"])){echo 'value="'.$row["pages"].'"';} ?> />
            <small id="pagesenHelp" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">DOI</span>
            </div>
            <input type="text" class="form-control" name="doi" required="required" placeholder="" <?php if(isset($row["doi"])){echo 'value="'.$row["doi"].'"';} ?> />
            <small id="doi" class="form-text text-muted">Este campo es requerido.</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">URL: </span>
            </div>
            <input type="text" class="form-control" name="url" required="required" placeholder="" <?php if(isset($row["url"])){echo 'value="'.$row["url"].'"';} ?> />
        </div>

        <div style="text-align: center;">
            <?php if( isset($id) ){ ?>
            <div class="columns small-12 medium-6">
                <button class="btn btn-success" type="submit"><i class="fa fa-pencil"></i>Modificar</button>
                <?php }else{ ?>
                    <button class="btn btn-success" type="submit"><i class="fa fa-pencil"></i>Crear</button>
                <?php } ?>
                <a class="btn btn-danger" href="..\view\resultados_org.php"><i class="fa fa-times"></i>Cancelar</a>
            </div>
   </form>
<?php fin(); ?>
    </body>

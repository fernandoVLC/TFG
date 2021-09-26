<!DOCTYPE html>
<html lang="es">
    <head>
        <!-- Incluimos Bootstrap 5.0  y los diferentes encabezados para un diseño responsive-->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Fusión de Bases de Datos Bibliográficas</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link href="../css/bootstrap.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>

    <!-- Menú superior para navegar por la aplicación -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/tfg"><img src="..\imagenes\logo_uned_2.jpg" width="10%"> Fusión de Bases de Datos Bibliográficas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="/tfg">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tfg/view/resultados_org.php">BBDD Organización</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tfg/view/resultados.php?fusion=1.php">Reiniciar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tfg/files/memoria.pdf">Memoria</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://github.com/fernandoVLC/TFG/tree/main/tfg">GitHub</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">Acerca de</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Acerca de-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fusión de Bases de Datos bibliográficas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"><p>PROYECTO FINAL DE GRADO</p><small>
                    Un sistema sencillo para gestionar bibliografías procedentes de diferentes fuentes.</br></br></small><p>ALUMNO:<br> Fernando Devís Rodríguez</p><p>DIRECTOR:<br>  Fernando López Ostenero</p>
                </div>
                <div class="modal-footer">Grado en Ingeniería de las Tecnologías de la Información
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>



<?php function ini(){ ?>
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
    <style>
        .responsive {
            width: 100%;
        }
        header .top-bar {
            background-color: transparent;
        }
        header .top-bar-section li:not(.has-form) a:not(.button):hover {
            background-color: #444;
        }
        table button, table .button {
            margin-bottom: 0px;
        }
        table img {
            max-height: 35px;
        }
        @media only screen and (max-width: 40em) {
            header .top-bar {
                background-color: #EB6C1E;
            }
        }
    </style>
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).foundation();
        });
    </script>
</html>
<body>

<div class="row">
    <div class="columns small-12">
        <?php
        $alert = filter_input(INPUT_GET, "alert");
        $info = filter_input(INPUT_GET, "info");
        if( isset($info) ){
            ?>
            <div class="row">
                <div class="columns small-12 medium-8 medium-centered">
                    <div class="alert alert-success" role=alert>Imprimo info:
                    <!--div class="alert alert<?php if(isset($alert)){echo "-".$alert;} ?>" role=alert !-->
                        <?php echo $info; ?>
                        <a href="#" class="close">&times;</a>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php } ?>

<?php function fin(){ ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
<?php } ?>


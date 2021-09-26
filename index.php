<!-- Archivo de inicio de la aplicación
no realiza de momento ninguna operación CRUD
Es la interfaz de inicio en la que el usuario elige las opciones
dependiendo del caso de uso
- Busqueda por autor
- Búsqueda por URL
- Gestiones en la BBDD de la Organización
-->

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fusión de Bases de Datos Bibliográficas</title>
    <!-- Incluimos Bootstrap 5.0  y los diferentes encabezados para un diseño responsive-->
    <link href="css/bootstrap.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </head>
  <?php
    include_once("./model/conexion.php");
  ?>

  <body>
    <div class="container py-4">
      <header class="pb-3 mb-4 border-bottom">
          <a href="/tfg" class="d-flex align-items-center text-dark text-decoration-none"><img src=".\imagenes\logo_uned_2.jpg" width="10%">
              <span class="fs-4">- Fusión de Bases de Datos Bibliográficas -</span>
          </a>
      </header>

      <div class="p-5 mb-4 bg-light rounded-3">
          <div class="container-fluid py-5">
              <h1 class="display-5 fw-bold">Búsqueda por autor</h1>
              <p class="col-md-8 fs-4">Desde este apartado, introduzca el autor.</br> El sistema realizará automáticamente las búsquedas en varias bases de datos y las fusionará de manera automática. Posteriormente el usuario puede editar, salvar los resultados
              y exportarlos a otros formatos</p>
              <form action="controllers/inicial.php" method="post">
                  <div class="mb-3">
                      <label for="inputAutor" class="form-label">Nombre del autor:</label>
                      <input type="text" class="form-control" name="inputAutor" id="inputAutor" aria-describedby="inputAutor">
                      <div id="autorHelp" class="form-text" style="color: red">Atención: El uso de esta opción elimina las fusiones anteriores</div>
                  </div>
                  <button type="submit" class="btn btn-success">Búsqueda por autor</button>
              </form>
          </div>
      </div>

      <div class="row align-items-md-stretch">
          <div class="col-md-6">
              <div class="h-100 p-5 text-white bg-dark rounded-3">
                  <h2>Búsqueda por URLs</h2>
                  <br>Introduzca las URLs correspondiente a cada una de las BBDD disponibles y el sistema las fusionará en una sola de forma automática.</br> Posteriormente el usuario puede editar, salvar los resultados y exportarlos a otros formatos</p>
                      <form action="controllers/inicial.php" method="post">
                      <label for="inputURL_GS" class="form-label">URL de Google Scholar:</label>
                      <input type="text" class="form-control" name="inputURL_GS" id="inputURL_GS" aria-describedby="inputURL_GS">
                      <label for="inputURL_DBLP" class="form-label">URL de DBLP:</label>
                      <input type="text" class="form-control" name="inputURL_DBLP" id="inputURL_DBLP" aria-describedby="inputURL_DBLP">
                      <div id="URL_Help" class="form-text" style="color: red">Atención: El uso de esta opción elimina las fusiones anteriores</div>
                      <button class="btn btn-outline-light" type="submit">Analizar URLs</button>
                  </form>
              </div>
          </div>
          <div class="col-md-6">
              <div class="h-100 p-4 bg-light border rounded-3">
                  <h2>Organizaciones</h2>
                  <p>Crear manualmente publicaciones en una base de datos para organizaciones o empresas:</p>
                  <form data-abide action="view/publicacion_org.php" method="post">
                   <button class="btn btn-outline-success"  type="submit">Agregar publicación nueva de organización</button>
                  </form>
                  </hr>
                  <p><br>Ver bibliografía de organizaciones / empresas:<br></p>
                  <form data-abide action="view/resultados_org.php" method="post">
                      <button class="btn btn-outline-success"  type="submit">Ver BBDD organizaciones</button>
                  </form>
              </div>
          </div>
        </div>
      <footer class="pt-3 mt-4 text-muted border-top">2021 - Fernando Devís Rodríguez. TFG: "Fusión de Bases de Datos Bibliográficas"</footer>
    </div>
  </body>
</html>
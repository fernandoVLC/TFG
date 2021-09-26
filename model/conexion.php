<?php
    /**
     * Credenciales para la conexión por PHP
     * y comprobación de errores
     *
     **/

    $servername = "localhost";
    $username = "root";
    $password = "";
    $conn = mysqli_connect($servername, $username, $password);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /**
     * Creación de sentencias SQL mediante variables PHP
     * para posteriormente ejecutarlas con control de errores
     *
     */

    $queryDrop_dblp_results = "DROP TABLE IF EXISTS dblp_results";
    $queryDrop_fusion_db = "DROP TABLE IF EXISTS fusion_db";
    $queryDrop_gs_results = "DROP TABLE IF EXISTS gs_results";
    $queryDrop_resultado_db = "DROP TABLE IF EXISTS resultado_db";
    $queryDrop_resultado_org_temp = "DROP TABLE IF EXISTS resultado_org_temp";
    $queryDrop_resultado_temp = "DROP TABLE IF EXISTS resultado_temp";


    $queryCreateDB="CREATE DATABASE IF NOT EXISTS fusion_db";
    $queryUse="USE fusion_db";
    $queryCreateTableGs="CREATE TABLE IF NOT EXISTS gs_results (
        id_gs INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        otros_aut VARCHAR(3000),
        publicado_en VARCHAR(300),
        bdorigen VARCHAR (300),
        descripcion VARCHAR (3000),
        url_detalle VARCHAR (3000),
        num_citaciones INT UNSIGNED)";

    $queryCreateTableDblp="CREATE TABLE IF NOT EXISTS dblp_results (
        id_dblp INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        otros_aut VARCHAR(3000),
        publicado_en VARCHAR(300),
        tipo_pub VARCHAR (300),
        bdorigen VARCHAR (300),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        doi VARCHAR(300))";

    $queryCreateTableFusionDB="CREATE TABLE IF NOT EXISTS fusion_db (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        organizacion VARCHAR(300),
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        publicado_en VARCHAR(300),
        otros_aut VARCHAR(3000),
        tipo_pub VARCHAR (300),  
        doi VARCHAR(300),
        num_citaciones INT UNSIGNED,
        descripcion VARCHAR (3000),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        url_detalle VARCHAR (3000),
        bdorigen VARCHAR (300))";

    $queryCreateTableResultadoDB="CREATE TABLE IF NOT EXISTS resultado_db (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        organizacion VARCHAR(300),
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        publicado_en VARCHAR(300),
        otros_aut VARCHAR(3000),
        tipo_pub VARCHAR (300),  
        doi VARCHAR(300),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        url_detalle VARCHAR (3000),
        descripcion VARCHAR (3000),
        num_citaciones INT UNSIGNED)";

    $queryCreateTableResultadoTemp="CREATE TABLE IF NOT EXISTS resultado_temp (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        organizacion VARCHAR(300),
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        publicado_en VARCHAR(300),
        otros_aut VARCHAR(3000),
        tipo_pub VARCHAR (300),  
        doi VARCHAR(300),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        url_detalle VARCHAR (3000),
        descripcion VARCHAR (3000),
        num_citaciones INT UNSIGNED)";

    $queryCreateTableResultadoOrg="CREATE TABLE IF NOT EXISTS resultado_org (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        organizacion VARCHAR(300),
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        publicado_en VARCHAR(300),
        otros_aut VARCHAR(3000),
        tipo_pub VARCHAR (300),  
        doi VARCHAR(300),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        url_detalle VARCHAR (3000),
        descripcion VARCHAR (3000),
        num_citaciones INT UNSIGNED)";

    $queryCreateTableResultadoOrgTemp="CREATE TABLE IF NOT EXISTS resultado_org_temp (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        organizacion VARCHAR(300),
        autor VARCHAR(300),
        titulo VARCHAR(300) NOT NULL,
        fecha_pub VARCHAR(300),
        publicado_en VARCHAR(300),
        otros_aut VARCHAR(3000),
        tipo_pub VARCHAR (300),  
        doi VARCHAR(300),
        url VARCHAR (300),
        pages VARCHAR (300),
        volume VARCHAR (300),
        url_detalle VARCHAR (3000),
        descripcion VARCHAR (3000),
        num_citaciones INT UNSIGNED)";

    /**
     * Ejecución de las queries anteriores con control de errores
     * Se incluyen echoes comentados con findes de depuración
     *
     * Se genera el esquema de toda la BBDD utilizada en el proyecto
     */

    if (mysqli_query($conn, $queryCreateDB)) {
        echo "<pre><code>- Base de datos FUSION_DB PREPARADA -</code></pre>";
    } else {
        echo "ERROR CREANDO LA NUEVA BASE DE DATOS FUSION_DB: " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryUse)) {
        echo "<pre><code>Usando la base de datos FUSION_DB</pre></code>";
    } else {
        echo "ERROR INTENTANDO USAR LA BASE DE DATOS FUSION_DB" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_resultado_temp)) {
        // echo "Tabla resultado_temp borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA resultado_temp " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_resultado_org_temp)) {
        // echo "Tabla resultado_org_temp borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA resultado_org_temp " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_resultado_db)) {
       // echo "Tabla resultado_db borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA resultado_db " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_gs_results)) {
       // echo "Tabla gs_results borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA gs_results " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_fusion_db)) {
        // echo "Tabla fusion_db borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA fusion_db " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryDrop_dblp_results)) {
         // echo "Tabla dblp_results borrada con éxito";
    } else {
        echo "ERROR BORRANDO LA TABLA dblp_results " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableGs)) {
        // echo "Tabla con resultados de Google Scholar creada con éxito";
    } else {
        echo "Error creando la tabla de Google Scholar " . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableDblp)) {
        // echo "Tabla con resultados de DBLP creada con éxito";
    } else {
        echo "Error creando la tabla de DBLP" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableFusionDB)) {
        // echo "Tabla para fusionar resultados fusion_db creada con éxito.";
    } else {
        echo "Error creando la tabla FUSION_DB" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableResultadoDB)) {
        // echo "Tabla para resultados finales ResultadoDB creada con éxito.";
    } else {
        // echo "Error creando la tabla FUSION_DB" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableResultadoTemp)) {
        // echo "Tabla para resultados temporales ResultadoTemp creada con éxito.";
    } else {
        echo "Error creando la tabla FUSION_DB" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableResultadoOrg)) {
        // echo "Tabla para resultados organizaciones ResultadoOrg creada con éxito.";
    } else {
        // echo "Error creando la tabla FUSION_DB" . mysqli_error($conn);
    }

    if (mysqli_query($conn, $queryCreateTableResultadoOrgTemp)) {
        // echo "Tabla para resultados organizaciones ResultadoOrg creada con éxito.";
    } else {
        // echo "Error creando la tabla FUSION_DB" . mysqli_error($conn);
    }

    // Cerrando la conexión
    mysqli_close($conn);
?>
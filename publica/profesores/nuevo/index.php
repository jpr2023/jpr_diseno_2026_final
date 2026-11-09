<?php
    session_start() ;
    $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");

    // Consulta para obtener el último ID y calcular el siguiente
    $siguienteIdSQL = "SELECT MAX(ID) AS MAX_ID FROM T_PROFESORES;";
    $accionId = $base->query($siguienteIdSQL);
    $resultadoId = $accionId ? $accionId->fetch(PDO::FETCH_OBJ) : null;
    $nuevoId = ($resultadoId && $resultadoId->MAX_ID !== null) ? intval($resultadoId->MAX_ID) + 1 : 1;
?><html >
    
<head>
    <title >Tu App</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <link rel="stylesheet" href="../estilosuser.css">
    <style >[full]{ width: 100% }</style>
</head>

    <body >
        <header >
            <h1>Insertar un/a nuev@ profesor@</h1>
            <?php
                if( isset($_SESSION["mensaje"]) ):
                    printf( "<p id='idMarcaMensaje' >%s</p><script>setTimeout(x=>idMarcaMensaje.outerHTML='',3000)</script>", $_SESSION["mensaje"] ) ;
                    unset( $_SESSION["mensaje"] ); 
                endif;
                if( isset($_SESSION["error"]) ):
                    printf( "<p id='idMarcaError' >%s</p><script>setTimeout(x=>idMarcaError.outerHTML='',3000)</script>", $_SESSION["error"] ) ;
                    unset( $_SESSION["error"] ); 
                endif;
            ?>
            <nav >
                <button onclick="location.href=('/profesores')">Volver</button>
            </nav>
        </header>
        <form action='/profesores/nuevo/procesar.php' method="get" >

<article >
    <label for='idID'>ID: </label><br>
    <input type="number" id="idID" name="nmID" value="<?php echo htmlspecialchars($nuevoId, ENT_QUOTES, 'UTF-8'); ?>" readonly />
</article>

<article >
    <label for='idNOMBRE'>NOMBRE: </label><br>
	<input type="TEXT" id="idNOMBRE" name="nmNOMBRE"   />
</article>

<article>
    <label for='idActivo'>ACTIVO: </label><br>
    <select id="idActivo" name="nmACTIVO">
        <option value="TRUE">TRUE</option>
        <option value="FALSE">FALSE</option>
    </select>
</article>

<article >
    <label for='idTELEFONO'>TELEFONO: </label><br>
	<input type="TEXT" id="idTELEFONO" name="nmTELEFONO"   />
</article>

<article >
    <label for='idDIRECCION'>DIRECCION: </label><br>
	<input type="TEXT" id="idDIRECCION" name="nmDIRECCION"   />
</article>

<article >
    <label for='idTITULO'>TITULO: </label><br>
	<input type="TEXT" id="idTITULO" name="nmTITULO"   />
</article>

<article >
    <label for='idF_NACIMIENTO'>FECHA DE NACIMIENTO: </label><br>
	<input type="DATE" id="idF_NACIMIENTO" name="nmF_NACIMIENTO"   />
</article>

            <input type="submit" value="Agregar nuevo ítem" />
        </form>
    </body>
</html>
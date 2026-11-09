<?php
    session_start() ;
    $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");

    // Consulta para obtener el último ID y calcular el siguiente
    $siguienteIdSQL = "SELECT MAX(ID) AS MAX_ID FROM T_MATERIAS;";
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
            <h1>Insertar una nueva materia</h1>
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
                <button onclick="location.href=('/materias')">Volver</button>
            </nav>
        </header>
        <form action='/materias/nuevo/procesar.php' method="get" >

<article >
    <label for='idID'>ID: </label><br>
    <input type="number" id="idID" name="nmID" value="<?php echo htmlspecialchars($nuevoId, ENT_QUOTES, 'UTF-8'); ?>" readonly />
</article>

<article >
    <label for='idNOMBRE'>NOMBRE: </label><br>
	<input type="TEXT" id="idNOMBRE" name="nmNOMBRE"   />
</article>

<article>
    <label for='idCURSO'>CURSO: </label><br>
    <select id="idCURSO" name="mnCURSO">
        <option value="1">1 - Primero</option>
        <option value="2">2 - Segundo</option>
        <option value="3">3 - Tercero</option>
        <option value="4">4 - Cuarto</option>
        <option value="5">5 - Quinto</option>
        <option value="6">6 - Sexto</option>
        <option value="7">7 - Septimo</option>
    </select>
</article>

<article>
    <label for='idActivo'>ACTIVO: </label><br>
    <select id="idActivo" name="nmACTIVO">
        <option value="TRUE">TRUE</option>
        <option value="FALSE">FALSE</option>
    </select>
</article>

            <input type="submit" value="Agregar nuevo ítem" />
        </form>
    </body>
</html>
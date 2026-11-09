<?php
    session_start() ;

    $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");

    $tutoresSQL = "SELECT ID, NOMBRE FROM T_TUTORES;";
    $accionTutores = $base->query($tutoresSQL);
    $listaTurores = $accionTutores ? $accionTutores->fetchAll(PDO::FETCH_OBJ) : [];

    $CarrerasSQL = "SELECT ID, NOMBRE FROM T_CARRERAS;";
    $accionCarreras = $base->query($CarrerasSQL);
    $listaCarreras = $accionCarreras ? $accionCarreras->fetchAll(PDO::FETCH_OBJ) : [];

    // Consulta para obtener el último ID y calcular el siguiente
    $siguienteIdSQL = "SELECT MAX(ID) AS MAX_ID FROM T_ALUMNOS;";
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
            <h1>Insertar un/a nuev@ alumno</h1>
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
                <button onclick="location.href=('/alumnos')">Volver</button>
            </nav>
        </header>
        <form action='/alumnos/nuevo/procesar.php' method="get" >
        
<!--
        <article >
    <label for='idID'>ID: </label><br>
	<input type="INT" id="idID" name="nmID"   />
</article>
            -->
<article >
    <label for='idID'>ID: </label><br>
    <input type="number" id="idID" name="nmID" value="<?php echo htmlspecialchars($nuevoId, ENT_QUOTES, 'UTF-8'); ?>" readonly />
</article>

<article >
    <label for='idNOMBRE'>NOMBRE: </label><br>
	<input type="TEXT" id="idNOMBRE" name="nmNOMBRE"   />
</article>

<!--
<article >
    <label for='idACTIVO'>ACTIVO: </label><br>
	<input type="BIT" id="idACTIVO" name="nmACTIVO"   />
</article>
            -->
<article>
    <label for='idActivo'>ACTIVO: </label><br>
    <select id="idActivo" name="nmACTIVO">
        <option value="TRUE">TRUE</option>
        <option value="FALSE">FALSE</option>
    </select>
</article>

<article >
    <label for='idF_NACIMIENTO'>F_NACIMIENTO: </label><br>
	<input type="DATE" id="idF_NACIMIENTO" name="nmF_NACIMIENTO"   />
</article>
<!--
<article >
    <label for='idTUTOR'>TUTOR: </label><br>
	<input type="INT" id="idTUTOR" name="nmTUTOR"   />
</article>
            -->

<article>
    <label for='idTutor'>TUTOR: </label><br>
    <select id="idTutor_ORI" name="nmTutor_ORI">
        <?php foreach ($listaTurores as $tutor): ?>
            <option value="<?php echo htmlspecialchars($tutor->ID, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($tutor->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="number" id="idTutor" name="nmTUTOR" readonly />
</article>

<!--
<article >
    <label for='idCARRERA'>CARRERA: </label><br>
	<input type="INT" id="idCARRERA" name="nmCARRERA"   />
</article>
        -->

<article>
    <label for='idCARRERA'>CARRERA: </label><br>
    <select id="idCARRERA_ORI" name="nmCARRERA_ORI">
        <?php foreach ($listaCarreras as $carrera): ?>
            <option value="<?php echo htmlspecialchars($carrera->ID, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($carrera->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="number" id="idCarrera" name="nmCARRERA" readonly />
</article>

<article >
    <label for='idDIRECCION'>DIRECCION: </label><br>
	<input type="TEXT" id="idDIRECCION" name="nmDIRECCION"   />
</article>

            <input type="submit" value="Agregar nuevo ítem" />
        </form>
    </body>
       <script>
    const selectTutor = document.getElementById('idTutor_ORI');
    const inputTutor = document.getElementById('idTutor');

    selectTutor.addEventListener('change', function() {
        inputTutor.value = this.value;
    });

    const selectCarrera = document.getElementById('idCARRERA_ORI');
    const inputCarrera = document.getElementById('idCarrera');

    selectCarrera.addEventListener('change', function() {
        inputCarrera.value = this.value;
    });
</script>
</html>
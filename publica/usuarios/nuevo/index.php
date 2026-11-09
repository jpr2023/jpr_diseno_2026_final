<?php
    session_start() ;
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
            <h1>Insertar un/a nuev@ usuarios</h1>
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
                <button onclick="location.href=('/usuarios')">Volver</button>
            </nav>
        </header>
        <form action='/usuarios/nuevo/procesar.php' method="get" onsubmit="return validarFormulario();">
            <article >
    <label for='idUSUARIO'>USUARIO: </label><br>
	<input type="TEXT" id="idUSUARIO" name="nmUSUARIO"   />
</article>

<article >
    <label for='idCLAVE'>CLAVE: </label><br>
	<input type="TEXT" id="idCLAVE" name="nmCLAVE"   />
</article>

<article >
    <label for='idNOMBRE'>NOMBRE: </label><br>
	<input type="TEXT" id="idNOMBRE" name="nmNOMBRE"   />
</article>

<article >
    <label for='idACTIVO'>ACTIVO: </label><br>
	<!--<input type="BIT" id="idACTIVO" name="nmACTIVO"   />-->
    <select id="idACTIVO" name="nmACTIVO">
        <option value="true">TRUE</option>
        <option value="false">FALSE</option>
    </select>
</article>

<article >
    <label for='idPERFIL'>PERFIL: </label><br>
	<!--<input type="number" id="idPERFIL" name="nmPERFIL"  />-->
    <select id="idPERFIL" name="nmPERFIL">
        <option value="">Seleccione un perfil...</option>
    
        <?php
        session_start();
        $url = (object)$_GET;
        $reglaSQL = "SELECT ID, NOMBRE FROM T_PERFILES;";
        $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");
    
        $accion = $base->query($reglaSQL);
    
        // Usamos fetch(PDO::FETCH_ASSOC) en lugar de fetch_assoc()
        while ($row = $accion->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['ID'] . '">' . $row['NOMBRE'] . '</option>';
        }
    ?>
    </select>
</article>

            <input type="submit" value="Agregar nuevo ítem" />
        </form>
    </body>
    <script>
        function validarFormulario() {
            // Obtenemos los valores de los campos eliminando espacios en blanco con trim()
            let usuario = document.getElementById('idUSUARIO').value.trim();
            let clave = document.getElementById('idCLAVE').value.trim();
            let nombre = document.getElementById('idNOMBRE').value.trim();

            if (usuario === "" || clave === "" || nombre === "") {
                alert("Faltan completar datos");
                return false; // Detiene el envío del formulario
            }

        return true; // Permite que el formulario se envíe si todo está completo
}
    </script>
</html>
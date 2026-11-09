<?php
    session_start();
    
    $db_path = $_SERVER['DOCUMENT_ROOT'] . '/../CURSADAS.db';
    $base = new PDO("sqlite:" . $db_path);

    // Consulta para obtener el último ID y calcular el siguiente
    $siguienteIdSQL = "SELECT MAX(ID) AS MAX_ID FROM T_CURSADAS2;";
    $accionId = $base->query($siguienteIdSQL);
    $resultadoId = $accionId ? $accionId->fetch(PDO::FETCH_OBJ) : null;
    $nuevoId = ($resultadoId && $resultadoId->MAX_ID !== null) ? intval($resultadoId->MAX_ID) + 1 : 1;

    $profesoresSQL = "SELECT ID, NOMBRE FROM T_PROFESORES;";
    $accionProfesores = $base->query($profesoresSQL);
    $listaProfesores = $accionProfesores ? $accionProfesores->fetchAll(PDO::FETCH_OBJ) : [];

    $materiasSQL = "SELECT ID, NOMBRE FROM T_MATERIAS;";
    $accionMaterias = $base->query($materiasSQL);
    $listaMaterias = $accionMaterias ? $accionMaterias->fetchAll(PDO::FETCH_OBJ) : [];
?>
<html>
<head>
    <title>Tu App</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style>
        [full]{ width: 100% }
        /* Estilo sutil para diferenciar los campos de solo lectura */
        input[readonly] {
            background-color: #f1f1f1;
            color: #555;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <header>
        <h1>Insertar una nueva cursada</h1>
        <?php
            if( isset($_SESSION["mensaje"]) ):
                printf( "<p id='idMarcaMensaje'>%s</p><script>setTimeout(x=>idMarcaMensaje.outerHTML='',3000)</script>", $_SESSION["mensaje"] );
                unset( $_SESSION["mensaje"] ); 
            endif;
            if( isset($_SESSION["error"]) ):
                printf( "<p id='idMarcaError'>%s</p><script>setTimeout(x=>idMarcaError.outerHTML='',3000)</script>", $_SESSION["error"] );
                unset( $_SESSION["error"] ); 
            endif;
        ?>
        <nav>
            <button onclick="location.href=('/cursadas')">Volver</button>
        </nav>
    </header>

    <form action='/cursadas/nuevo/procesar.php' method="POST">

        <article>
            <label for='idID'>ID: </label><br>
            <input type="number" id="idID" name="nmID" value="<?php echo htmlspecialchars($nuevoId, ENT_QUOTES, 'UTF-8'); ?>" readonly />
        </article>

        <article>
            <label for='idANIO'>ANIO: </label><br>
            <select id="idANIO" name="nmANIO">
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
            </select>
        </article>

        <article>
            <label for='idC_MATERIA_ORI'>MATERIA: </label><br>
            <select id="idC_MATERIA_ORI" name="nmC_MATERIA_ORI">
                <option value="">Seleccione una materia...</option>
                <?php foreach ($listaMaterias as $materia): ?>
                    <option value="<?php echo htmlspecialchars($materia->ID, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($materia->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <label for='idC_MATERIA'>ID Materia Seleccionada:</label><br>
            <input type="number" id="idC_MATERIA" name="nmMATERIA" readonly />
        </article>

        <article>
            <label for='idC_PROFESOR_ORI'>PROFESOR: </label><br>
            <select id="idC_PROFESOR_ORI" name="nmC_PROFESOR_ORI">
                <option value="">Seleccione un profesor...</option>
                <?php foreach ($listaProfesores as $profesor): ?>
                    <option value="<?php echo htmlspecialchars($profesor->ID, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($profesor->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <label for='idC_PROFESOR'>ID Profesor Seleccionado:</label><br>
            <input type="number" id="idC_PROFESOR" name="nmPROFESOR" readonly />
        </article>

        <article>
            <label for='idACTIVO'>ACTIVO: </label><br>
            <select id="idACTIVO" name="nmACTIVO">
                <option value="1">TRUE</option>
                <option value="0">FALSE</option>
            </select>
        </article>

        <input type="submit" value="Agregar nuevo ítem" />
    </form>
</body>

<script>
    // Sincronizar select de Materia con su input visible pero de solo lectura
    const selectMateria = document.getElementById('idC_MATERIA_ORI');
    const inputMateria = document.getElementById('idC_MATERIA');
    
    inputMateria.value = selectMateria.value;

    selectMateria.addEventListener('change', function() {
        inputMateria.value = this.value;
    });

    // Sincronizar select de Profesor con su input visible pero de solo lectura
    const selectProfesor = document.getElementById('idC_PROFESOR_ORI');
    const inputProfesor = document.getElementById('idC_PROFESOR');
    
    inputProfesor.value = selectProfesor.value;

    selectProfesor.addEventListener('change', function() {
        inputProfesor.value = this.value;
    });
</script>
</html>
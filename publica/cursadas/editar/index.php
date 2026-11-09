<?php
    session_start();
    
    // Capturar el ID enviado por POST desde la tabla principal
    $id = isset($_POST['id']) ? $_POST['id'] : 1;
    
    $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");
    
    $reglaSQL = "SELECT * FROM T_CURSADAS2 WHERE ID = ?;";
    $stmt = $base->prepare($reglaSQL);
    $stmt->execute([$id]);
    
    if (!$stmt) {
        printf("Hubo un error - %s", $base->errorInfo()[2]);
        die();
    }
    
    $todos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $profesoresSQL = "SELECT ID, NOMBRE FROM T_PROFESORES;";
    $accionProfesores = $base->query($profesoresSQL);
    $listaProfesores = $accionProfesores ? $accionProfesores->fetchAll(PDO::FETCH_OBJ) : [];

    $materiasSQL = "SELECT ID, NOMBRE FROM T_MATERIAS;";
    $accionMaterias = $base->query($materiasSQL);
    $listaMaterias = $accionMaterias ? $accionMaterias->fetchAll(PDO::FETCH_OBJ) : [];
?>
<html>
<head>
    <title>Edición de Cursadas</title>
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
        <h1>Edición de Cursadas</h1>
        <nav>
            <button onclick="history.back()">Volver</button>
        </nav>
    </header>
    <section>

<?php
    if (count($todos) == 0):
        printf("<p> No se encontró el registro %d </p>", $id);
    else:
        $encontrado = $todos[0];
        $Id = $encontrado->ID;
        $Anio = $encontrado->ANIO;
        $Materia = $encontrado->C_MATERIA;
        $Profesor = $encontrado->C_PROFESOR;
        $Activo = $encontrado->ACTIVO;
?>

<form action='/cursadas/editar/procesar.php' method="POST">
    <article>
        <label for='idID'>CODIGO: </label><br>
        <input type="text" id="idID" name="nmID" readonly value="<?php echo htmlspecialchars($Id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idANIO'>ANIO: </label><br>
        <select id="idANIO" name="nmANIO">
            <?php 
                $aniosDisponibles = [2025, 2026, 2027, 2028, 2029, 2030];
                foreach ($aniosDisponibles as $anioOption): 
            ?>
                <option value="<?php echo $anioOption; ?>" <?php echo ($anioOption == $Anio) ? 'selected' : ''; ?>>
                    <?php echo $anioOption; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </article>

    <article>
        <label for='idC_MATERIA_ORI'>MATERIA: </label><br>
        <select id="idC_MATERIA_ORI" name="nmC_MATERIA_ORI">
            <option value="">Seleccione una materia...</option>
            <?php foreach ($listaMaterias as $materia): ?>
                <option value="<?php echo htmlspecialchars($materia->ID, ENT_QUOTES, 'UTF-8'); ?>" 
                    <?php echo ($materia->ID == $Materia) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($materia->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for='idC_MATERIA'>ID Materia Seleccionada:</label><br>
        <input type="number" id="idC_MATERIA" name="nmMATERIA" value="<?php echo htmlspecialchars($Materia, ENT_QUOTES, 'UTF-8'); ?>" readonly />
    </article>

    <article>
        <label for='idC_PROFESOR_ORI'>PROFESOR: </label><br>
        <select id="idC_PROFESOR_ORI" name="nmC_PROFESOR_ORI">
            <option value="">Seleccione un profesor...</option>
            <?php foreach ($listaProfesores as $profesor): ?>
                <option value="<?php echo htmlspecialchars($profesor->ID, ENT_QUOTES, 'UTF-8'); ?>" 
                    <?php echo ($profesor->ID == $Profesor) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($profesor->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for='idC_PROFESOR'>ID Profesor Seleccionado:</label><br>
        <input type="number" id="idC_PROFESOR" name="nmPROFESOR" value="<?php echo htmlspecialchars($Profesor, ENT_QUOTES, 'UTF-8'); ?>" readonly />
    </article>
    
    <article>
        <label for='idActivo'>ACTIVO: </label><br>
        <select id="idActivo" name="nmACTIVO">
            <option value="1" <?php echo ($Activo === 'TRUE' || $Activo == 1 || $Activo === true) ? 'selected' : ''; ?>>TRUE</option>
            <option value="0" <?php echo ($Activo === 'FALSE' || $Activo == 0 || $Activo === false) ? 'selected' : ''; ?>>FALSE</option>
        </select>
    </article>

    <input type="submit" value="Modificar ítem" />
</form>

<?php
    endif;
?>
    </section>
</body>
<script>
    // Sincronizar select de Materia con su input visible pero de solo lectura
    const selectMateria = document.getElementById('idC_MATERIA_ORI');
    const inputMateria = document.getElementById('idC_MATERIA');
    
    selectMateria.addEventListener('change', function() {
        inputMateria.value = this.value;
    });

    // Sincronizar select de Profesor con su input visible pero de solo lectura
    const selectProfesor = document.getElementById('idC_PROFESOR_ORI');
    const inputProfesor = document.getElementById('idC_PROFESOR');
    
    selectProfesor.addEventListener('change', function() {
        inputProfesor.value = this.value;
    });
</script>
</html>
<?php
    session_start();

    // Capturar el ID de la cursada enviada por POST desde la tabla principal
    $idCursada = isset($_POST['id']) ? $_POST['id'] : (isset($_POST['idCursada']) ? $_POST['idCursada'] : null);

    if (!$idCursada) {
        $_SESSION["error"] = "No se ha seleccionado ninguna cursada válida.";
        header("location: ../../cargar_notas.php");
        exit;
    }

    $db_path = $_SERVER['DOCUMENT_ROOT'] . '/../CURSADAS.db';
    $base = new PDO("sqlite:" . $db_path);

    // Procesar formulario si se envió (guardar cambios)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_asignacion'])) {
        $alumnos = isset($_POST['alumnos']) ? $_POST['alumnos'] : [];
        $e1 = isset($_POST['e1']) ? $_POST['e1'] : [];
        $e2 = isset($_POST['e2']) ? $_POST['e2'] : [];
        $e3 = isset($_POST['e3']) ? $_POST['e3'] : [];
        $e4 = isset($_POST['e4']) ? $_POST['e4'] : [];
        $tp1 = isset($_POST['tp1']) ? $_POST['tp1'] : [];
        $tp2 = isset($_POST['tp2']) ? $_POST['tp2'] : [];
        $tp3 = isset($_POST['tp3']) ? $_POST['tp3'] : [];
        $tp4 = isset($_POST['tp4']) ? $_POST['tp4'] : [];
        $notaFinal = isset($_POST['nota_final']) ? $_POST['nota_final'] : [];

        try {
            $base->beginTransaction();

            // Limpiar registros anteriores de esta cursada para actualizar con la grilla actual
            $stmtDel = $base->prepare("DELETE FROM T_CURSADAS_A WHERE C_CURSADA = ?");
            $stmtDel->execute([$idCursada]);

            // Preparar sentencia de inserción incluyendo el ID calculado manualmente
            $stmtIns = $base->prepare("INSERT INTO T_CURSADAS_A (ID, C_CURSADA, C_ALUMNO, E1, E2, E3, E4, TP1, TP2, TP3, TP4, NOTA_FINAL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($alumnos); $i++) {
                if (!empty($alumnos[$i])) {
                    // Obtener el último ID actual de la tabla T_CURSADAS_A y sumarle 1
                    $stmtMaxId = $base->query("SELECT MAX(CAST(ID AS INTEGER)) AS MAX_ID FROM T_CURSADAS_A;");
                    $resMaxId = $stmtMaxId ? $stmtMaxId->fetch(PDO::FETCH_OBJ) : null;
                    $nuevoId = ($resMaxId && $resMaxId->MAX_ID !== null) ? intval($resMaxId->MAX_ID) + 1 : 1;

                    $stmtIns->execute([
                        $nuevoId,
                        $idCursada,
                        $alumnos[$i],
                        $e1[$i] ?? '',
                        $e2[$i] ?? '',
                        $e3[$i] ?? '',
                        $e4[$i] ?? '',
                        $tp1[$i] ?? '',
                        $tp2[$i] ?? '',
                        $tp3[$i] ?? '',
                        $tp4[$i] ?? '',
                        $notaFinal[$i] ?? ''
                    ]);
                }
            }

            $base->commit();
            $_SESSION["mensaje"] = "Asignaciones y notas guardadas correctamente.";
            header("location: ../../cargar_notas.php");
            exit;
        } catch (Exception $e) {
            $base->rollBack();
            $_SESSION["error"] = "Error al guardar: " . $e->getMessage();
        }
    }

    // Obtener información de la cursada y su materia
    $stmtCursada = $base->prepare("SELECT C.*, M.NOMBRE AS MNOMBRE FROM T_CURSADAS2 AS C LEFT JOIN T_MATERIAS AS M ON M.ID = C.C_MATERIA WHERE C.ID = ?");
    $stmtCursada->execute([$idCursada]);
    $cursadaInfo = $stmtCursada->fetch(PDO::FETCH_OBJ);

    // Obtener lista general de alumnos
    $stmtAlumnos = $base->query("SELECT ID, NOMBRE FROM T_ALUMNOS;");
    $listaAlumnos = $stmtAlumnos ? $stmtAlumnos->fetchAll(PDO::FETCH_OBJ) : [];

    // Obtener los registros actuales de T_CURSADAS_A para esta cursada
    $stmtAsignados = $base->prepare("SELECT * FROM T_CURSADAS_A WHERE C_CURSADA = ?");
    $stmtAsignados->execute([$idCursada]);
    $alumnosActuales = $stmtAsignados->fetchAll(PDO::FETCH_OBJ);
?>
<html>
<head>
    <title>Cargar Calificaciones</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style>
        [full]{ width: 100% }
        .table-container { width: 100%; overflow-x: auto; margin-bottom: 1.5rem; }
        table { width: 100%; white-space: nowrap; font-size: 0.85rem; }
        th, td { padding: 0.5rem; text-align: center; }
        input[type="text"] { width: 60px; text-align: center; padding: 0.3rem; }
        .btn-eliminar { background-color: #d9534f; color: white; padding: 0.3rem 0.6rem; border: none; cursor: pointer; }
        .btn-agregar { background-color: #4fd961; color: white; padding: 0.5rem 1rem; border: none; cursor: pointer; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <header>
        <h1>Asignación de Alumnos</h1>
        <p><strong>Materia:</strong> <?php echo htmlspecialchars($cursadaInfo->MNOMBRE ?? 'Sin materia'); ?> | <strong>Año:</strong> <?php echo htmlspecialchars($cursadaInfo->ANIO ?? ''); ?></p>
        <nav>
            <button onclick="location.href='../../cargar_notas.php'">Volver</button>
        </nav>
    </header>

    <main>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idCursada); ?>">
            <input type="hidden" name="guardar_asignacion" value="1">

            <h3>Grilla de Alumnos y Calificaciones</h3>
            
            <div class="table-container">
                <table id="grillaAlumnos">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>E1</th>
                            <th>E2</th>
                            <th>E3</th>
                            <th>E4</th>
                            <th>TP1</th>
                            <th>TP2</th>
                            <th>TP3</th>
                            <th>TP4</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alumnosActuales)): ?>
                            <tr id="filaVacia">
                                <td colspan="11" style="text-align: center;">No hay alumnos asignados todavía. Haga clic en "+ Agregar Alumno".</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($alumnosActuales as $reg): ?>
                                <tr>
                                    <td>
                                        <select name="alumnos[]" required>
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($listaAlumnos as $alumno): ?>
                                                <option value="<?php echo $alumno->ID; ?>" <?php echo ($alumno->ID == $reg->C_ALUMNO) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($alumno->NOMBRE); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="e1[]" value="<?php echo htmlspecialchars($reg->E1 ?? ''); ?>"></td>
                                    <td><input type="text" name="e2[]" value="<?php echo htmlspecialchars($reg->E2 ?? ''); ?>"></td>
                                    <td><input type="text" name="e3[]" value="<?php echo htmlspecialchars($reg->E3 ?? ''); ?>"></td>
                                    <td><input type="text" name="e4[]" value="<?php echo htmlspecialchars($reg->E4 ?? ''); ?>"></td>
                                    <td><input type="text" name="tp1[]" value="<?php echo htmlspecialchars($reg->TP1 ?? ''); ?>"></td>
                                    <td><input type="text" name="tp2[]" value="<?php echo htmlspecialchars($reg->TP2 ?? ''); ?>"></td>
                                    <td><input type="text" name="tp3[]" value="<?php echo htmlspecialchars($reg->TP3 ?? ''); ?>"></td>
                                    <td><input type="text" name="tp4[]" value="<?php echo htmlspecialchars($reg->TP4 ?? ''); ?>"></td>
                                    <td><input type="text" name="nota_final[]" value="<?php echo htmlspecialchars($reg->NOTA_FINAL ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <br><br>
            <button type="submit" style="background-color: #4f5ad9; color: white;">Guardar Cambios</button>
        </form>
    </main>

    <script>
        const listaAlumnosJS = <?php echo json_encode($listaAlumnos); ?>;

        function agregarFila() {
            const tbody = document.querySelector('#grillaAlumnos tbody');
            const filaVacia = document.getElementById('filaVacia');
            if (filaVacia) {
                filaVacia.remove();
            }

            let opcionesHTML = '<option value="">Seleccione...</option>';
            listaAlumnosJS.forEach(alum => {
                opcionesHTML += `<option value="${alum.ID}">${alum.NOMBRE}</option>`;
            });

            const nuevaFila = document.createElement('tr');
            nuevaFila.innerHTML = `
                <td>
                    <select name="alumnos[]" required>
                        ${opcionesHTML}
                    </select>
                </td>
                <td><input type="text" name="e1[]" value=""></td>
                <td><input type="text" name="e2[]" value=""></td>
                <td><input type="text" name="e3[]" value=""></td>
                <td><input type="text" name="e4[]" value=""></td>
                <td><input type="text" name="tp1[]" value=""></td>
                <td><input type="text" name="tp2[]" value=""></td>
                <td><input type="text" name="tp3[]" value=""></td>
                <td><input type="text" name="tp4[]" value=""></td>
                <td><input type="text" name="nota_final[]" value=""></td>
                <td>
                    <button type="button" class="btn-eliminar" onclick="eliminarFila(this)">Eliminar</button>
                </td>
            `;
            tbody.appendChild(nuevaFila);
        }

        function eliminarFila(boton) {
            const fila = boton.closest('tr');
            fila.remove();

            const tbody = document.querySelector('#grillaAlumnos tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `<tr id="filaVacia"><td colspan="11" style="text-align: center;">No hay alumnos asignados todavía. Haga clic en "+ Agregar Alumno".</td></tr>`;
            }
        }
    </script>
</body>
</html>
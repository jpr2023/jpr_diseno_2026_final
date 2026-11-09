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
    // Habilitar excepciones en PDO para depurar cualquier fallo SQL
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Procesar formulario si se envió (guardar cambios)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_asignacion'])) {
        $alumnos   = isset($_POST['alumnos']) ? $_POST['alumnos'] : [];
        $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : [];
        $estados   = isset($_POST['estado']) ? $_POST['estado'] : [];

        try {
            $base->beginTransaction();

            // Limpiar registros anteriores de esta cursada para actualizar con la grilla actual
            $stmtDel = $base->prepare("DELETE FROM T_ASISTENCIA WHERE C_CURSADA = ?");
            $stmtDel->execute([$idCursada]);

            // Preparar sentencia de inserción con los campos correctos
            $stmtIns = $base->prepare("INSERT INTO T_ASISTENCIA (ID, C_ALUMNO, C_CURSADA, CANTIDAD, ESTADO) VALUES (?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($alumnos); $i++) {
                if (!empty($alumnos[$i])) {
                    // Obtener el último ID actual de la tabla T_ASISTENCIA y sumarle 1
                    $stmtMaxId = $base->query("SELECT MAX(CAST(ID AS INTEGER)) AS MAX_ID FROM T_ASISTENCIA;");
                    $resMaxId = $stmtMaxId ? $stmtMaxId->fetch(PDO::FETCH_OBJ) : null;
                    $nuevoId = ($resMaxId && $resMaxId->MAX_ID !== null) ? intval($resMaxId->MAX_ID) + 1 : 1;

                    // Asegurar que el estado tome el valor correcto del array correspondiente
                    $estadoActual = isset($estados[$i]) && !empty($estados[$i]) ? $estados[$i] : 'Activo';

                    $stmtIns->execute([
                        $nuevoId,
                        $alumnos[$i],
                        $idCursada,
                        $cantidades[$i] ?? 0,
                        $estadoActual
                    ]);
                }
            }

            $base->commit();
            $_SESSION["mensaje"] = "Asistencias guardadas correctamente.";
            
            header("location: ../../cargar_notas.php");
            exit;

        } catch (Exception $e) {
            $base->rollBack();
            $_SESSION["error"] = "Error al guardar: " . $e->getMessage();
        }
    }

    // Obtener información de la cursada y su materia de forma segura
    $stmtCursada = $base->prepare("SELECT C.*, M.NOMBRE AS MNOMBRE FROM T_CURSADAS2 AS C LEFT JOIN T_MATERIAS AS M ON M.ID = C.C_MATERIA WHERE C.ID = ?");
    $stmtCursada->execute([$idCursada]);
    $cursadaInfo = $stmtCursada->fetch(PDO::FETCH_OBJ);

    if (!$cursadaInfo) {
        $cursadaInfo = (object) ['MNOMBRE' => 'Sin materia', 'ANIO' => ''];
    }

    // Obtener lista general de alumnos
    $stmtAlumnos = $base->query("SELECT ID, NOMBRE FROM T_ALUMNOS;");
    $listaAlumnos = $stmtAlumnos ? $stmtAlumnos->fetchAll(PDO::FETCH_OBJ) : [];

    // Obtener los registros actuales de T_ASISTENCIA para esta cursada
    $stmtAsignados = $base->prepare("SELECT * FROM T_ASISTENCIA WHERE C_CURSADA = ?");
    $stmtAsignados->execute([$idCursada]);
    $alumnosActuales = $stmtAsignados->fetchAll(PDO::FETCH_OBJ);
?>
<html>
<head>
    <title>Cargar Asistencias</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style>
        [full]{ width: 100% }
        .table-container { width: 100%; overflow-x: auto; margin-bottom: 1.5rem; }
        table { width: 100%; white-space: nowrap; font-size: 0.85rem; }
        th, td { padding: 0.5rem; text-align: center; }
        input[type="number"], select { padding: 0.3rem; text-align: center; }
        input[type="number"] { width: 80px; }
        .btn-eliminar { background-color: #d9534f; color: white; padding: 0.3rem 0.6rem; border: none; cursor: pointer; }
        .btn-agregar { background-color: #4fd961; color: white; padding: 0.5rem 1rem; border: none; cursor: pointer; margin-bottom: 1rem; }
        .alert-error { background-color: #ffe6e6; color: #cc0000; padding: 1rem; margin-bottom: 1rem; border-left: 5px solid #cc0000; }
        .alert-success { background-color: #e6ffe6; color: #006600; padding: 1rem; margin-bottom: 1rem; border-left: 5px solid #006600; }
    </style>
</head>
<body>
    <header>
        <h1>Control de Asistencias</h1>
        <p><strong>Materia:</strong> <?php echo htmlspecialchars($cursadaInfo->MNOMBRE ?? 'Sin materia'); ?> | <strong>Año:</strong> <?php echo htmlspecialchars($cursadaInfo->ANIO ?? ''); ?></p>
        <nav>
            <button onclick="location.href='../../cargar_notas.php'">Volver</button>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idCursada); ?>">
            <input type="hidden" name="guardar_asignacion" value="1">

            <h3>Grilla de Asistencias y Estados</h3>
            
            <!--<button type="hidden" class="btn-agregar" onclick="agregarFila()">+ Agregar Alumno</button>-->

            <div class="table-container">
                <table id="grillaAlumnos">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Cantidad de Asistencias</th>
                            <th>Estado</th>
                            <!--<th>Acciones</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alumnosActuales)): ?>
                            <tr id="filaVacia">
                                <td colspan="4" style="text-align: center;">No hay alumnos asignados todavía. Haga clic en "+ Agregar Alumno".</td>
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
                                    <td><input type="number" name="cantidad[]" min="0" value="<?php echo htmlspecialchars($reg->CANTIDAD ?? 0); ?>" required></td>
                                    <td>
                                        <select name="estado[]" required>
                                            <option value="Activo" <?php echo (isset($reg->ESTADO) && strcasecmp($reg->ESTADO, 'Activo') == 0) ? 'selected' : ''; ?>>Activo</option>
                                            <option value="libre" <?php echo (isset($reg->ESTADO) && strcasecmp($reg->ESTADO, 'libre') == 0) ? 'selected' : ''; ?>>libre</option>
                                        </select>
                                    </td>
                                    <!--
                                    <td>
                                        <button readonly type="button" class="btn-eliminar" onclick="eliminarFila(this)">Eliminar</button>
                                    </td>
                                            -->
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <br>
            <button type="submit" style="background-color: #4f5ad9; color: white;">Guardar Cambios</button>
        </form>
    </main>

    <script>
        const listaAlumnosJS = <?php echo json_encode($listaAlumnos); ?>;

        function agregarFila() {
            /*
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
                <td><input type="number" name="cantidad[]" min="0" value="0" required></td>
                <td>
                    <select name="estado[]" required>
                        <option value="Activo" selected>Activo</option>
                        <option value="libre">libre</option>
                    </select>
                </td>
                <td>
                    <button readonly type="button" class="btn-eliminar" onclick="eliminarFila(this)">Eliminar</button>
                </td>
            `;
            tbody.appendChild(nuevaFila);
            */
        }

        function eliminarFila(boton) {
            /*
            const fila = boton.closest('tr');
            fila.remove();

            const tbody = document.querySelector('#grillaAlumnos tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `<tr id="filaVacia"><td colspan="4" style="text-align: center;">No hay alumnos asignados todavía. Haga clic en "+ Agregar Alumno".</td></tr>`;
            }
                */
        }
    </script>
</body>
</html>
<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    
    $reglaSQL = "   SELECT A.*, T.NOMBRE AS NOMBRET, C.NOMBRE AS NOMBREC 
                    FROM T_ALUMNOS AS A
                    LEFT JOIN T_TUTORES AS T ON T.ID = A.TUTOR
                    LEFT JOIN T_CARRERAS AS C ON C.ID = A.TUTOR 
                    WHERE A.ID='$id';" ;
    $accion = $base->query( $reglaSQL ) ;
    if( !$accion ):
        printf( "Hubo un error - %s", $base->errorInfo()[2] ) ;
        die() ;
    endif;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;

    $tutoresSQL = "SELECT ID, NOMBRE FROM T_TUTORES;";
    $accionTutores = $base->query($tutoresSQL);
    $listaTurores = $accionTutores ? $accionTutores->fetchAll(PDO::FETCH_OBJ) : [];

    $CarrerasSQL = "SELECT ID, NOMBRE FROM T_CARRERAS;";
    $accionCarreras = $base->query($CarrerasSQL);
    $listaCarreras = $accionCarreras ? $accionCarreras->fetchAll(PDO::FETCH_OBJ) : [];
?>
<html>
    
<head>
    <title>X</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style>[full]{ width: 100% }</style>
</head>

    <body>
        <header>
            <h1>Eliminación de Alumnos</h1>
            <nav>
                <button onclick="history.back()">Volver</button>
            </nav>
        </header>
        <section>

<?php
    if( count($todos) == 0 ):
        printf( "<p> No se encontró el registro %d </p>", $id );
    else:
        $encontrado = $todos[0] ;
        $Id = $encontrado->ID;
        $Nombre = $encontrado->NOMBRE;
        $Activo = $encontrado->ACTIVO;
        $FechaNac = $encontrado->F_NACIMIENTO;
        $Direccion = $encontrado->DIRECCION;
        /* Desplegables de turores*/
        $IDTutores = $encontrado->TUTOR;
        $NOMTutores = $encontrado->NOMBRET; 
        /* Desplegable de carreras*/
        $IDCarrera = $encontrado->CARRERA;
        $NOMCarrera = $encontrado->NOMBREC; 
    endif;

    // Asegurar el formato correcto YYYY-MM-DD para el input type="date"
    $fechaFormateada = "";
    if (!empty($FechaNac)) {
        $dt = DateTime::createFromFormat('Y-m-d', $FechaNac);
        if ($dt) {
            $fechaFormateada = $dt->format('Y-m-d');
        } else {
            // Si viene con hora o en otro formato, intenta parsearlo de forma genérica
            $dtAlt = new DateTime($FechaNac);
            $fechaFormateada = $dtAlt->format('Y-m-d');
        }
    }
?>

<form action='/ALUMNOS/borrar/procesar.php' method="get">
    <article>
        <label for='idAlumno'>CODIGO: </label><br>
        <input type="TEXT" id="idAlumno" name="nmID" readonly value="<?php echo htmlspecialchars($Id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE" readonly value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>
    
    <article>
        <label for='idActivo'>ACTIVO: </label><br>
        <select id="idActivo" name="nmACTIVO">
            <option value="TRUE" <?php echo ($Activo === 'TRUE' || $Activo == 1 || $Activo === true) ? 'selected' : ''; ?>>TRUE</option>
            <option value="FALSE" <?php echo ($Activo === 'FALSE' || $Activo == 0 || $Activo === false) ? 'selected' : ''; ?>>FALSE</option>
        </select>
    </article>

    <article>
        <label for='idFechaNac'>FECHA DE NACIMIENTO: </label><br>
        <input type="date" id="idFechaNac" name="nmFECHANAC" readonly value="<?php echo htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  

    <article>
        <label for='idDireccion'>DIRECCION: </label><br>
        <input type="TEXT" id="idDireccion" name="nmDIRECCION" readonly value="<?php echo htmlspecialchars($Direccion, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  

    <article>
        <label for='idTutor'>PERFIL: </label><br>
        <select id="idTutor_ORI" name="nmTutor_ORI">
            <?php foreach ($listaTurores as $p): ?>
                <option value="<?php echo htmlspecialchars($p->ID, ENT_QUOTES, 'UTF-8'); ?>" 
                    <?php echo ($p->ID == $IDTutores) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="idTutor" name="nmTUTOR" value="<?php echo htmlspecialchars($IDTutores, ENT_QUOTES, 'UTF-8'); ?>" readonly />
    </article>

    <article>
        <label for='idCarrera'>CARRERA: </label><br>
        <select id="idCarrera_ORI" name="nmCarrera_ORI">
            <?php foreach ($listaCarreras as $p): ?>
                <option value="<?php echo htmlspecialchars($p->ID, ENT_QUOTES, 'UTF-8'); ?>" 
                    <?php echo ($p->ID == $IDCarrera) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="idCarrera" name="nmCARRERA" value="<?php echo htmlspecialchars($IDCarrera, ENT_QUOTES, 'UTF-8'); ?>" readonly />
    </article>

    <input type="submit" value="Eliminar ítem" />
</form> 
   </body>
   <script>
    
    //const selectTutor = document.getElementById('idTutor_ORI');
    //const inputTutor = document.getElementById('idTutor');

    selectTutor.addEventListener('change', function() {
        inputTutor.value = this.value;
    });

    //const selectCarrera = document.getElementById('idCarrera_ORI');
    //const inputCarrera = document.getElementById('idCarrera');

    selectCarrera.addEventListener('change', function() {
        inputCarrera.value = this.value;
    });
</script>
</html>
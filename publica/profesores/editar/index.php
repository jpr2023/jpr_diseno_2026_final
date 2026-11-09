<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    
    $reglaSQL = "   SELECT *
                    FROM T_PROFESORES AS A
                    WHERE A.ID='$id';" ;
    $accion = $base->query( $reglaSQL ) ;
    if( !$accion ):
        printf( "Hubo un error - %s", $base->errorInfo()[2] ) ;
        die() ;
    endif;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;
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
            <h1>Edición de Profesores</h1>
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
        $Tel = $encontrado->TELEFONO; 
        $Direccion = $encontrado->DIRECCION;
        $Titulo = $encontrado->TITULO;
        $FechaNac = $encontrado->F_NACIMIENTO;
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

<form action='/PROFESORES/editar/procesar.php' method="get">
    <article>
        <label for='idID'>CODIGO: </label><br>
        <input type="TEXT" id="idID" name="nmID" readonly value="<?php echo htmlspecialchars($Id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE" value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>
    
    <article>
        <label for='idActivo'>ACTIVO: </label><br>
        <select id="idActivo" name="nmACTIVO">
            <option value="TRUE" <?php echo ($Activo === 'TRUE' || $Activo == 1 || $Activo === true) ? 'selected' : ''; ?>>TRUE</option>
            <option value="FALSE" <?php echo ($Activo === 'FALSE' || $Activo == 0 || $Activo === false) ? 'selected' : ''; ?>>FALSE</option>
        </select>
    </article>

    <article>
        <label for='idTEL'>NOMBRE: </label><br>
        <input type="TEXT" id="idTEL" name="nmTEL" value="<?php echo htmlspecialchars($Tel, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idDireccion'>DIRECCION: </label><br>
        <input type="TEXT" id="idDireccion" name="nmDIRECCION" value="<?php echo htmlspecialchars($Direccion, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  

    <article>
        <label for='idFechaNac'>FECHA DE NACIMIENTO: </label><br>
        <input type="date" id="idFechaNac" name="nmFECHANAC" value="<?php echo htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  

    <article>
        <label for='idTITULO'>TITULO: </label><br>
        <input type="TEXT" id="idTITULO" name="nmTITULO" value="<?php echo htmlspecialchars($Titulo, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <input type="submit" value="Modificar ítem" />
</form>
   </body>
</html>
<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    
    $reglaSQL = "   SELECT *
                    FROM T_MATERIAS
                    WHERE ID='$id';" ;
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
            <h1>Edición de Materias</h1>
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
        $Curso = $encontrado->CURSO;
        $Activo = $encontrado->ACTIVO;
    endif;

?>

<form action='/MATERIAS/editar/procesar.php' method="get">
    <article>
        <label for='idID'>CODIGO: </label><br>
        <input type="TEXT" id="idID" name="nmID" readonly value="<?php echo htmlspecialchars($Id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE" value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>
    
    <article>
        <label for='idCurso'>CURSO: </label><br>
        <select id="idCurso" name="nmCURSO">
            <option value="1" <?php echo ($Curso == 1) ? 'selected' : ''; ?>>1 - Primero</option>
            <option value="2" <?php echo ($Curso == 2) ? 'selected' : ''; ?>>2 - Segundo</option>
            <option value="3" <?php echo ($Curso == 3) ? 'selected' : ''; ?>>3 - Tercero</option>
            <option value="4" <?php echo ($Curso == 4) ? 'selected' : ''; ?>>4 - Cuarto</option>
            <option value="5" <?php echo ($Curso == 5) ? 'selected' : ''; ?>>5 - Quinto</option>
            <option value="6" <?php echo ($Curso == 6) ? 'selected' : ''; ?>>6 - Sexto</option>
            <option value="7" <?php echo ($Curso == 7) ? 'selected' : ''; ?>>7 - Septimo</option>
        </select>
    </article>

    <article>
        <label for='idActivo'>ACTIVO: </label><br>
        <select id="idActivo" name="nmACTIVO">
            <option value="TRUE" <?php echo ($Activo === 'TRUE' || $Activo == 1 || $Activo === true) ? 'selected' : ''; ?>>TRUE</option>
            <option value="FALSE" <?php echo ($Activo === 'FALSE' || $Activo == 0 || $Activo === false) ? 'selected' : ''; ?>>FALSE</option>
        </select>
    </article>

    <input type="submit" value="Modificar ítem" />
</form>
   </body>
</html>
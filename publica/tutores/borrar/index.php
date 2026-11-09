<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    
    $reglaSQL = "   SELECT *
                    FROM T_TUTORES AS A
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
            <h1>Eliminación de Tutores</h1>
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
        $Tel = $encontrado->TELEFONO;
        $Direccion = $encontrado->DIRECCION;
        $Email = $encontrado->EMAIL;
    endif;

?>

<form action='/TUTORES/borrar/procesar.php' method="get">
    <article>
        <label for='idID'>CODIGO: </label><br>
        <input type="TEXT" id="idID" name="nmID" readonly value="<?php echo htmlspecialchars($Id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE" readonly value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>
    
    <article>
        <label for='idTEL'>TELEFONO: </label><br>
        <input type="TEXT" id="idTEL" name="nmTEL" readonly value="<?php echo htmlspecialchars($Tel, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idDireccion'>DIRECCION: </label><br>
        <input type="TEXT" id="idDireccion" name="nmDIRECCION" readonly value="<?php echo htmlspecialchars($Direccion, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  

    <article>
        <label for='idEmail'>E-MAIL: </label><br>
        <input type="TEXT" id="idEmail" name="nmEMAIL" readonly value="<?php echo htmlspecialchars($Email, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>  


    <input type="submit" value="Eliminar ítem" />
</form>
   </body>
</html>
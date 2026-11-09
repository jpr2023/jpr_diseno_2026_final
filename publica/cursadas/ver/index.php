<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    $reglaSQL = "SELECT * FROM T_CURSADAS WHERE ID='$id';" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( !$accion ):
        printf( "Hubo un error - %s", $base->errorInfo()[2] ) ;
        die() ;
    endif;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;
?>
<html >
    
<head>
    <title >Tu App</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style >[full]{ width: 100% }</style>
</head>

    <body >
        <header >
            <h1>Viendo un T_CURSADAS</h1>
            <nav>
                <button onclick="history.back()">Volver</button>
            </nav>
        </header>
        <section >
<?php
    if( count($todos) == 0 ):
        printf( "<p> No se encontró el registro %d </p>", $id );
    else:
        $encontrado = $todos[0] ;
        printf( "<details ><pre>%s</pre></details>", json_encode( $encontrado ) ) ;
            printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "ID", $encontrado->ID );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "ANIO", $encontrado->ANIO );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "C_MATERIA", $encontrado->C_MATERIA );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "C_PROFESOR", $encontrado->C_PROFESOR );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "C_ALUMNO", $encontrado->C_ALUMNO );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "E1", $encontrado->E1 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "E2", $encontrado->E2 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "E3", $encontrado->E3 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "E4", $encontrado->E4 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "TP1", $encontrado->TP1 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "TP2", $encontrado->TP2 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "TP3", $encontrado->TP3 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "TP4", $encontrado->TP4 );
printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "NOTA_FINAL", $encontrado->NOTA_FINAL );

    endif;
?>
    </body>
</html>

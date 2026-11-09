<?php
    session_start() ;
    $url = (object)$_GET ;
    $reglaSQL = "SELECT * FROM T_CARRERAS" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;
    header( "Content-type: application/json" ) ;
    print json_encode( $todos, null, 4 ) ;
?>
    
<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "INSERT INTO T_TUTORES VALUES ( 
    '$form->nmID'
    , '$form->nmNOMBRE'
	, '$form->nmTELEFONO'
	, '$form->nmDIRECCION'
	, '$form->nmEMAIL'
)" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "T_TUTORES agregad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;

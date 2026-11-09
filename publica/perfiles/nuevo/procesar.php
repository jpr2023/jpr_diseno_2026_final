<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "INSERT INTO T_PERFILES VALUES ( 
    '$form->nmID'
    , '$form->nmNOMBRE'
	, '$form->nmNIVEL'
	, '$form->nmACTIVO'
)" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "T_PERFILES agregad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;

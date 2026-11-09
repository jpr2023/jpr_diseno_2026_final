<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "DELETE FROM T_USUARIOS WHERE USUARIO = '$form->nmUsuario'" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "T_USUARIOS Eliminado@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
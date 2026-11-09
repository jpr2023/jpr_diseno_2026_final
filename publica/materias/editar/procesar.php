<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "   UPDATE T_MATERIAS SET  
                    NOMBRE = '$form->nmNOMBRE'
                    ,CURSO = '$form->nmCURSO'
                    ,ACTIVO = '$form->nmACTIVO'
                    WHERE ID = '$form->nmID'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "MATERIA actualizada" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;

<?php
    session_start() ;
    $form = (object)$_POST ;
    $reglaSQL = "   UPDATE T_CURSADAS2 SET  
                    ANIO = '$form->nmANIO'
                    ,C_MATERIA = '$form->nmMATERIA'
                    ,C_PROFESOR = '$form->nmPROFESOR'
                    ,ACTIVO = '$form->nmACTIVO'
                    WHERE ID = '$form->nmID'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "Cursada actualizada" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;

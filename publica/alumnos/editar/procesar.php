<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "   UPDATE T_ALUMNOS SET  
                    NOMBRE = '$form->nmNOMBRE'
                    ,ACTIVO = '$form->nmACTIVO'
                    ,F_NACIMIENTO = '$form->nmFECHANAC'
                    ,DIRECCION = '$form->nmDIRECCION'
                    ,TUTOR = '$form->nmTUTOR'
                    ,CARRERA = '$form->nmCARRERA'
                    WHERE ID = '$form->nmALUMNO'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "ALUMNO actualizad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;

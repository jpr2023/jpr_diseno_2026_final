<link rel="stylesheet" href="./estilosuser.css">
<article id="idArticulo_<?= $cadaRegistro->USUARIO ?>" >
    <h2><?= $cadaRegistro->NOMBRE ?></h2>
    <nav>
        <button onclick="location.href='/usuarios/ver?id=<?= $cadaRegistro->USUARIO ?>'">Más info</button>
        <button onclick="location.href='/usuarios/editar?id=<?= $cadaRegistro->USUARIO ?>'">Modificar</button>
        <button onclick="location.href='/usuarios/borrar?id=<?= $cadaRegistro->USUARIO ?>'">Eliminar</button>
    </nav>
</article>

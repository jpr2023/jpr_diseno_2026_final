<article id="idArticulo_<?= $cadaRegistro->ID ?>" >
    <h2><?= $cadaRegistro->NOMBRE ?></h2>
    <nav>
        <button onclick="location.href='/carreras/ver?id=<?= $cadaRegistro->ID ?>'">Más info</button>
        <button onclick="location.href='/carreras/editar?id=<?= $cadaRegistro->ID ?>'">Editar</button>
        <button onclick="location.href='/carreras/borrar?id=<?= $cadaRegistro->ID ?>'">Eliminar</button>
    </nav>
</article>

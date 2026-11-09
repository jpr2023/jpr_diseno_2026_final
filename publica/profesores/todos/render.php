<article id="idArticulo_<?= $cadaRegistro->ID ?>" >
    <h2><?= $cadaRegistro->NOMBRE ?></h2>
    <nav>
        <button onclick="location.href='/profesores/ver?id=<?= $cadaRegistro->ID ?>'">Más info</button>
        <button onclick="location.href='/profesores/editar?id=<?= $cadaRegistro->ID ?>'">Editar</button>
        <button onclick="location.href='/profesores/borrar?id=<?= $cadaRegistro->ID ?>'">Borrar</button>
    </nav>
</article>

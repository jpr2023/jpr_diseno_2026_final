<article id="idArticulo_<?= $cadaRegistro->ID ?>" >
    <h2><?= $cadaRegistro->NOMBRE ?></h2>
    <nav>
        <button onclick="location.href='/tutores/ver?id=<?= $cadaRegistro->ID ?>'">Más info</button>
        <button onclick="location.href='/tutores/editar?id=<?= $cadaRegistro->ID ?>'">Editar</button>
        <button onclick="location.href='/tutores/borrar?id=<?= $cadaRegistro->ID ?>'">Borrar</button>
    </nav>
</article>

<article id="idArticulo_<?= $cadaRegistro->ID ?>" >
    <h2><?= $cadaRegistro->ANIO ?></h2>
    <h2><?= $cadaRegistro->MNOMBRE ?></h2>
    <nav>
        <button onclick="location.href='/cursadas/ver?id=<?= $cadaRegistro->ID ?>'">Más info</button>
    </nav>
</article>

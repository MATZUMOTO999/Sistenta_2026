<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="card">
    <h3> Lista Producto</h3>
     <table class="table-bordered m-3">
        <thead>
            <tr>
                <th>Nro</th>
                <th>Categoría</th>
                <th> Código </th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $contador =0;
            foreach ($productos as $producto){
                $$contador+=1;
                ?>
                <tr>
                    <td><?=  $contador ?></td>
                    <td><?=  $producto['nombre'] ?></td>
                    <td><?=  $producto['precio'] ?></td>
                    <td><?=  $producto['stock'] ?></td>
                    <td><?=  $producto['categoria'] ?></td>
                    <td><?=  $producto['codigo'] ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/productos/editar/<?= $producto['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="<?= BASE_URL ?>/admin/productos/eliminar/<?= $producto['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            }
        </tbody>
     </table>
 
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
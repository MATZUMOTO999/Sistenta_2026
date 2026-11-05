<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="card">
    <h3> Lista Producto</h3>
    <br>
    <a class="btn btn-outline-success col-2" href="<?= BASE_URL ?>/admin/productos/nuevo">Nuevo Producto</a>

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
            $contador = 0;
            foreach ($productos as $producto):
                $contador++;
            ?>
                <tr>
                    <td><?= $contador ?></td>
                    <td><?= htmlspecialchars($producto['categoria_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($producto['codigo_sku'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $producto['precio_venta'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $producto['stock_actual'], ENT_QUOTES, 'UTF-8') ?></td>
                    
                    <td>
                        <a class="btn btn-outline-primary" title="Editar" href="<?= BASE_URL ?>/admin/productos/editar/<?= $producto['id'] ?>" ><i class="fa fa-edit"></i class="fa fa-edit"> </a>
                        <a class="btn btn-outline-danger" title="Eliminar" href="<?= BASE_URL ?>/admin/productos/eliminar/<?= $producto['id'] ?>" data-sigi-confirm onclick="return confirm('!Estás seguro de que deseas eliminar este producto¡')"><i class="fa fa-trash"></i></a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
     </table>
 
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
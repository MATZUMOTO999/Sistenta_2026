<?php require __DIR__ . '/../../layouts/header.php'; ?>
<div class="card p-3">
    <h3>Registrar Nuevo Producto</h3>
    <form action ="" method="POST">
        <div class="col-md-4 form-group">
            <label for="">Categoria :</label>
            <select name="" id="">

            </select>
        </div>
        <!--contenedor del Codigo SKU-->
        <div class="col-md-4 form-group">
            <label for="">Codigo SKU :</label>
            <input type="text" name="" id="" class="form-control">
        </div>
        <!--contenedor del Nombre-->
        <div class="col-md-4 form-group">
            <label for="">Nombre :</label>
            <input type="text" name="" id="" class="form-control">
        </div>
        <!--contenedor del Descripcion-->
        <div class="col-md-4 form-group">
            <label for="">Descripcion :</label>
            <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>
        </div>
        <!--contenedor del Precio de Venta-->   
        <div class="col-md-4 form-group">
            <label for="">Precio de Venta :</label>
            <input type="text" name="" id="" class="form-control">
        </div>
        <!--contenedor del Stock Actual-->
        <div class="col-md-4 form-group">   
            <label for="">Stock Actual :</label>
            <input type="text" name="" id="" class="form-control">
        </div>
        <!--contenedor del Activo-->    
        <div class="col-md-4 form-group">
            <label for="">Activo :</label>
            <select name="" id="" class="form-control">
                <option value="">Seleccione</option>
                <option value="">Si</option>
                <option value="">No</option>
            </select>  
    </form>
</div>    
    <!-- Aquí puedes agregar la tabla o lista de productos -->    
<?php require __DIR__ . '/../../layouts/footer.php'; ?>

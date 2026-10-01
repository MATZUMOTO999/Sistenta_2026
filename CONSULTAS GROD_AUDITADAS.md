# Consultas SQL CRUD auditadas

Consultas preparadas para la base `tienda_virtual`, contrastadas con el esquema de `tienda_virtual.sql` y el documento `CONSULTAS GROD.pdf`.

## Alcance y validacion

- El esquema contiene 11 tablas: `categorias`, `productos`, `ventas`, `detalle_ventas`, `usuarios`, `direcciones_usuario`, `imagenes_producto`, `roles`, `permisos`, `rol_permisos` y `sesiones`.
- Las consultas se comprobaron contra MySQL 8.4.7 en `localhost`.
- Se verifico la sintaxis y resolucion de los `SELECT` con `EXPLAIN`, y se prepararon las sentencias de escritura sin ejecutarlas.
- Se consultaron las referencias existentes para confirmar errores de clave primaria y claves foraneas. No se modificaron datos.

## Como usar los parametros

Los signos `?` representan parametros posicionales de consultas preparadas (`PDO` o `mysqli`). En phpMyAdmin no se ejecutan literalmente: reemplaza cada `?` por un valor del tipo correcto, en el mismo orden. Usa consultas preparadas desde la aplicacion para evitar inyeccion SQL. Ejecuta cada bloque por separado, excepto las transacciones indicadas expresamente.

## Hallazgos corregidos del PDF

- En `rol_permisos`, insertar `(1, 1)` falla porque ese par ya existe. Actualizarlo a `(1, 2)` tambien falla porque `(1, 2)` ya existe y la clave primaria es compuesta (`rol_id`, `permiso_id`). Los ejemplos corregidos usan parametros y exigen un par destino libre.
- Insertar un detalle para `venta_id = 1` falla en la base validada: no hay ventas cargadas y la clave foranea requiere una venta existente. Primero se crea la venta y luego sus detalles dentro de una transaccion.
- Borrar la categoria `id = 1` falla porque tiene un producto asociado. Borrar el rol `id = 1` falla porque cinco usuarios lo tienen asignado. Las consultas de borrado incluyen una condicion para evitar esos casos.
- Las lecturas principales ahora muestran los datos relacionados mediante `JOIN`; los detalles de venta se muestran junto a su venta, cliente y producto.

## 1. Categorias

```sql
-- CREATE
INSERT INTO categorias (nombre, descripcion, activo)
VALUES (?, ?, ?);

-- READ
SELECT id, nombre, descripcion, activo
FROM categorias
ORDER BY nombre;

-- UPDATE
UPDATE categorias
SET nombre = ?, descripcion = ?, activo = ?
WHERE id = ?;

-- DELETE: solo si no hay productos asociados
DELETE FROM categorias
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM productos WHERE categoria_id = ?);
```

## 2. Productos

```sql
-- CREATE
INSERT INTO productos
    (categoria_id, codigo_sku, nombre, descripcion, precio_venta, stock_actual, activo)
VALUES (?, ?, ?, ?, ?, ?, ?);

-- READ: producto, categoria e imagen principal
SELECT p.id, p.codigo_sku, p.nombre, p.descripcion, p.precio_venta,
       p.stock_actual, p.activo, c.nombre AS categoria,
       ip.url_imagen AS imagen_principal
FROM productos AS p
JOIN categorias AS c ON c.id = p.categoria_id
LEFT JOIN imagenes_producto AS ip
       ON ip.producto_id = p.id AND ip.es_principal = 1
ORDER BY p.nombre;

-- UPDATE
UPDATE productos
SET categoria_id = ?, codigo_sku = ?, nombre = ?, descripcion = ?,
    precio_venta = ?, stock_actual = ?, activo = ?
WHERE id = ?;

-- DELETE: solo si el producto no aparece en detalles de venta
DELETE FROM productos
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM detalle_ventas WHERE producto_id = ?);
```

## 3. Ventas

```sql
-- CREATE individual; para registrar la venta con sus detalles, usar la transaccion al final de esta seccion
INSERT INTO ventas (usuario_id, total_venta, estado, metodo_pago)
VALUES (?, ?, ?, ?);

-- READ: una fila por producto vendido; incluye ventas sin detalles
SELECT v.id AS venta_id, v.fecha_venta, v.estado, v.metodo_pago,
       v.total_venta, u.id AS usuario_id, u.nombre, u.apellidos,
       dv.id AS detalle_id, p.codigo_sku, p.nombre AS producto,
       dv.cantidad, dv.precio_unitario, dv.subtotal
FROM ventas AS v
JOIN usuarios AS u ON u.id = v.usuario_id
LEFT JOIN detalle_ventas AS dv ON dv.venta_id = v.id
LEFT JOIN productos AS p ON p.id = dv.producto_id
ORDER BY v.fecha_venta DESC, v.id, dv.id;

-- READ: una venta especifica
SELECT v.id AS venta_id, v.fecha_venta, v.estado, v.metodo_pago,
       v.total_venta, u.nombre, u.apellidos, dv.cantidad,
       dv.precio_unitario, dv.subtotal, p.nombre AS producto
FROM ventas AS v
JOIN usuarios AS u ON u.id = v.usuario_id
LEFT JOIN detalle_ventas AS dv ON dv.venta_id = v.id
LEFT JOIN productos AS p ON p.id = dv.producto_id
WHERE v.id = ?;

-- UPDATE: cambiar el estado
UPDATE ventas
SET estado = ?
WHERE id = ?;

-- DELETE: sus detalles se eliminan en cascada segun la clave foranea
DELETE FROM ventas
WHERE id = ?;
```

## 4. Detalles de venta

```sql
-- CREATE: la venta y el producto deben existir
INSERT INTO detalle_ventas
    (venta_id, producto_id, cantidad, precio_unitario, subtotal)
VALUES (?, ?, ?, ?, ROUND(? * ?, 2));
-- Los dos ultimos parametros son cantidad y precio_unitario, repetidos para calcular subtotal.

-- READ: detalle, venta, cliente y producto
SELECT dv.id AS detalle_id, dv.venta_id, v.fecha_venta, v.estado,
       u.nombre, u.apellidos, p.id AS producto_id, p.nombre AS producto,
       dv.cantidad, dv.precio_unitario, dv.subtotal
FROM detalle_ventas AS dv
JOIN ventas AS v ON v.id = dv.venta_id
JOIN usuarios AS u ON u.id = v.usuario_id
JOIN productos AS p ON p.id = dv.producto_id
ORDER BY dv.venta_id, dv.id;

-- UPDATE: cantidad, precio y subtotal se calculan con los nuevos valores
UPDATE detalle_ventas
SET cantidad = ?, precio_unitario = ?,
    subtotal = ROUND(? * ?, 2)
WHERE id = ?;
-- Para subtotal, repetir cantidad y precio_unitario como parametros.

-- DELETE
DELETE FROM detalle_ventas
WHERE id = ?;

-- Ejecutar despues de actualizar o borrar un detalle, en la misma transaccion
UPDATE ventas
SET total_venta = (
    SELECT COALESCE(SUM(subtotal), 0.00)
    FROM detalle_ventas
    WHERE venta_id = ?
)
WHERE id = ?;
```

### Registrar una venta con detalle

Ejecuta este flujo como una sola transaccion. Repite el `INSERT` de detalle por cada producto y confirma con `COMMIT` solo si todos los pasos terminan correctamente; ante un error, usa `ROLLBACK`.

```sql
START TRANSACTION;

INSERT INTO ventas (usuario_id, total_venta, estado, metodo_pago)
VALUES (?, 0.00, 'Pendiente', ?);

SET @venta_nueva_id = LAST_INSERT_ID();

INSERT INTO detalle_ventas
    (venta_id, producto_id, cantidad, precio_unitario, subtotal)
VALUES (@venta_nueva_id, ?, ?, ?, ROUND(? * ?, 2));

UPDATE ventas
SET total_venta = (
    SELECT COALESCE(SUM(subtotal), 0.00)
    FROM detalle_ventas
    WHERE venta_id = @venta_nueva_id
)
WHERE id = @venta_nueva_id;

COMMIT;
-- Si falla cualquier sentencia anterior al COMMIT, ejecutar ROLLBACK.
```

El esquema actual no descuenta stock automaticamente. Si la tienda debe controlar inventario, la validacion y el descuento deben agregarse dentro de esta misma transaccion.

## 5. Usuarios

```sql
-- CREATE: password_hash debe ser un hash seguro generado por la aplicacion
INSERT INTO usuarios
    (rol_id, nombre, apellidos, email, password_hash, telefono,
     activo, reset_password, token_password)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);

-- READ: no exponer password_hash ni token_password
SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono,
       u.fecha_registro, u.activo, r.nombre AS rol
FROM usuarios AS u
JOIN roles AS r ON r.id = u.rol_id
ORDER BY u.apellidos, u.nombre;

-- UPDATE: no modifica credenciales
UPDATE usuarios
SET rol_id = ?, nombre = ?, apellidos = ?, email = ?, telefono = ?, activo = ?
WHERE id = ?;

-- DELETE: direcciones y sesiones dependientes se eliminan en cascada
DELETE FROM usuarios
WHERE id = ?;
```

## 6. Direcciones de usuario

```sql
-- CREATE
INSERT INTO direcciones_usuario
    (usuario_id, direccion, ciudad, codigo_postal, es_principal)
VALUES (?, ?, ?, ?, ?);

-- READ: direccion con su usuario
SELECT d.id AS direccion_id, u.id AS usuario_id, u.nombre, u.apellidos,
       d.direccion, d.ciudad, d.codigo_postal, d.es_principal
FROM direcciones_usuario AS d
JOIN usuarios AS u ON u.id = d.usuario_id
ORDER BY u.id, d.es_principal DESC, d.id;

-- UPDATE
UPDATE direcciones_usuario
SET direccion = ?, ciudad = ?, codigo_postal = ?, es_principal = ?
WHERE id = ?;

-- DELETE
DELETE FROM direcciones_usuario
WHERE id = ?;
```

## 7. Imagenes de producto

```sql
-- CREATE
INSERT INTO imagenes_producto (producto_id, url_imagen, es_principal)
VALUES (?, ?, ?);

-- READ: imagen con su producto
SELECT ip.id AS imagen_id, p.id AS producto_id, p.nombre AS producto,
       ip.url_imagen, ip.es_principal
FROM imagenes_producto AS ip
JOIN productos AS p ON p.id = ip.producto_id
ORDER BY p.id, ip.es_principal DESC, ip.id;

-- UPDATE
UPDATE imagenes_producto
SET url_imagen = ?, es_principal = ?
WHERE id = ?;

-- DELETE
DELETE FROM imagenes_producto
WHERE id = ?;
```

## 8. Roles

```sql
-- CREATE
INSERT INTO roles (nombre, descripcion)
VALUES (?, ?);

-- READ: incluye roles sin permisos asignados
SELECT r.id AS rol_id, r.nombre AS rol, r.descripcion,
       p.id AS permiso_id, p.nombre AS permiso,
       p.descripcion AS detalle_permiso
FROM roles AS r
LEFT JOIN rol_permisos AS rp ON rp.rol_id = r.id
LEFT JOIN permisos AS p ON p.id = rp.permiso_id
ORDER BY r.id, p.id;

-- UPDATE
UPDATE roles
SET nombre = ?, descripcion = ?
WHERE id = ?;

-- DELETE: solo si ningun usuario tiene asignado el rol
DELETE FROM roles
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE rol_id = ?);
```

## 9. Permisos

```sql
-- CREATE
INSERT INTO permisos (nombre, descripcion)
VALUES (?, ?);

-- READ: permisos y roles relacionados
SELECT p.id AS permiso_id, p.nombre AS permiso, p.descripcion,
       r.id AS rol_id, r.nombre AS rol
FROM permisos AS p
LEFT JOIN rol_permisos AS rp ON rp.permiso_id = p.id
LEFT JOIN roles AS r ON r.id = rp.rol_id
ORDER BY p.id, r.id;

-- UPDATE
UPDATE permisos
SET nombre = ?, descripcion = ?
WHERE id = ?;

-- DELETE: las asignaciones relacionadas se eliminan en cascada
DELETE FROM permisos
WHERE id = ?;
```

## 10. Relacion rol-permiso

La clave primaria es compuesta por `rol_id` y `permiso_id`. El par insertado debe ser nuevo; al actualizar, ambos IDs deben existir y el nuevo par no debe estar ocupado.

```sql
-- CREATE
INSERT INTO rol_permisos (rol_id, permiso_id)
VALUES (?, ?);

-- READ
SELECT rp.rol_id, r.nombre AS rol, rp.permiso_id, p.nombre AS permiso
FROM rol_permisos AS rp
JOIN roles AS r ON r.id = rp.rol_id
JOIN permisos AS p ON p.id = rp.permiso_id
ORDER BY rp.rol_id, rp.permiso_id;

-- UPDATE
UPDATE rol_permisos
SET rol_id = ?, permiso_id = ?
WHERE rol_id = ? AND permiso_id = ?;

-- DELETE: identificar la fila con las dos columnas de la clave primaria
DELETE FROM rol_permisos
WHERE rol_id = ? AND permiso_id = ?;
```

## 11. Sesiones

```sql
-- CREATE
INSERT INTO sesiones
    (usuario_id, token, ip_address, user_agent, fecha_expiracion, activa)
VALUES (?, ?, ?, ?, ?, ?);

-- READ: omite el token de sesion
SELECT s.id AS sesion_id, u.id AS usuario_id, u.nombre, u.apellidos,
       s.ip_address, s.user_agent, s.fecha_creacion,
       s.fecha_expiracion, s.activa
FROM sesiones AS s
JOIN usuarios AS u ON u.id = s.usuario_id
ORDER BY s.fecha_creacion DESC;

-- UPDATE: revocar una sesion
UPDATE sesiones
SET activa = 0
WHERE id = ?;

-- DELETE
DELETE FROM sesiones
WHERE id = ?;
```
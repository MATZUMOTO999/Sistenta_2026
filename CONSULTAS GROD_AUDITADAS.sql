-- Auditoria de CONSULTAS GROD.pdf contra tienda_virtual.sql
-- Motor validado en localhost: MySQL 8.4.7
-- Los signos ? son parametros posicionales para consultas preparadas (PDO/mysqli).
-- En phpMyAdmin, reemplazar cada ? por un valor del tipo correcto antes de ejecutar.
-- No ejecutar todo el archivo como un unico script: seleccionar cada consulta.

USE tienda_virtual;

-- 1. categorias
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

-- DELETE: solo elimina categorias que no tengan productos asociados.
DELETE FROM categorias
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM productos WHERE categoria_id = ?);


-- 2. productos
-- CREATE
INSERT INTO productos
    (categoria_id, codigo_sku, nombre, descripcion, precio_venta, stock_actual, activo)
VALUES (?, ?, ?, ?, ?, ?, ?);

-- READ: categoria e imagen principal, sin columnas ambiguas.
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

-- DELETE: evita borrar un producto que figure en detalles de venta.
DELETE FROM productos
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM detalle_ventas WHERE producto_id = ?);


-- 3. ventas
-- CREATE: usar el bloque transaccional de venta + detalle que aparece abajo.
INSERT INTO ventas (usuario_id, total_venta, estado, metodo_pago)
VALUES (?, ?, ?, ?);

-- READ: cabecera, cliente, detalle y producto en una fila por producto vendido.
SELECT v.id AS venta_id, v.fecha_venta, v.estado, v.metodo_pago,
       v.total_venta, u.id AS usuario_id, u.nombre, u.apellidos,
       dv.id AS detalle_id, p.codigo_sku, p.nombre AS producto,
       dv.cantidad, dv.precio_unitario, dv.subtotal
FROM ventas AS v
JOIN usuarios AS u ON u.id = v.usuario_id
LEFT JOIN detalle_ventas AS dv ON dv.venta_id = v.id
LEFT JOIN productos AS p ON p.id = dv.producto_id
ORDER BY v.fecha_venta DESC, v.id, dv.id;

-- READ: una venta concreta, incluyendo ventas sin detalle.
SELECT v.id AS venta_id, v.fecha_venta, v.estado, v.metodo_pago,
       v.total_venta, u.nombre, u.apellidos, dv.cantidad,
       dv.precio_unitario, dv.subtotal, p.nombre AS producto
FROM ventas AS v
JOIN usuarios AS u ON u.id = v.usuario_id
LEFT JOIN detalle_ventas AS dv ON dv.venta_id = v.id
LEFT JOIN productos AS p ON p.id = dv.producto_id
WHERE v.id = ?;

-- UPDATE: cambiar el estado de una venta.
UPDATE ventas
SET estado = ?
WHERE id = ?;

-- DELETE: detalle_ventas se elimina en cascada segun la FK del esquema.
DELETE FROM ventas
WHERE id = ?;


-- 4. detalle_ventas
-- CREATE: venta_id debe pertenecer a una venta existente y producto_id a un producto.
INSERT INTO detalle_ventas
    (venta_id, producto_id, cantidad, precio_unitario, subtotal)
VALUES (?, ?, ?, ?, ROUND(? * ?, 2));
-- Parametros finales repetidos: cantidad y precio_unitario para calcular subtotal.

-- READ: detalle con su venta, cliente y producto.
SELECT dv.id AS detalle_id, dv.venta_id, v.fecha_venta, v.estado,
       u.nombre, u.apellidos, p.id AS producto_id, p.nombre AS producto,
       dv.cantidad, dv.precio_unitario, dv.subtotal
FROM detalle_ventas AS dv
JOIN ventas AS v ON v.id = dv.venta_id
JOIN usuarios AS u ON u.id = v.usuario_id
JOIN productos AS p ON p.id = dv.producto_id
ORDER BY dv.venta_id, dv.id;

-- UPDATE: recalcula subtotal con la cantidad y el precio nuevos.
UPDATE detalle_ventas
SET cantidad = ?, precio_unitario = ?,
    subtotal = ROUND(? * ?, 2)
WHERE id = ?;
-- Parametros para subtotal: cantidad y precio_unitario, repetidos.

-- DELETE
DELETE FROM detalle_ventas
WHERE id = ?;

-- Despues de actualizar o borrar un detalle, recalcular la cabecera dentro
-- de la misma transaccion que modifico el detalle:
UPDATE ventas
SET total_venta = (
    SELECT COALESCE(SUM(subtotal), 0.00)
    FROM detalle_ventas
    WHERE venta_id = ?
)
WHERE id = ?;

-- CREATE de una venta completa: insertar cabecera y detalles en la misma transaccion.
-- Repetir el INSERT de detalle por cada producto y recalcular el total antes del COMMIT.
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
-- Si falla cualquier paso, ejecutar ROLLBACK en lugar de COMMIT.
-- Este esquema no descuenta stock automaticamente; implementarlo dentro de esta
-- transaccion si la aplicacion requiere control de inventario.


-- 5. usuarios
-- CREATE: password_hash debe ser un hash seguro generado por la aplicacion, no texto plano.
INSERT INTO usuarios
    (rol_id, nombre, apellidos, email, password_hash, telefono,
     activo, reset_password, token_password)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);

-- READ: omite password_hash y token_password por ser datos sensibles.
SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono,
       u.fecha_registro, u.activo, r.nombre AS rol
FROM usuarios AS u
JOIN roles AS r ON r.id = u.rol_id
ORDER BY u.apellidos, u.nombre;

-- UPDATE: no modificar credenciales si no se pretende cambiarlas.
UPDATE usuarios
SET rol_id = ?, nombre = ?, apellidos = ?, email = ?, telefono = ?, activo = ?
WHERE id = ?;

-- DELETE: direcciones y sesiones dependientes se eliminan en cascada.
DELETE FROM usuarios
WHERE id = ?;


-- 6. direcciones_usuario
-- CREATE
INSERT INTO direcciones_usuario
    (usuario_id, direccion, ciudad, codigo_postal, es_principal)
VALUES (?, ?, ?, ?, ?);

-- READ: cada direccion con su usuario.
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


-- 7. imagenes_producto
-- CREATE
INSERT INTO imagenes_producto (producto_id, url_imagen, es_principal)
VALUES (?, ?, ?);

-- READ: cada imagen con su producto.
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


-- 8. roles
-- CREATE
INSERT INTO roles (nombre, descripcion)
VALUES (?, ?);

-- READ: roles con permisos; un rol sin permisos tambien aparece.
SELECT r.id AS rol_id, r.nombre AS rol, r.descripcion,
       p.id AS permiso_id, p.nombre AS permiso, p.descripcion AS detalle_permiso
FROM roles AS r
LEFT JOIN rol_permisos AS rp ON rp.rol_id = r.id
LEFT JOIN permisos AS p ON p.id = rp.permiso_id
ORDER BY r.id, p.id;

-- UPDATE
UPDATE roles
SET nombre = ?, descripcion = ?
WHERE id = ?;

-- DELETE: no elimina roles asignados a usuarios.
DELETE FROM roles
WHERE id = ?
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE rol_id = ?);


-- 9. permisos
-- CREATE
INSERT INTO permisos (nombre, descripcion)
VALUES (?, ?);

-- READ: permisos y roles que los tienen asignados.
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

-- DELETE: la FK elimina las asignaciones correspondientes en cascada.
DELETE FROM permisos
WHERE id = ?;


-- 10. rol_permisos (clave primaria compuesta: rol_id + permiso_id)
-- CREATE: asignar un par que todavia no exista.
INSERT INTO rol_permisos (rol_id, permiso_id)
VALUES (?, ?);

-- READ
SELECT rp.rol_id, r.nombre AS rol, rp.permiso_id, p.nombre AS permiso
FROM rol_permisos AS rp
JOIN roles AS r ON r.id = rp.rol_id
JOIN permisos AS p ON p.id = rp.permiso_id
ORDER BY rp.rol_id, rp.permiso_id;

-- UPDATE: los nuevos IDs deben existir y el nuevo par no debe estar ocupado.
UPDATE rol_permisos
SET rol_id = ?, permiso_id = ?
WHERE rol_id = ? AND permiso_id = ?;

-- DELETE: identificar la fila usando ambas columnas de la clave primaria.
DELETE FROM rol_permisos
WHERE rol_id = ? AND permiso_id = ?;


-- 11. sesiones
-- CREATE
INSERT INTO sesiones
    (usuario_id, token, ip_address, user_agent, fecha_expiracion, activa)
VALUES (?, ?, ?, ?, ?, ?);

-- READ: omite el token de sesion de los resultados generales.
SELECT s.id AS sesion_id, u.id AS usuario_id, u.nombre, u.apellidos,
       s.ip_address, s.user_agent, s.fecha_creacion,
       s.fecha_expiracion, s.activa
FROM sesiones AS s
JOIN usuarios AS u ON u.id = s.usuario_id
ORDER BY s.fecha_creacion DESC;

-- UPDATE: revocar una sesion.
UPDATE sesiones
SET activa = 0
WHERE id = ?;

-- DELETE
DELETE FROM sesiones
WHERE id = ?;
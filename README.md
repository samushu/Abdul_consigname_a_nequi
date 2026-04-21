# RumBoss · Gestor de Alquiler de Vehículos
**Arquitectura MVC en PHP + MySQL**

---

## Estructura del proyecto

```
rumboss/
├── index.php                  ← Front Controller (punto de entrada único)
├── database.sql               ← Script de BD con tablas y datos de prueba
│
├── Model/
│   ├── Database.php           ← Conexión PDO singleton
│   ├── ClienteModel.php       ← CRUD clientes
│   ├── VehiculoModel.php      ← CRUD vehículos
│   └── ReservaModel.php       ← Reservas + control de estado
│
├── Controller/
│   ├── ClienteController.php  ← Lógica + validación clientes
│   ├── VehiculoController.php ← Lógica + validación vehículos
│   └── ReservaController.php  ← Lógica + validación reservas
│
└── View/
    ├── shared/
    │   ├── header.php         ← Navbar + CSS global
    │   └── footer.php         ← Cierre HTML
    ├── clientes/
    │   ├── lista.php
    │   └── formulario.php
    ├── vehiculos/
    │   ├── lista.php
    │   └── formulario.php
    └── reservas/
        ├── lista.php
        └── formulario.php
```

---

## Instalación

### 1. Base de datos
```sql
-- En MySQL Workbench, phpMyAdmin o CLI:
source rumboss/database.sql;
```

### 2. Configurar conexión
Editar `Model/Database.php`:
```php
private static string $host = 'localhost';
private static string $db   = 'rumboss';
private static string $user = 'root';
private static string $pass = '';        // tu contraseña
```

### 3. Servidor web
**Opción A — PHP built-in server (desarrollo):**
```bash
cd rumboss
php -S localhost:8000
# Abrir: http://localhost:8000
```

**Opción B — XAMPP / WAMP:**
Copiar la carpeta `rumboss/` a `htdocs/` y acceder a:
`http://localhost/rumboss/`

---

## Módulos disponibles

| Módulo    | URL                              | Operaciones            |
|-----------|----------------------------------|------------------------|
| Vehículos | `index.php?modulo=vehiculos`     | Crear, Editar, Eliminar |
| Clientes  | `index.php?modulo=clientes`      | Crear, Editar, Eliminar |
| Reservas  | `index.php?modulo=reservas`      | Crear, Finalizar, Cancelar |

---

## Notas de diseño

- **Single entry point**: todo pasa por `index.php` usando `?modulo=` y `?accion=`.
- **Validación**: en el Controller, nunca en la View ni en el Model.
- **PDO + prepared statements**: sin SQL injection.
- **Transacciones**: al crear/finalizar/cancelar una reserva se actualiza el estado del vehículo atómicamente.
- **Sessions**: se usan solo para pasar errores de validación de vuelta al formulario (flash messages).

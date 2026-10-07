# CRM empresarial

Sistema para consultar clientes, prospectos que llegan desde la web y permisos de acceso en un solo lugar, y exportar el informe del día a PDF o Excel.

## Qué resuelve

La información de clientes, cobertura y prospectos estaba repartida. Sin roles, cualquier usuario veía o editaba lo mismo. Los informes se armaban fuera del sistema.

## Qué incluye

- Clientes, departamentos y municipios
- Prospectos que entran desde la web
- Acceso por roles
- Exportación de informes a PDF y Excel

## Stack

Laravel, PHP y MySQL.

## Cómo correrlo

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Crea el `.env` local con la conexión MySQL antes de migrar. No lo subas.

No subas el archivo `.env`.

# Proyecto apiviajes

Este es un proyecto con Symfony que implementa una pequeña API y un comando para consultar disponibilidad de vuelos desde un proveedor, 
siguiendo la arquitectura de Puertos y Adaptadores (Hexagonal).

## Tecnologías
- PHP 8.3
- Symfony 7
- PHPUnit (para los tests automatizados)

## Arquitectura
El proyecto sigue una pequeña versión de la **Arquitectura Hexagonal**:
- **Entidad de dominio**: `Segment.php`
- **Puerto**: `ProveedorDisponibilidadInterface.php`
- **Adaptador**: `ProveedorDisponibilidadHttp.php`
- **Caso de uso**: `ConsultarDisponibilidad.php`
- **Entradas**: `DisponibilidadController.php` (API) y `DisponibilidadComando.php` (Comando)

De esta forma, el dominio y los casos de uso no dependen de detalles técnicos (como HTTP), y el adaptador se puede sustituir fácilmente.

## Estructura
- `src/Entity/Segment.php` → Entidad de dominio que representa un vuelo (Segment).
- `src/Proveedor/ProveedorDisponibilidadInterface.php` → Puerto (interfaz) para consultar disponibilidad.
- `src/Proveedor/ProveedorDisponibilidadHttp.php` → Adaptador que implementa el puerto y llama al endpoint HTTP.
- `src/Aplicacion/ConsultarDisponibilidad.php` → Caso de uso que gestiona la consulta de disponibilidad.
- `src/Controller/DisponibilidadController.php` → Controlador con el endpoint `/api/avail`.
- `src/Comando/DisponibilidadComando.php` → Comando que muestra los vuelos (`lleego:avail`).
- `tests/DisponibilidadControllerTest.php` → Tests del controlador.

## Primeros pasos:

**Clonar el repositorio:**
```bash
git clone https://github.com/pabloportillo/apiviajes.git
```

**Instalar dependencias:**
```bash
composer install
```

**Arrancamos el servidor:**

```bash
symfony server:start -d
```

## Endpoint API

**Realizar la petición:**

```bash
curl "http://127.0.0.1:8000/api/avail?origin=MAD&destination=BIO&date=2022-06-01"
```

**Tipo de respuesta JSON:**

```bash
[
  {
    "originCode": "MAD",
    "originName": "MAD",
    "destinationCode": "BIO",
    "destinationName": "BIO",
    "start": "2022-06-01 11:50",
    "end": "2022-06-01 12:55",
    "transportNumber": "0426",
    "companyCode": "IB",
    "companyName": "IB"
  },
  ...
]
```

## Comando
**Ejecutar desde consola:**
```bash
php bin/console lleego:avail MAD BIO 2022-06-01
```

## Tests
**Ejecutar los tests:**
```bash
php bin/phpunit
```

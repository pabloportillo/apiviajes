# Proyecto apiviajes

Este es un proyecto con Symfony que implementa una pequeña API y un comando para consultar disponibilidad de vuelos desde un proveedor.

## Tecnologías
- PHP 8.3
- Symfony 7
- PHPUnit (para los tests automatizados)

## Estructura
- `src/Entity/FlightSegment.php` → Entidad con los campos del modelo Segment.
- `src/Proveedor/ProveedorDisponibilidad.php` → Clase que consulta al proveedor (GET al endpoint) para obtener los vuelos, parsea el XML y devuelve objetos FlightSegment.
- `src/Controller/DisponibilidadController.php` → Controlador con el Endpoint `/api/avail`.
- `src/Comando/DisponibilidadComando.php` → Comando que devuelve los vuelos `lleego:avail`.
- `tests/DisponibilidadControllerTest.php` → Tests del controlador.

## Endpoint API
**Arrancamos el servidor:**

```bash
symfony server:start -d
```

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
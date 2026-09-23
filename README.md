# Caso 4: Agenda de Eventos Académicos

## Integrantes

- Apellido Apellido, Nombre - Carnet
- Apellido Apellido, Nombre - Carnet

## Alcance de esta entrega

Esta etapa se trabaja en pareja y cubre la base de datos y la clase Repository del caso 4. La aplicación web, los formularios HTML, las vistas y el flujo POST/Redirect/Get se agregarán en la siguiente etapa.

## Tecnologías

- PHP 8.2 o superior.
- Composer.
- MariaDB 11 ejecutándose en un contenedor Docker liviano.
- PDO con consultas preparadas.

## Estructura

```text
.
├── composer.json
├── database/schema.sql
├── docker-compose.yml
├── scripts/verificar_repository.php
└── src/
    ├── Config/Database.php
    └── Repository/EventoRepository.php
```

## Cómo ejecutar

1. Copiar `.env.example` como `.env` si se desean personalizar las variables. Los valores predeterminados funcionan sin crear el archivo.
2. Iniciar MariaDB:

   ```bash
   docker compose up -d
   ```

   El primer inicio crea `eventos_db`, las tablas, los tipos de evento y cinco eventos semilla.

3. Instalar dependencias y generar el autoload:

   ```bash
   composer install
   composer dump-autoload
   ```

4. Ejecutar la verificación del Repository:

   ```bash
   php scripts/verificar_repository.php
   ```

5. Detener el contenedor cuando termine el trabajo:

   ```bash
   docker compose down
   ```

Para eliminar también los datos persistidos y volver a ejecutar el script de inicialización SQL:

```bash
docker compose down -v
```

## Configuración de conexión

| Variable      | Valor predeterminado |
| ------------- | -------------------- |
| `DB_HOST`     | `127.0.0.1`          |
| `DB_PORT`     | `3307`               |
| `DB_NAME`     | `eventos_db`         |
| `DB_USER`     | `eventos_user`       |
| `DB_PASSWORD` | `eventos_password`   |

El puerto `3307` evita asumir que el puerto local `3306` está libre. PHP se ejecuta en el equipo anfitrión y se conecta mediante el puerto publicado por Docker.

## Funcionalidades implementadas

- [x] CREATE mediante `EventoRepository::crear()`.
- [x] READ mediante `obtenerTodos()` y `obtenerPorId()`.
- [x] UPDATE mediante `actualizar()`.
- [x] DELETE mediante `eliminar()`.
- [x] Relación `eventos.tipo_id` con `tipos_evento.id`.
- [x] Consultas preparadas para datos recibidos externamente.
- [x] Validación de título, fecha, hora, lugar, tipo, cupo e inscritos.
- [x] Cálculo de cupos disponibles.
- [x] Bloqueo de eliminación para eventos cuya fecha ya pasó.
- [x] Script PHP de verificación sin formularios.

## Regla de negocio

Un evento cuya fecha ya pasó no puede eliminarse para conservar el historial. El Repository lanza una excepción con un mensaje claro. Los formularios futuros deberán mostrar una pantalla de confirmación y enviar la eliminación exclusivamente por POST.

## Anotaciones de conceptos

El código incluye anotaciones `[CONCEPTO]` sobre encapsulación, abstracción, inyección de dependencias, seguridad, consultas preparadas y regla de negocio.

## Entrega posterior

Antes de comprimir el proyecto se debe exportar el estado final de la base de datos como `database/respaldo_bd.sql`, excluir `vendor/` y completar los datos reales de los integrantes.

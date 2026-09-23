# Caso 4: Agenda de Eventos Académicos

## Integrantes

- Diego Andrés Hernández Contreras 2022HC650
- Rodolfo Rivas Rodriguez 2022RR651

## Caso asignado

Agenda de Eventos Académicos

## Descripción

Aplicación web para registrar eventos académicos con cupos máximos e inscritos, validación del lado del servidor, carga dinámica de tipos, listado ordenado por fecha y bloqueo de eliminaciones para eventos ya finalizados.

## Tecnologías

- PHP 8.1+
- Composer
- MariaDB 11 en Docker
- PDO con consultas preparadas

## Estructura del proyecto

```text
.
├── composer.json
├── README.md
├── database/
│   ├── schema.sql
│   └── respaldo_bd.sql
├── docker-compose.yml
├── public/
│   ├── crear.php
│   ├── editar.php
│   ├── eliminar.php
│   ├── index.php
│   ├── procesar.php
│   └── css/
│       └── estilos.css
├── scripts/
│   └── verificar_repository.php
├── src/
│   ├── Config/
│   │   └── Database.php
│   └── Repository/
│       └── EventoRepository.php
└── vendor/   (generado con Composer, no se entrega)
```

## Requisitos del entorno

1. Tener Docker Desktop o Docker Engine instalado.
2. Tener PHP 8.2+ y Composer instalados en la máquina local.
3. Tener acceso al puerto 3308 para MariaDB.

## Cómo ejecutar

1. Iniciar la base de datos:

   ```bash
   docker compose up -d
   ```

2. Instalar dependencias de Composer:

   ```bash
   composer install
   ```

3. Ejecutar la validación del repositorio:

   ```bash
   php scripts/verificar_repository.php
   ```

4. Iniciar la aplicación web desde la carpeta public:

   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

5. Abrir en el navegador:

   ```text
   http://127.0.0.1:8000/index.php
   ```

6. Para detener la base de datos:

   ```bash
   docker compose down
   ```

## Variables de entorno

El proyecto usa estas variables por defecto:

```env
DB_HOST=127.0.0.1
DB_PORT=3308
DB_NAME=eventos_db
DB_USER=eventos_user
DB_PASSWORD=eventos_password
```

## Validaciones implementadas

- Título entre 5 y 150 caracteres.
- Fecha del evento igual o posterior a la actual en creación.
- Cupo entre 1 y 300.
- Inscritos mayor o igual a 0 y no mayor que el cupo.
- Tipo de evento válido.
- Lugar obligatorio y máximo 100 caracteres.
- Eliminación bloqueada si la fecha del evento ya pasó.

## Regla de negocio

Los eventos cuya fecha ya pasó aparecen en el listado con la etiqueta "Finalizado" y no pueden eliminarse. La eliminación solo se ejecuta mediante un formulario de confirmación que usa POST.

<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class EventoRepository
{
    // [CONCEPTO] Inyección de dependencias: el Repository recibe PDO desde fuera.
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return int ID del evento creado.
     */
    public function crear(
        string $titulo,
        ?string $descripcion,
        int $tipoId,
        string $fechaEvento,
        string $horaInicio,
        string $lugar,
        int $cupo,
        int $inscritos = 0
    ): int {
        $this->validarDatos($titulo, $tipoId, $fechaEvento, $horaInicio, $lugar, $cupo, $inscritos, true);

        // [CONCEPTO] Seguridad: todos los valores externos viajan como parámetros preparados.
        $statement = $this->pdo->prepare(
            'INSERT INTO eventos
                (titulo, descripcion, tipo_id, fecha_evento, hora_inicio, lugar, cupo, inscritos)
             VALUES (:titulo, :descripcion, :tipo_id, :fecha_evento, :hora_inicio, :lugar, :cupo, :inscritos)'
        );
        $statement->execute([
            'titulo' => trim($titulo),
            'descripcion' => $descripcion !== null ? trim($descripcion) : null,
            'tipo_id' => $tipoId,
            'fecha_evento' => $fechaEvento,
            'hora_inicio' => $horaInicio,
            'lugar' => trim($lugar),
            'cupo' => $cupo,
            'inscritos' => $inscritos,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function obtenerTodos(): array
    {
        $statement = $this->pdo->query(
            'SELECT e.id, e.titulo, e.descripcion, e.tipo_id, t.nombre AS tipo_nombre,
                    e.fecha_evento, e.hora_inicio, e.lugar, e.cupo, e.inscritos,
                    (e.cupo - e.inscritos) AS cupos_disponibles
             FROM eventos e
             INNER JOIN tipos_evento t ON t.id = e.tipo_id
             ORDER BY e.fecha_evento ASC, e.hora_inicio ASC, e.id ASC'
        );

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function obtenerPorId(int $id): ?array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El ID del evento debe ser positivo.');
        }

        $statement = $this->pdo->prepare(
            'SELECT e.id, e.titulo, e.descripcion, e.tipo_id, t.nombre AS tipo_nombre,
                    e.fecha_evento, e.hora_inicio, e.lugar, e.cupo, e.inscritos,
                    (e.cupo - e.inscritos) AS cupos_disponibles
             FROM eventos e
             INNER JOIN tipos_evento t ON t.id = e.tipo_id
             WHERE e.id = :id'
        );
        $statement->execute(['id' => $id]);
        $evento = $statement->fetch();

        return $evento === false ? null : $evento;
    }

    public function actualizar(
        int $id,
        string $titulo,
        ?string $descripcion,
        int $tipoId,
        string $fechaEvento,
        string $horaInicio,
        string $lugar,
        int $cupo,
        int $inscritos = 0
    ): void {
        if ($this->obtenerPorId($id) === null) {
            throw new RuntimeException('El evento que intenta actualizar no existe.');
        }

        $this->validarDatos($titulo, $tipoId, $fechaEvento, $horaInicio, $lugar, $cupo, $inscritos, false);

        // [CONCEPTO] Abstracción: la modificación se realiza mediante una operación del Repository.
        $statement = $this->pdo->prepare(
            'UPDATE eventos
             SET titulo = :titulo,
                 descripcion = :descripcion,
                 tipo_id = :tipo_id,
                 fecha_evento = :fecha_evento,
                 hora_inicio = :hora_inicio,
                 lugar = :lugar,
                 cupo = :cupo,
                 inscritos = :inscritos
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'titulo' => trim($titulo),
            'descripcion' => $descripcion !== null ? trim($descripcion) : null,
            'tipo_id' => $tipoId,
            'fecha_evento' => $fechaEvento,
            'hora_inicio' => $horaInicio,
            'lugar' => trim($lugar),
            'cupo' => $cupo,
            'inscritos' => $inscritos,
        ]);
    }

    public function eliminar(int $id): void
    {
        $evento = $this->obtenerPorId($id);
        if ($evento === null) {
            throw new RuntimeException('El evento que intenta eliminar no existe.');
        }

        // [CONCEPTO] Regla de negocio: un evento pasado conserva su registro histórico.
        if ($evento['fecha_evento'] < (new DateTimeImmutable('today'))->format('Y-m-d')) {
            throw new RuntimeException('No se pueden eliminar eventos cuya fecha ya pasó.');
        }

        $statement = $this->pdo->prepare('DELETE FROM eventos WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function validarDatos(
        string $titulo,
        int $tipoId,
        string $fechaEvento,
        string $horaInicio,
        string $lugar,
        int $cupo,
        int $inscritos,
        bool $validarFechaActual
    ): void {
        $titulo = trim($titulo);
        $lugar = trim($lugar);
        if (mb_strlen($titulo) < 5 || mb_strlen($titulo) > 150) {
            throw new InvalidArgumentException('El título debe tener entre 5 y 150 caracteres.');
        }
        if ($lugar === '' || mb_strlen($lugar) > 100) {
            throw new InvalidArgumentException('El lugar es obligatorio y no puede superar 100 caracteres.');
        }
        if (!$this->fechaEsValida($fechaEvento)) {
            throw new InvalidArgumentException('La fecha del evento no tiene un formato válido.');
        }
        if ($validarFechaActual && $fechaEvento < (new DateTimeImmutable('today'))->format('Y-m-d')) {
            throw new InvalidArgumentException('La fecha del evento debe ser igual o posterior a la fecha actual.');
        }
        if (!$this->horaEsValida($horaInicio)) {
            throw new InvalidArgumentException('La hora de inicio no tiene un formato válido.');
        }
        if ($cupo < 1 || $cupo > 300) {
            throw new InvalidArgumentException('El cupo debe estar entre 1 y 300.');
        }
        if ($inscritos < 0 || $inscritos > $cupo) {
            throw new InvalidArgumentException('Los inscritos deben ser mayores o iguales a 0 y no superar el cupo.');
        }
        if (!$this->tipoExiste($tipoId)) {
            throw new InvalidArgumentException('El tipo de evento seleccionado no existe.');
        }
    }

    private function tipoExiste(int $tipoId): bool
    {
        if ($tipoId <= 0) {
            return false;
        }

        $statement = $this->pdo->prepare('SELECT 1 FROM tipos_evento WHERE id = :id');
        $statement->execute(['id' => $tipoId]);

        return $statement->fetchColumn() !== false;
    }

    private function fechaEsValida(string $fecha): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        return $date !== false && $date->format('Y-m-d') === $fecha;
    }

    private function horaEsValida(string $hora): bool
    {
        $time = DateTimeImmutable::createFromFormat('!H:i:s', $hora);

        return $time !== false && $time->format('H:i:s') === $hora;
    }
}
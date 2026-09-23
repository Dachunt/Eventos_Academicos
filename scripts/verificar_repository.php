<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\Database;
use App\Repository\EventoRepository;

$pdo = Database::conectar();
$repository = new EventoRepository($pdo);
$createdId = null;

try {
    $events = $repository->obtenerTodos();
    if (count($events) < 5 || !array_key_exists('cupos_disponibles', $events[0])) {
        throw new RuntimeException('La lectura o el cálculo de cupos no produjo el resultado esperado.');
    }
    echo "OK: lectura y cupos disponibles\n";

    $createdId = $repository->crear(
        'Evento temporal de verificación',
        'Registro creado por el script de comprobación.',
        2,
        '2026-12-20',
        '11:30:00',
        'Laboratorio temporal',
        40,
        5
    );
    echo "OK: creación ({$createdId})\n";

    $repository->actualizar(
        $createdId,
        'Evento temporal actualizado',
        'Registro actualizado por el script de comprobación.',
        2,
        '2026-12-20',
        '12:00:00',
        'Laboratorio temporal',
        50,
        8
    );
    $updated = $repository->obtenerPorId($createdId);
    if ($updated === null || $updated['titulo'] !== 'Evento temporal actualizado') {
        throw new RuntimeException('La actualización no produjo el resultado esperado.');
    }
    echo "OK: actualización\n";

    $pastEvent = null;
    foreach ($events as $event) {
        if ($event['fecha_evento'] < date('Y-m-d')) {
            $pastEvent = $event;
            break;
        }
    }
    if ($pastEvent === null) {
        throw new RuntimeException('No se encontró un evento pasado para probar la regla de eliminación.');
    }

    try {
        $repository->eliminar((int) $pastEvent['id']);
        throw new RuntimeException('La eliminación de un evento pasado fue permitida.');
    } catch (RuntimeException $exception) {
        if ($exception->getMessage() !== 'No se pueden eliminar eventos cuya fecha ya pasó.') {
            throw $exception;
        }
    }
    echo "OK: eliminación de evento pasado bloqueada\n";

    $repository->eliminar($createdId);
    $createdId = null;
    echo "OK: eliminación de evento futuro permitida\n";
    echo "Verificación completada correctamente.\n";
} finally {
    if ($createdId !== null) {
        $statement = $pdo->prepare('DELETE FROM eventos WHERE id = :id');
        $statement->execute(['id' => $createdId]);
    }
}
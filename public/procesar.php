<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Repository\EventoRepository;
use InvalidArgumentException;
use Throwable;

function redirect(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php?error=' . rawurlencode('La operación solo está permitida mediante POST.'));
}

$action = $_POST['action'] ?? '';
$pdo = Database::conectar();
$repository = new EventoRepository($pdo);

try {
    switch ($action) {
        case 'crear':
            $titulo = (string) ($_POST['titulo'] ?? '');
            $descripcion = isset($_POST['descripcion']) && $_POST['descripcion'] !== '' ? (string) $_POST['descripcion'] : null;
            $tipoId = isset($_POST['tipo_id']) ? (int) $_POST['tipo_id'] : 0;
            $fechaEvento = (string) ($_POST['fecha_evento'] ?? '');
            $horaInicio = (string) ($_POST['hora_inicio'] ?? '');
            $lugar = (string) ($_POST['lugar'] ?? '');
            $cupo = isset($_POST['cupo']) ? (int) $_POST['cupo'] : 0;
            $inscritos = isset($_POST['inscritos']) ? (int) $_POST['inscritos'] : 0;

            $repository->crear($titulo, $descripcion, $tipoId, $fechaEvento, $horaInicio, $lugar, $cupo, $inscritos);
            redirect('index.php?success=' . rawurlencode('Evento registrado correctamente.'));

        case 'editar':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $titulo = (string) ($_POST['titulo'] ?? '');
            $descripcion = isset($_POST['descripcion']) && $_POST['descripcion'] !== '' ? (string) $_POST['descripcion'] : null;
            $tipoId = isset($_POST['tipo_id']) ? (int) $_POST['tipo_id'] : 0;
            $fechaEvento = (string) ($_POST['fecha_evento'] ?? '');
            $horaInicio = (string) ($_POST['hora_inicio'] ?? '');
            $lugar = (string) ($_POST['lugar'] ?? '');
            $cupo = isset($_POST['cupo']) ? (int) $_POST['cupo'] : 0;
            $inscritos = isset($_POST['inscritos']) ? (int) $_POST['inscritos'] : 0;

            $repository->actualizar($id, $titulo, $descripcion, $tipoId, $fechaEvento, $horaInicio, $lugar, $cupo, $inscritos);
            redirect('index.php?success=' . rawurlencode('Evento actualizado correctamente.'));

        case 'eliminar':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $repository->eliminar($id);
            redirect('index.php?success=' . rawurlencode('Evento eliminado correctamente.'));

        default:
            redirect('index.php?error=' . rawurlencode('La acción solicitada no es válida.'));
    }
} catch (Throwable $exception) {
    $message = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'No se pudo completar la operación.';

    if ($action === 'crear') {
        redirect('crear.php?error=' . rawurlencode($message));
    }

    if ($action === 'editar') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        redirect('editar.php?id=' . $id . '&error=' . rawurlencode($message));
    }

    if ($action === 'eliminar') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        redirect('eliminar.php?id=' . $id . '&error=' . rawurlencode($message));
    }

    redirect('index.php?error=' . rawurlencode($message));
}

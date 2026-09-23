<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Repository\EventoRepository;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?error=' . rawurlencode('El identificador del evento no es válido.'));
    exit;
}

$pdo = Database::conectar();
$repository = new EventoRepository($pdo);
$evento = $repository->obtenerPorId($id);

if ($evento === null) {
    header('Location: index.php?error=' . rawurlencode('El evento solicitado no existe.'));
    exit;
}

$esFinalizado = $evento['fecha_evento'] < (new DateTimeImmutable('today'))->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar eliminación</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container narrow">
        <header class="page-header compact">
            <div>
                <h1>Confirmar eliminación</h1>
            </div>
            <a class="btn secondary" href="index.php">Volver</a>
        </header>

        <?php if ($esFinalizado): ?>
            <div class="alert error">Este evento ya finalizó y no puede eliminarse.</div>
            <div class="actions-row">
                <a class="btn secondary" href="index.php">Volver al listado</a>
            </div>
        <?php else: ?>
            <div class="form-card warning-box">
                <p>¿Estás seguro de que deseas eliminar el siguiente evento?</p>
                <ul class="event-summary">
                    <li><strong>Título:</strong> <?= htmlspecialchars((string) $evento['titulo'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Tipo:</strong> <?= htmlspecialchars((string) $evento['tipo_nombre'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Fecha:</strong> <?= htmlspecialchars((string) $evento['fecha_evento'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Hora:</strong> <?= htmlspecialchars((string) $evento['hora_inicio'], ENT_QUOTES, 'UTF-8') ?></li>
                </ul>

                <form action="procesar.php" method="post">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" value="<?= (int) $evento['id'] ?>">
                    <div class="actions-row">
                        <button class="btn danger" type="submit">Confirmar eliminación</button>
                        <a class="btn secondary" href="index.php">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

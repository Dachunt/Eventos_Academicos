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

$tipos = $repository->obtenerTiposEvento();
$errorMessage = isset($_GET['error']) ? htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar evento</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container narrow">
        <header class="page-header compact">
            <div>
                <h1>Editar evento</h1>
            </div>
            <a class="btn secondary" href="index.php">Volver</a>
        </header>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert error"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form action="procesar.php" method="post" class="form-card">
            <input type="hidden" name="action" value="editar">
            <input type="hidden" name="id" value="<?= (int) $evento['id'] ?>">

            <div class="field-group">
                <label for="titulo">Título</label>
                <input id="titulo" name="titulo" type="text" maxlength="150" value="<?= htmlspecialchars((string) $evento['titulo'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="field-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars((string) ($evento['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="field-group">
                <label for="tipo_id">Tipo de evento</label>
                <select id="tipo_id" name="tipo_id" required>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= (int) $tipo['id'] ?>" <?= ((int) $tipo['id'] === (int) $evento['tipo_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $tipo['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid-two">
                <div class="field-group">
                    <label for="fecha_evento">Fecha del evento</label>
                    <input id="fecha_evento" name="fecha_evento" type="date" value="<?= htmlspecialchars((string) $evento['fecha_evento'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="field-group">
                    <label for="hora_inicio">Hora de inicio</label>
                    <input id="hora_inicio" name="hora_inicio" type="time" step="1" value="<?= htmlspecialchars((string) $evento['hora_inicio'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>

            <div class="grid-two">
                <div class="field-group">
                    <label for="lugar">Lugar</label>
                    <input id="lugar" name="lugar" type="text" maxlength="100" value="<?= htmlspecialchars((string) $evento['lugar'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="field-group">
                    <label for="cupo">Cupo</label>
                    <input id="cupo" name="cupo" type="number" min="1" max="300" value="<?= (int) $evento['cupo'] ?>" required>
                </div>
            </div>

            <div class="field-group">
                <label for="inscritos">Inscritos</label>
                <input id="inscritos" name="inscritos" type="number" min="0" value="<?= (int) $evento['inscritos'] ?>" required>
            </div>

            <div class="actions-row">
                <button class="btn primary" type="submit">Actualizar evento</button>
                <a class="btn secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

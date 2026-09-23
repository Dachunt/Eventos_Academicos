<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Repository\EventoRepository;

$pdo = Database::conectar();
$repository = new EventoRepository($pdo);
$tipos = $repository->obtenerTiposEvento();
$errorMessage = isset($_GET['error']) ? htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear evento</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container narrow">
        <header class="page-header compact">
            <div>
                <h1>Crear evento</h1>
            </div>
            <a class="btn secondary" href="index.php">Volver</a>
        </header>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert error"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form action="procesar.php" method="post" class="form-card">
            <input type="hidden" name="action" value="crear">

            <div class="field-group">
                <label for="titulo">Título</label>
                <input id="titulo" name="titulo" type="text" maxlength="150" required>
            </div>

            <div class="field-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>

            <div class="field-group">
                <label for="tipo_id">Tipo de evento</label>
                <select id="tipo_id" name="tipo_id" required>
                    <option value="">Selecciona un tipo</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= (int) $tipo['id'] ?>"><?= htmlspecialchars((string) $tipo['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid-two">
                <div class="field-group">
                    <label for="fecha_evento">Fecha del evento</label>
                    <input id="fecha_evento" name="fecha_evento" type="date" required>
                </div>

                <div class="field-group">
                    <label for="hora_inicio">Hora de inicio</label>
                    <input id="hora_inicio" name="hora_inicio" type="time" step="1" required>
                </div>
            </div>

            <div class="grid-two">
                <div class="field-group">
                    <label for="lugar">Lugar</label>
                    <input id="lugar" name="lugar" type="text" maxlength="100" required>
                </div>

                <div class="field-group">
                    <label for="cupo">Cupo</label>
                    <input id="cupo" name="cupo" type="number" min="1" max="300" required>
                </div>
            </div>

            <div class="field-group">
                <label for="inscritos">Inscritos</label>
                <input id="inscritos" name="inscritos" type="number" min="0" value="0" required>
            </div>

            <div class="actions-row">
                <button class="btn primary" type="submit">Guardar evento</button>
                <a class="btn secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

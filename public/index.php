<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Repository\EventoRepository;

$pdo = Database::conectar();
$repository = new EventoRepository($pdo);
$eventos = $repository->obtenerTodos();

$successMessage = isset($_GET['success']) ? htmlspecialchars((string) $_GET['success'], ENT_QUOTES, 'UTF-8') : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8') : '';
$today = (new DateTimeImmutable('today'))->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Eventos Académicos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <header class="page-header">
            <div>
                <h1>Agenda de Eventos Académicos</h1>
                <p>Listado actualizado de conferencias, seminarios, talleres y hackatones.</p>
            </div>
            <a class="btn primary" href="crear.php">+ Nuevo evento</a>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="alert success"><?= $successMessage ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert error"><?= $errorMessage ?></div>
        <?php endif; ?>

        <main>
            <?php if ($eventos === []): ?>
                <div class="empty-state">
                    <h2>No hay eventos registrados</h2>
                    <p>Agrega el primer evento académico para comenzar.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Lugar</th>
                            <th>Cupo</th>
                            <th>Inscritos</th>
                            <th>Cupos disponibles</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eventos as $evento): ?>
                            <?php
                            $eventoId = (int) $evento['id'];
                            $titulo = htmlspecialchars((string) $evento['titulo'], ENT_QUOTES, 'UTF-8');
                            $tipo = htmlspecialchars((string) $evento['tipo_nombre'], ENT_QUOTES, 'UTF-8');
                            $fecha = htmlspecialchars((string) $evento['fecha_evento'], ENT_QUOTES, 'UTF-8');
                            $hora = htmlspecialchars((string) $evento['hora_inicio'], ENT_QUOTES, 'UTF-8');
                            $lugar = htmlspecialchars((string) $evento['lugar'], ENT_QUOTES, 'UTF-8');
                            $cupo = (int) $evento['cupo'];
                            $inscritos = (int) $evento['inscritos'];
                            $disponibles = (int) $evento['cupos_disponibles'];
                            $esFinalizado = $fecha < $today;
                            ?>
                            <tr class="<?= $esFinalizado ? 'finalizado' : '' ?>">
                                <td><?= $eventoId ?></td>
                                <td><?= $titulo ?></td>
                                <td><?= $tipo ?></td>
                                <td><?= $fecha ?></td>
                                <td><?= $hora ?></td>
                                <td><?= $lugar ?></td>
                                <td><?= $cupo ?></td>
                                <td><?= $inscritos ?></td>
                                <td><?= $disponibles ?></td>
                                <td>
                                    <?php if ($esFinalizado): ?>
                                        <span class="badge finished">Finalizado</span>
                                    <?php else: ?>
                                        <span class="badge active">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a class="btn secondary" href="editar.php?id=<?= $eventoId ?>">Editar</a>
                                    <?php if (!$esFinalizado): ?>
                                        <a class="btn danger" href="eliminar.php?id=<?= $eventoId ?>">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

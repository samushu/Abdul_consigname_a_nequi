<?php
// View/shared/header.php
// Cabecera HTML compartida — carga el CSS externo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RumBoss · <?= $pageTitle ?? 'Gestor de Alquiler' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="View/css/estilos.css">
</head>
<body>

<nav class="navbar">
    <a href="index.html" class="navbar-brand"><span>Rum</span>Boss</a>
    <div class="nav-links">
        <a href="index.php?modulo=vehiculos"
           class="<?= (($modulo ?? '') === 'vehiculos') ? 'activo' : '' ?>">Vehículos</a>
        <a href="index.php?modulo=clientes"
           class="<?= (($modulo ?? '') === 'clientes')  ? 'activo' : '' ?>">Clientes</a>
        <a href="index.php?modulo=reservas"
           class="<?= (($modulo ?? '') === 'reservas')  ? 'activo' : '' ?>">Reservas</a>
    </div>
</nav>

<main class="contenedor">
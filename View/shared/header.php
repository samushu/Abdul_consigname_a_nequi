<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RumBoss · <?= $pageTitle ?? 'Gestor de Alquiler' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & tokens ─────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --rojo:      #C0392B;
            --rojo-claro:#E74C3C;
            --negro:     #0D0D0D;
            --gris-osc:  #1A1A1A;
            --gris-med:  #2C2C2C;
            --gris-bord: #3A3A3A;
            --gris-lite: #888;
            --crema:     #F0EDE6;
            --blanco:    #FAFAF8;
            --verde:     #27AE60;
            --amarillo:  #F39C12;
            --font-h:    'Barlow Condensed', sans-serif;
            --font-b:    'Barlow', sans-serif;
            --radio:     6px;
            --sombra:    0 4px 20px rgba(0,0,0,.45);
        }

        body {
            background: var(--negro);
            color: var(--crema);
            font-family: var(--font-b);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── Navbar ─────────────────────────────────────── */
        .navbar {
            background: var(--gris-osc);
            border-bottom: 3px solid var(--rojo);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 64px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: var(--sombra);
        }
        .navbar-brand {
            font-family: var(--font-h);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            text-decoration: none;
            color: var(--crema);
        }
        .navbar-brand span { color: var(--rojo); }

        .nav-links { display: flex; gap: .25rem; }
        .nav-links a {
            font-family: var(--font-h);
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            text-decoration: none;
            color: var(--gris-lite);
            padding: .4rem 1rem;
            border-radius: var(--radio);
            transition: background .18s, color .18s;
        }
        .nav-links a:hover,
        .nav-links a.activo {
            background: var(--rojo);
            color: var(--blanco);
        }

        /* ── Contenedor principal ───────────────────────── */
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* ── Page header ────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        .page-title {
            font-family: var(--font-h);
            font-size: 2.4rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--blanco);
        }
        .page-title span { color: var(--rojo); }

        /* ── Botones ────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            font-family: var(--font-h);
            font-weight: 700;
            font-size: .95rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            text-decoration: none;
            padding: .5rem 1.25rem;
            border-radius: var(--radio);
            border: none; cursor: pointer;
            transition: filter .15s, transform .1s;
        }
        .btn:hover  { filter: brightness(1.15); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-rojo   { background: var(--rojo);  color: #fff; }
        .btn-gris   { background: var(--gris-med); color: var(--crema); }
        .btn-verde  { background: var(--verde);  color: #fff; }
        .btn-amber  { background: var(--amarillo); color: #fff; }
        .btn-sm     { font-size: .78rem; padding: .3rem .75rem; }

        /* ── Tabla ──────────────────────────────────────── */
        .tabla-wrap {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: var(--sombra);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--gris-osc);
        }
        thead th {
            background: var(--gris-med);
            font-family: var(--font-h);
            font-weight: 700;
            font-size: .9rem;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--gris-lite);
            padding: .85rem 1rem;
            text-align: left;
            border-bottom: 2px solid var(--gris-bord);
        }
        tbody tr { border-bottom: 1px solid var(--gris-bord); }
        tbody tr:hover { background: rgba(192,57,43,.08); }
        tbody td { padding: .75rem 1rem; vertical-align: middle; }
        .acciones { display: flex; gap: .4rem; flex-wrap: wrap; }

        /* ── Badge de estado ────────────────────────────── */
        .badge {
            display: inline-block;
            font-family: var(--font-h);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: .2rem .6rem;
            border-radius: 4px;
        }
        .badge-verde   { background: rgba(39,174,96,.18);  color: #2ecc71; border: 1px solid rgba(39,174,96,.3); }
        .badge-rojo    { background: rgba(192,57,43,.2);   color: #e74c3c; border: 1px solid rgba(192,57,43,.3); }
        .badge-amber   { background: rgba(243,156,18,.15); color: #f39c12; border: 1px solid rgba(243,156,18,.3); }
        .badge-gris    { background: rgba(136,136,136,.15);color: #aaa;    border: 1px solid rgba(136,136,136,.3);}

        /* ── Formularios ────────────────────────────────── */
        .form-card {
            background: var(--gris-osc);
            border-radius: 10px;
            padding: 2rem;
            max-width: 640px;
            box-shadow: var(--sombra);
            border: 1px solid var(--gris-bord);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
        .form-grid .full { grid-column: 1 / -1; }
        .campo { display: flex; flex-direction: column; gap: .35rem; }
        .campo label {
            font-family: var(--font-h);
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--gris-lite);
        }
        .campo input,
        .campo select,
        .campo textarea {
            background: var(--gris-med);
            border: 1px solid var(--gris-bord);
            border-radius: var(--radio);
            color: var(--crema);
            font-family: var(--font-b);
            font-size: .95rem;
            padding: .6rem .9rem;
            transition: border-color .18s;
            outline: none;
        }
        .campo input:focus,
        .campo select:focus { border-color: var(--rojo); }
        .campo .error-msg {
            font-size: .78rem; color: var(--rojo-claro); margin-top: .15rem;
        }
        .form-acciones { display: flex; gap: .75rem; margin-top: 1.5rem; }

        /* ── Alertas ────────────────────────────────────── */
        .alerta {
            padding: .85rem 1.2rem;
            border-radius: var(--radio);
            margin-bottom: 1.5rem;
            font-weight: 500;
            font-size: .92rem;
            display: flex; align-items: center; gap: .6rem;
        }
        .alerta-exito { background: rgba(39,174,96,.15); border-left: 4px solid var(--verde); color: #2ecc71; }
        .alerta-error { background: rgba(192,57,43,.15); border-left: 4px solid var(--rojo);  color: #e74c3c; }

        /* ── Tabla vacía ────────────────────────────────── */
        .tabla-vacia {
            text-align: center; padding: 3rem; color: var(--gris-lite);
            font-family: var(--font-h); font-size: 1.1rem; letter-spacing: .5px;
        }

        /* Icono categoría */
        .ico { font-size: 1.1rem; }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="navbar-brand"><span>Rum</span>Boss</a>
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

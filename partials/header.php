<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/flash.php';

$u = current_user();
$f = flash_get();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'CRUD SQLite', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="wrap topbar__inner">
    <a class="brand" href="<?= BASE_URL ?>/index.php">CRUD Articles (SQLite)</a>
    <nav class="nav">
      <a href="<?= BASE_URL ?>/index.php">Articles</a>
      <?php if ($u): ?>
        <span class="badge">
          <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>
          (<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>)
        </span>
        <a href="<?= BASE_URL ?>/auth/logout.php">Déconnexion</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/auth/login.php">Connexion</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="wrap">
<?php if ($f): ?>
  <div class="flash flash--<?= htmlspecialchars($f['type'], ENT_QUOTES, 'UTF-8') ?>">
    <?= htmlspecialchars($f['message'], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>
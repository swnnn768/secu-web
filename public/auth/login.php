<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';

ensure_session();

if (is_logged_in()) {
  header('Location: ' . BASE_URL . '/index.php');
  exit;
}

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_or_die();

  $email = trim((string)($_POST['email'] ?? ''));
  $pass  = (string)($_POST['password'] ?? '');

  if ($email === '' || $pass === '') {
    flash_set('error', 'Email et mot de passe requis.');
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
  }

  $stmt = $pdo->prepare("SELECT id, email, password_hash, role FROM users WHERE email = :e");
  $stmt->execute([':e' => $email]);
  $u = $stmt->fetch();

  if (!$u || !password_verify($pass, $u['password_hash'])) {
    flash_set('error', 'Identifiants invalides.');
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
  }

  $_SESSION['user'] = [
    'id' => (int)$u['id'],
    'email' => $u['email'],
    'role' => $u['role'],
  ];

  flash_set('success', 'Connecté.');
  header('Location: ' . BASE_URL . '/index.php');
  exit;
}

$title = "Connexion";
require __DIR__ . '/../../partials/header.php';
?>
<div class="card">
  <h1>Connexion</h1>

  <p><small>Comptes de démo (créés automatiquement) :</small></p>
  <ul>
    <li><small>Admin: admin@demo.local / Admin123!</small></li>
    <li><small>User: user@demo.local / User123!</small></li>
  </ul>

  <form method="post">
    <?= csrf_field() ?>

    <label>Email</label>
    <input name="email" type="email" required>

    <label>Mot de passe</label>
    <input name="password" type="password" required>

    <div class="actions">
      <button class="btn" type="submit">Se connecter</button>
      <a class="btn btn--muted" href="<?= BASE_URL ?>/index.php">Retour</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
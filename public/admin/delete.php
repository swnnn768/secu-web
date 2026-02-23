<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';

require_admin();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, title, slug FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$a = $stmt->fetch();

if (!$a) {
  http_response_code(404);
  exit('Introuvable.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_or_die();

  $del = $pdo->prepare("DELETE FROM articles WHERE id = :id");
  $del->execute([':id' => $a['id']]);

  flash_set('success', 'Article supprimé.');
  header('Location: ' . BASE_URL . '/index.php');
  exit;
}

$title = "Supprimer";
require __DIR__ . '/../../partials/header.php';
?>
<div class="card">
  <h1>Supprimer</h1>
  <p>Confirmer la suppression de :</p>

  <div class="card">
    <strong><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></strong><br>
    <small>Slug: <?= htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8') ?></small>
  </div>

  <form method="post">
    <?= csrf_field() ?>
    <div class="actions">
      <button class="btn btn--danger" type="submit">Confirmer</button>
      <a class="btn btn--muted" href="<?= BASE_URL ?>/index.php">Annuler</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
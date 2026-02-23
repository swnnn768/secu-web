<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = db();

$slug = trim((string)($_GET['s'] ?? ''));
if ($slug === '') {
  http_response_code(404);
  exit('Introuvable.');
}

$stmt = $pdo->prepare("SELECT id, title, slug, content, created_at, updated_at FROM articles WHERE slug = :s");
$stmt->execute([':s' => $slug]);
$a = $stmt->fetch();

if (!$a) {
  http_response_code(404);
  exit('Introuvable.');
}

$title = $a['title'];
require __DIR__ . '/../partials/header.php';
?>
<div class="card">
  <h1><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></h1>
  <small>
    Slug: <?= htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8') ?> •
    Créé: <?= htmlspecialchars($a['created_at'], ENT_QUOTES, 'UTF-8') ?>
    <?php if ($a['updated_at']): ?>
      • Modifié: <?= htmlspecialchars($a['updated_at'], ENT_QUOTES, 'UTF-8') ?>
    <?php endif; ?>
  </small>

  <div class="card" style="margin-top:14px">
    <?= nl2br(htmlspecialchars($a['content'], ENT_QUOTES, 'UTF-8')) ?>
  </div>

  <div class="actions">
    <a class="btn" href="<?= BASE_URL ?>/index.php">← Retour</a>
    <?php if (is_admin()): ?>
      <a class="btn" href="<?= BASE_URL ?>/admin/edit.php?id=<?= (int)$a['id'] ?>">Modifier</a>
      <a class="btn btn--danger" href="<?= BASE_URL ?>/admin/delete.php?id=<?= (int)$a['id'] ?>">Supprimer</a>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
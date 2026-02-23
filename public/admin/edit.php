<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';

require_admin();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, title, slug, content FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$a = $stmt->fetch();

if (!$a) {
  http_response_code(404);
  exit('Introuvable.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_or_die();

  $title = trim((string)($_POST['title'] ?? ''));
  $slug  = trim((string)($_POST['slug'] ?? ''));
  $content = trim((string)($_POST['content'] ?? ''));

  if ($title === '' || $slug === '' || $content === '') {
    flash_set('error', 'Tous les champs sont obligatoires.');
    header('Location: ' . BASE_URL . '/admin/edit.php?id=' . $a['id']);
    exit;
  }

  if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    flash_set('error', 'Slug invalide.');
    header('Location: ' . BASE_URL . '/admin/edit.php?id=' . $a['id']);
    exit;
  }

  $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = :s AND id != :id");
  $check->execute([':s' => $slug, ':id' => $a['id']]);
  if ((int)$check->fetchColumn() > 0) {
    flash_set('error', 'Ce slug est déjà utilisé.');
    header('Location: ' . BASE_URL . '/admin/edit.php?id=' . $a['id']);
    exit;
  }

  $upd = $pdo->prepare("
    UPDATE articles
    SET title=:t, slug=:s, content=:c, updated_at=datetime('now')
    WHERE id=:id
  ");
  $upd->execute([':t' => $title, ':s' => $slug, ':c' => $content, ':id' => $a['id']]);

  flash_set('success', 'Article modifié.');
  header('Location: ' . BASE_URL . '/article.php?s=' . urlencode($slug));
  exit;
}

$title = "Modifier";
require __DIR__ . '/../../partials/header.php';
?>
<div class="card">
  <h1>Modifier</h1>

  <form method="post">
    <?= csrf_field() ?>

    <label>Titre</label>
    <input name="title" value="<?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>" required>

    <label>Slug</label>
    <input name="slug" value="<?= htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8') ?>" required>

    <label>Contenu</label>
    <textarea name="content" required><?= htmlspecialchars($a['content'], ENT_QUOTES, 'UTF-8') ?></textarea>

    <div class="actions">
      <button class="btn" type="submit">Enregistrer</button>
      <a class="btn btn--muted" href="<?= BASE_URL ?>/article.php?s=<?= urlencode($a['slug']) ?>">Annuler</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
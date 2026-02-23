<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/csrf.php';
require_once __DIR__ . '/../../lib/flash.php';

require_admin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_or_die();

  $title = trim((string)($_POST['title'] ?? ''));
  $slug  = trim((string)($_POST['slug'] ?? ''));
  $content = trim((string)($_POST['content'] ?? ''));

  if ($title === '' || $slug === '' || $content === '') {
    flash_set('error', 'Tous les champs sont obligatoires.');
    header('Location: ' . BASE_URL . '/admin/create.php');
    exit;
  }

  if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    flash_set('error', 'Slug invalide (minuscule + tirets).');
    header('Location: ' . BASE_URL . '/admin/create.php');
    exit;
  }

  $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = :s");
  $check->execute([':s' => $slug]);
  if ((int)$check->fetchColumn() > 0) {
    flash_set('error', 'Ce slug existe déjà.');
    header('Location: ' . BASE_URL . '/admin/create.php');
    exit;
  }

  $ins = $pdo->prepare("INSERT INTO articles (title, slug, content) VALUES (:t,:s,:c)");
  $ins->execute([':t' => $title, ':s' => $slug, ':c' => $content]);

  flash_set('success', 'Article créé.');
  header('Location: ' . BASE_URL . '/index.php');
  exit;
}

$title = "Créer";
require __DIR__ . '/../../partials/header.php';
?>
<div class="card">
  <h1>Créer un article</h1>

  <form method="post">
    <?= csrf_field() ?>

    <label>Titre</label>
    <input name="title" required>

    <label>Slug (unique)</label>
    <input name="slug" placeholder="ex: mon-article" required>

    <label>Contenu</label>
    <textarea name="content" required></textarea>

    <div class="actions">
      <button class="btn" type="submit">Créer</button>
      <a class="btn btn--muted" href="<?= BASE_URL ?>/index.php">Annuler</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
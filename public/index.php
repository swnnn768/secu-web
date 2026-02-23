<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = db();

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$total = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();

$stmt = $pdo->prepare("SELECT id, title, slug, created_at FROM articles ORDER BY created_at DESC LIMIT :l OFFSET :o");
$stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':o', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

$pages = max(1, (int)ceil($total / $perPage));

$title = "Articles";
require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <section class="card">
    <h1>Articles</h1>
    <small><?= $total ?> article(s)</small>

    <ul class="list">
      <?php foreach ($articles as $a): ?>
        <li>
          <a class="btn btn--muted" href="<?= BASE_URL ?>/article.php?s=<?= urlencode($a['slug']) ?>">Lire</a>
          <strong><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></strong><br>
          <small>
            Slug: <?= htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8') ?>
            • <?= htmlspecialchars($a['created_at'], ENT_QUOTES, 'UTF-8') ?>
          </small>

          <?php if (is_admin()): ?>
            <div class="actions">
              <a class="btn" href="<?= BASE_URL ?>/admin/edit.php?id=<?= (int)$a['id'] ?>">Modifier</a>
              <a class="btn btn--danger" href="<?= BASE_URL ?>/admin/delete.php?id=<?= (int)$a['id'] ?>">Supprimer</a>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="actions">
      <?php if ($page > 1): ?>
        <a class="btn" href="<?= BASE_URL ?>/index.php?page=<?= $page - 1 ?>">← Précédent</a>
      <?php endif; ?>
      <?php if ($page < $pages): ?>
        <a class="btn" href="<?= BASE_URL ?>/index.php?page=<?= $page + 1 ?>">Suivant →</a>
      <?php endif; ?>
    </div>
  </section>

  <aside class="card">
    <h2>Administration</h2>
    <?php if (!is_logged_in()): ?>
      <p><small>Connecte-toi pour accéder à l’admin.</small></p>
      <a class="btn" href="<?= BASE_URL ?>/auth/login.php">Connexion</a>
    <?php elseif (!is_admin()): ?>
      <p><small>Tu peux lire uniquement (pas admin).</small></p>
    <?php else: ?>
      <p><small>Tu es admin : Create/Update/Delete autorisés.</small></p>
      <a class="btn" href="<?= BASE_URL ?>/admin/create.php">Créer un article</a>
    <?php endif; ?>
  </aside>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
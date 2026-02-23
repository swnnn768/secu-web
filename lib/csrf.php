<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function csrf_token(): string {
  ensure_session();
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function csrf_field(): string {
  $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
  return '<input type="hidden" name="csrf_token" value="'.$t.'">';
}

function csrf_verify_or_die(): void {
  ensure_session();
  $sent = (string)($_POST['csrf_token'] ?? '');
  $ok = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
  if (!$ok) {
    http_response_code(400);
    exit('CSRF token invalide.');
  }
}
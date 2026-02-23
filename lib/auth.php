<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function ensure_session(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
}

function current_user(): ?array {
  ensure_session();
  return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
  return current_user() !== null;
}

function is_admin(): bool {
  $u = current_user();
  return $u && ($u['role'] ?? '') === 'admin';
}

function require_login(): void {
  if (!is_logged_in()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
  }
}

function require_admin(): void {
  require_login();
  if (!is_admin()) {
    http_response_code(403);
    exit('Accès refusé (admin uniquement).');
  }
}
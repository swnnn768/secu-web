<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function flash_set(string $type, string $message): void {
  ensure_session();
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
  ensure_session();
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}
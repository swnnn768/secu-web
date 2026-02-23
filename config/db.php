<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $dir = dirname(SQLITE_PATH);
  if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
  }

  $pdo = new PDO('sqlite:' . SQLITE_PATH, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $pdo->exec("PRAGMA foreign_keys = ON;");

  // Tables
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      email TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL,
      role TEXT NOT NULL CHECK(role IN ('admin','user')),
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS articles (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      slug TEXT NOT NULL UNIQUE,
      content TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NULL
    );
  ");

  // Seed (admin + user) si vide
  $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
  if ($count === 0) {
    $adminPass = password_hash('Admin123!', PASSWORD_DEFAULT);
    $userPass  = password_hash('User123!', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (:e,:p,:r)");
    $stmt->execute([':e' => 'admin@demo.local', ':p' => $adminPass, ':r' => 'admin']);
    $stmt->execute([':e' => 'user@demo.local',  ':p' => $userPass,  ':r' => 'user']);
  }

  return $pdo;
}
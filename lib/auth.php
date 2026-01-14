<?php
require_once __DIR__ . '/../config/db.php';

function login($email, $password) {
  global $pdo;
  $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status = "ACTIVE" LIMIT 1');
  $stmt->execute([$email]);
  $user = $stmt->fetch();
if ($user && $password === $user['password_hash']) {    $_SESSION['user'] = [
      'id' => $user['id'],
      'name' => $user['name'],
      'email' => $user['email'],
      'role' => $user['role'],
    ];
    return true;
  }
  return false;
}

function require_login() {
  if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
  }
}

function require_role($roles = []) {
  require_login();
  if (!in_array($_SESSION['user']['role'], $roles)) {
    http_response_code(403);
    exit('Unauthorized');
  }
}

function logout() {
  session_destroy();
}
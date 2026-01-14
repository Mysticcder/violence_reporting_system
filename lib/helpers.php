<?php
function generate_tracking_code($len = 12) {
  $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  $code = '';
  for ($i = 0; $i < $len; $i++) {
    $code .= $chars[random_int(0, strlen($chars)-1)];
  }
  return $code;
}

function sanitize_filename($name) {
  return preg_replace('/[^A-Za-z0-9_\.-]/', '_', $name);
}

function flash($key, $message = null) {
  if ($message === null) {
    if (!empty($_SESSION['flash'][$key])) {
      $msg = $_SESSION['flash'][$key];
      unset($_SESSION['flash'][$key]);
      return $msg;
    }
    return null;
  } else {
    $_SESSION['flash'][$key] = $message;
  }
}
<?php
function validate_report($data) {
  $errors = [];
  if (empty($data['report_type']) || !in_array($data['report_type'], ['GBV','CHILD_ABUSE','SEXUAL_HARASSMENT','OTHER'])) {
    $errors[] = 'Invalid report type.';
  }
  if (empty($data['title']) || mb_strlen($data['title']) < 5) {
    $errors[] = 'Title must be at least 5 characters.';
  }
  if (empty($data['description']) || mb_strlen($data['description']) < 20) {
    $errors[] = 'Description must be at least 20 characters.';
  }
  if (!empty($data['reporter_email']) && !filter_var($data['reporter_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
  }
  return $errors;
}
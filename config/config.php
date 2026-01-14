<?php
return [
  'app_name' => 'Violence Reporting System',
  'base_url' => 'http://localhost/violence_reporting_system/public',
  'env' => 'local',
  'debug' => true,
  'upload_dir' => __DIR__ . '/../public/uploads/evidence',
  'max_upload_bytes' => 10 * 1024 * 1024, // 10 MB
  'allowed_extensions' => ['jpg','jpeg','png','pdf','docx'],
];
<?php
// header.php — include at top of pages (after session_start() when needed)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Smart Hospital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --brand:#0d6efd; /* modern blue */
      --muted:#6c757d;
    }
    .site-brand { font-weight:700; color:var(--brand); }
    .card-hero { border-radius:14px; box-shadow:0 6px 18px rgba(13,110,253,0.08); }
    .accent { color:var(--brand); }
    .small-muted { font-size:.9rem; color:var(--muted); }
    @media (max-width:576px){ .max-420{max-width:420px;margin:auto;} }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand site-brand" href="index.php">Smart Hospital</a>
    <div class="ms-auto">
      <?php if(session_status()!==PHP_SESSION_NONE && isset($_SESSION['username'])): ?>
        <span class="me-3 small-muted">Hello, <strong><?=htmlspecialchars($_SESSION['username'])?></strong></span>
        <a class="btn btn-outline-secondary btn-sm" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-primary btn-sm" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container my-4">

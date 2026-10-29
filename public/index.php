<?php

require __DIR__.'/bootstrap.php';
if (!isset($_SESSION['user'])) {
    echo '<a href="/login.php">Sign in</a>';
    exit;
} echo 'Signed in as '.htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8').' · <a href="/logout.php">Sign out</a>';

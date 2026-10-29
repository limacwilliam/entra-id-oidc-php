<?php require __DIR__.'/bootstrap.php'; header('Location: '.(new App\OidcClient(App\Config::fromEnv()))->begin(), true, 302);

# Legacy integration

Require `public/bootstrap.php` before accessing session state. Protect pages with `isset($_SESSION['user'])`; never trust a browser-supplied identity header.

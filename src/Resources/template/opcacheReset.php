<?php

if (!extension_loaded('opcache')) {
    $success = false;
    $message = 'Opcache extension not loaded';
} elseif (opcache_reset()) {
    $success = true;
    $message = 'Opcache reset successful';
} else {
    $success = false;
    $message = 'Opcache reset fail';
}

echo json_encode([
    'success' => $success,
    'message' => $message,
]);

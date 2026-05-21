<?php
/*
 * CPCS403 – Red Sea Escapes
 * File: api/search.php
 * Purpose: Live search API — called by JavaScript as the user types
 * Method:  GET ?q=keyword&category=water|desert|all
 * Returns: JSON
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$q        = trim($_GET['q']        ?? '');
$category = trim($_GET['category'] ?? 'all');

// Whitelist the category value
if (!in_array($category, ['water', 'desert', 'all'], true)) {
    $category = 'all';
}

$pdo = getDB();

if ($category === 'all') {
    $stmt = $pdo->prepare(
        "SELECT id, name, category, description, price, min_age, image
         FROM   activities
         WHERE  name LIKE :q OR description LIKE :q
         ORDER  BY name ASC LIMIT 10"
    );
    $stmt->execute([':q' => "%$q%"]);
} else {
    $stmt = $pdo->prepare(
        "SELECT id, name, category, description, price, min_age, image
         FROM   activities
         WHERE  (name LIKE :q OR description LIKE :q) AND category = :cat
         ORDER  BY name ASC LIMIT 10"
    );
    $stmt->execute([':q' => "%$q%", ':cat' => $category]);
}

$results = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'count'   => count($results),
    'results' => $results,
]);
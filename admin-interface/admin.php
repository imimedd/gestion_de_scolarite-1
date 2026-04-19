<?php
require_once("database.php");
$hash = password_hash("admin123", PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO administrateurs (email, password, role) VALUES (?, ?, ?)")
    ->execute(["admin@usthb.dz", $hash, "admin"]);
echo "Admin créé !";
?>
<?php
echo "--- gestion_scolarite ---\n";
try {
    $pdo1 = new PDO('mysql:host=localhost;dbname=gestion_scolarite', 'root', '');
    print_r($pdo1->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN));
    print_r($pdo1->query('SELECT * FROM administrateurs')->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo $e->getMessage(); }

echo "\n--- usthb_enseignant ---\n";
try {
    $pdo2 = new PDO('mysql:host=localhost;dbname=usthb_enseignant', 'root', '');
    print_r($pdo2->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN));
    print_r($pdo2->query('SELECT * FROM enseignants')->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo $e->getMessage(); }
?>

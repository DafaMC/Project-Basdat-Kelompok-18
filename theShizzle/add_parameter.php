<?php
require_once 'db.php';

$projects = $pdo->query("SELECT * FROM projects")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = new PDO('mysql:host=localhost:3307;dbname=basdat', 'root', '');

    $parameter_name = $_POST['name']; // Corrected to match the form field name
    $project_id = $_POST['project_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO parameter_penilaian (name, project_id) VALUES (?, ?)");
        $stmt->execute([$parameter_name, $project_id]); // Use $parameter_name instead of $name
        echo "Parameter Penilaian added successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="POST">
    <label>Parameter Name:</label>
    <input type="text" name="name" required><br> <!-- Ensure the name matches the PHP code -->

    <label>Project:
        <select name="project_id">
            <?php foreach ($projects as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['project_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <button type="submit">Add Parameter</button>
</form>

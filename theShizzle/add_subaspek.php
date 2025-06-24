<?php
require_once 'db.php';

$params = $pdo->query("SELECT * FROM parameter_penilaian")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $param_id = $_POST['parameter_id'];
    $name = $_POST['name'];

    $stmt = $pdo->prepare("INSERT INTO sub_aspek (parameter_id, name) VALUES (?, ?)");
    $stmt->execute([$param_id, $name]);

    echo "Sub-Aspek added!";
}
?>

<form method="post">
    <label>Sub-Aspek Name: <input type="text" name="name" required></label><br>
    <label>Parameter Penilaian:
        <select name="parameter_id">
            <?php foreach ($params as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Add Sub-Aspek</button>
</form>

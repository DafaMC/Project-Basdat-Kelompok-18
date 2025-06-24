<?php
require_once 'db.php';

$users = $pdo->query("SELECT * FROM users WHERE role = 'user'")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects")->fetchAll();
$subs = $pdo->query("SELECT sa.id, sa.name AS sub_name, pp.name AS param_name 
                     FROM sub_aspek sa 
                     JOIN parameter_penilaian pp ON sa.parameter_id = pp.id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = new PDO('mysql:host=localhost:3307;dbname=basdat', 'root', '');

    $project_id = $_POST['project_id'];
    $sub_aspek_id = $_POST['sub_aspek_id'];
    $user_id = $_POST['user_id'];
    $jumlah_kesalahan = $_POST['jumlah_kesalahan'];

    try {
        $stmt = $pdo->prepare("INSERT INTO mistakes (project_id, sub_aspek_id, user_id, jumlah_kesalahan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$project_id, $sub_aspek_id, $user_id, $jumlah_kesalahan]);
        echo "Score added successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="POST">
    <label>User:
        <select name="user_id" required>
            <option value="">Select User</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= htmlspecialchars($u['id']) ?>"><?= htmlspecialchars($u['username']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Project:
        <select name="project_id" required>
            <option value="">Select Project</option>
            <?php foreach ($projects as $p): ?>
                <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['project_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Sub-Aspek:
        <select name="sub_aspek_id" required>
            <option value="">Select Sub-Aspek</option>
            <?php foreach ($subs as $s): ?>
                <option value="<?= htmlspecialchars($s['id']) ?>"><?= htmlspecialchars($s['sub_name']) ?> - <?= htmlspecialchars($s['param_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Jumlah Kesalahan: </label>
    <input type="number" name="jumlah_kesalahan" min="0" max="10" required><br>

    <button type="submit">Add Score</button>
</form>

<?php
require_once 'db.php'; // Reuse db connection

$users = $pdo->query("SELECT * FROM users WHERE role = 'user'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_name'], $_POST['user_id'])) {
    $project_name = $_POST['project_name'];
    $user_id = $_POST['user_id'];

    $stmt = $pdo->prepare("INSERT INTO projects (project_name, user_id) VALUES (?, ?)");
    $stmt->execute([$project_name, $user_id]);

    echo "Project added!";
}
?>


<form method="post">
    <label>Project Name: <input type="text" name="project_name" required></label><br>
    <label>User:
        <select name="user_id">
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Add Project</button>
</form>

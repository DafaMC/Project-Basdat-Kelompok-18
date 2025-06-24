<?php
$host = 'localhost:3307';
$db = 'basdat';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if (!$user_id || !$project_id) {
    echo "Please provide both user_id and project_id in the URL.";
    exit;
}

$sql = "
    SELECT 
        pp.id AS parameter_id,
        pp.name,
        sa.id AS sub_id,
        sa.name,
        IFNULL(s.jumlah_kesalahan, 0) AS kesalahan
    FROM parameter_penilaian pp
    JOIN sub_aspek sa ON sa.parameter_id = pp.id
    JOIN projects p ON pp.project_id = p.id
    LEFT JOIN mistakes s ON s.sub_aspek_id = sa.id AND s.user_id = :user_id
    WHERE pp.project_id = :project_id
    ORDER BY pp.id, sa.id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'user_id' => $user_id,
    'project_id' => $project_id
]);

$data = [];
$total_kesalahan = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $param_id = $row['parameter_id'];
    $param_name = $row['name'];
    $sub_name = $row['name'];
    $kesalahan = $row['kesalahan'];

    $total_kesalahan += $kesalahan;

    if (!isset($data[$param_id])) {
        $data[$param_id] = [
            'parameter_name' => $param_name,
            'sub_aspek' => [],
            'total_param_kesalahan' => 0
        ];
    }

    $data[$param_id]['sub_aspek'][] = [
        'name' => $sub_name,
        'kesalahan' => $kesalahan
    ];

    $data[$param_id]['total_param_kesalahan'] += $kesalahan;
}

$total_score = 90 - $total_kesalahan;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Project Score</title>
</head>
<body>
    <h2>Score for User ID: <?= htmlspecialchars($user_id) ?> | Project ID: <?= htmlspecialchars($project_id) ?></h2>
    <?php foreach ($data as $param): ?>
        <h3><?= htmlspecialchars($param['parameter_name']) ?></h3>
        <ul>
            <?php foreach ($param['sub_aspek'] as $sub): ?>
                <li><?= htmlspecialchars($sub['name']) ?>: <?= $sub['kesalahan'] ?> kesalahan</li>
            <?php endforeach; ?>
        </ul>
        <strong>Total Kesalahan for Parameter: <?= $param['total_param_kesalahan'] ?></strong><br><br>
    <?php endforeach; ?>

    <hr>
    <h3>Total Kesalahan (Project): <?= $total_kesalahan ?></h3>
    <h3>Total Nilai: <?= $total_score ?></h3>
</body>
</html>

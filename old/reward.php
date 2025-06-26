<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/build/header.php');
include_once(__DIR__ . '/api/bdd.php');

$_SESSION['last_url'] = $_SERVER['REQUEST_URI'];
if (!isset($_SESSION['user'])) {
    echo "<script>console.log('not logged');</script>";
    header('Location: /action/userConnection.php');
    exit();
} else {
    echo "<script>console.log('logged');</script>";
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$points = $stmt->fetchColumn();
$rewards = $conn->query("SELECT * FROM reward")->fetchAll();
?>
<div class="reward-grid" style="color: white;">
    <h2>Récompenses disponibles</h2>
    <p>Vous avez <strong><?= htmlspecialchars($points) ?> points</strong></p>
    <?php foreach ($rewards as $reward): ?>
        <div class="reward-card">
            <img src="data:image/jpeg;base64, <?php echo $reward['image'] ?> " alt="<?php echo htmlspecialchars($reward['nom']) ?>" style="width: 550px; height: 450px; object-fit: cover;"/>            
            <h3><?= htmlspecialchars($reward['nom']) ?></h3>
            <p><?= htmlspecialchars($reward['description']) ?></p>
            <p><strong>Coût : <?= $reward['points'] ?> points</strong></p>
            <?php if ($points >= $reward['points']): ?>
                <form method="post" action="/unlockReward.php">
                    <input type="hidden" name="reward_id" value="<?= $reward['id'] ?>">
                    <button type="submit">Débloquer</button>
                </form>
            <?php else: ?>
                <button disabled>Indisponible</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>


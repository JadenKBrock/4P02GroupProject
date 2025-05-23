<?php
session_start();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login_pageNew.php");
    exit();
}

// Azure SQL connection
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// get user information
$tsql = "SELECT * FROM Users WHERE id = ?";
$params = array($_SESSION['user_id']);
$stmt = sqlsrv_query($conn, $tsql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$user_info = $user; // keep compatibility

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

$status_message = isset($_GET['status']) && $_GET['status'] === 'success' ? 'Changes saved!' : '';

//$base_url = "http://localhost:8080/";
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";
$page_title = "My Profile";
$page_styles = ["profile.css"];
include "../../views/header.php";
?>

<div class="main-container">
    <div class="content-container">
        <h2>My Profile</h2>

        <!-- Show status message if exists -->
        <?php if ($status_message): ?>
            <div class="status-message">
                <p><?= htmlspecialchars($status_message) ?></p>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form id="profile-form" action="update_profile.php" method="post">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user_info['first_name']) ?>" required />

                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user_info['last_name']) ?>" required />

                <label>Username:</label>
                <input type="text" value="<?= htmlspecialchars($user_info['username']) ?>" disabled />

                <label>Email:</label>
                <input type="email" value="<?= htmlspecialchars($user_info['email']) ?>" disabled />

                <label>Content Generation Frequency:</label>
                <select name="frequency" id="frequency" onchange="toggleFrequencyOptions()">
                    <option value="daily" <?= $user['frequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="weekly" <?= $user['frequency'] === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                    <option value="monthly" <?= $user['frequency'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>

                <div id="weekly-options" class="<?= $user['frequency'] === 'weekly' ? '' : 'hidden' ?>">
                    <label>Day of the Week:</label>
                    <select name="day_of_week">
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $day) {
                            $selected = ($user['day_of_week'] === $day) ? 'selected' : '';
                            echo "<option value='$day' $selected>$day</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="monthly-options" class="<?= $user['frequency'] === 'monthly' ? '' : 'hidden' ?>">
                    <label>Day of the Month:</label>
                    <input type="number" name="day_of_month" min="1" max="31" value="<?= $user['day_of_month'] ?>" />
                </div>

                <label>Generation Time:</label>
                <input type="time" name="generation_time" value="<?= $user['generation_time'] ?? '00:00:00' ?>" />

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleFrequencyOptions() {
    const frequency = document.getElementById("frequency").value;
    document.getElementById("weekly-options").classList.toggle("hidden", frequency !== "weekly");
    document.getElementById("monthly-options").classList.toggle("hidden", frequency !== "monthly");
}
</script>

<?php 
include "../../views/footer.php"; 
?>
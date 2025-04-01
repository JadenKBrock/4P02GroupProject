<?php
ob_start();
session_start();

$base_url = "http://localhost:8080/";
//$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";

if (!isset($_SESSION["user_id"])) {
     header("Location:" . $base_url . "src/Login/login_pageNew.php");
     exit();
}

$page_title = "My Profile";
//$page_styles = ["profile.css"];
include "../../views/header.php";
?>

<div class="main-container">
    <div class="content-container">
        <h2>My Profile</h2>

        <div class="form-container">
            <form id="profile-form" action="update_profile.php" method="post">
                <label>Email:</label>
                <input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                
                <label>Newsletter Frequency:</label>
                <select name="newsletter_frequency" id="newsletter-frequency" onchange="toggleCustomDateTime()">
                    <option value="daily" <?= $user['newsletter_frequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="weekly" <?= $user['newsletter_frequency'] === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                    <option value="custom" <?= $user['newsletter_frequency'] === 'custom' ? 'selected' : '' ?>>Custom</option>
                </select>

                <div id="custom-date-time" class="<?= $user['newsletter_frequency'] === 'custom' ? '' : 'hidden' ?>">
                    <label>Custom Date:</label>
                    <input type="date" name="custom_date" value="<?= $user['custom_date'] ?>" />

                    <label>Custom Time:</label>
                    <input type="time" name="custom_time" value="<?= $user['custom_time'] ?>" />
                </div>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleCustomDateTime() {
        const frequency = document.getElementById("newsletter-frequency").value;
        const customDateTime = document.getElementById("custom-date-time");
        customDateTime.classList.toggle("hidden", frequency !== "custom");
    }
</script>

<?php
include "../../views/footer.php";
?>
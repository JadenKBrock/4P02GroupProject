<?php
session_start();
ob_start(); // 启用输出缓冲

$email = $_POST['email'];
$password = $_POST['password'];

$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");
if($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} else {
    $stmt = $con->prepare("select * from login where email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    if($stmt_result->num_rows > 0) {
        $data = $stmt_result->fetch_assoc();
        if($data['password'] === $password) {
            $_SESSION['user_id'] = $data['id']; // assume that the primary key of the login table is 'id'

            // 向 Flask 应用发送 user_id
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/set_user_id");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('user_id' => $data['id'])));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $_SESSION['user_id'] = $data['id'];
            header("Location: http://127.0.0.1:5000");
            exit();
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password";
    }
}
?>

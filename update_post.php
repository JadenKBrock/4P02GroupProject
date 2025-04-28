<?php
session_start();

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];
$content = $data['content'];

// 数据库连接信息
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

// 连接数据库
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(json_encode(['success' => false, 'message' => '数据库连接失败']));
}

// 更新帖子内容
$sql = "UPDATE Posts SET post_content = ? WHERE post_id = ?";
$params = array($content, $post_id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => '更新失败：' . print_r(sqlsrv_errors(), true)]));
}

// 关闭连接
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// 返回成功响应
echo json_encode(['success' => true, 'message' => '更新成功']);
?> 
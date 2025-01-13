<?php
session_start();
session_unset(); // ลบค่าทั้งหมดใน session
session_destroy(); // ทำลาย session


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
header("Location: login_form.php"); // เปลี่ยนเส้นทางไปยังหน้า Login
exit();
?>
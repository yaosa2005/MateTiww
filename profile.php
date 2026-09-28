<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กว่ามีการล็อกอินอยู่ไหม
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้ปัจจุบัน
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 🚀 โค้ดป้องกัน Error: ถ้าหาบัญชีในฐานข้อมูลไม่เจอ (เพราะถูกรีเซ็ต) ให้บังคับเคลียร์ระบบและกลับหน้าล็อกอินทันที
if (!$user) {
    session_destroy(); 
    echo "<script>alert('เซสชันหมดอายุหรือบัญชีถูกรีเซ็ต กรุณาสมัครสมาชิกใหม่ครับ!'); window.location.href='index.php';</script>";
    exit();
}

// รูปภาพอวตารให้เลือก
$avatars = [
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Felix",
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Aneka",
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Mimi",
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Oreo",
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Jack",
    "https://api.dicebear.com/7.x/adventurer/svg?seed=Princess"
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .avatar-label input[type="radio"] { display: none; }
        .avatar-label img {
            width: 80px; height: 80px; border-radius: 50%;
            cursor: pointer; border: 3px solid transparent;
            transition: 0.3s; background-color: #f8f9fa;
        }
        .avatar-label img:hover { border-color: #0dcaf0; }
        .avatar-label input[type="radio"]:checked + img {
            border-color: #198754; background-color: #e9ecef; transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-light" style="font-family: 'Prompt', sans-serif;">

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #46875c;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php">MateTiww</a>
            <div class="d-flex align-items-center">
                <a href="home.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2">กลับหน้าฟีด</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
                    <h4 class="fw-bold mb-4 text-success">โปรไฟล์ของฉัน</h4>
                    
                    <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'https://api.dicebear.com/7.x/adventurer/svg?seed=default'); ?>" alt="Profile" class="rounded-circle mb-3 border border-3 border-success bg-light" width="120" height="120">
                    <h5 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                    <hr>

                    <form action="update_profile.php" method="POST">
                        <h6 class="fw-bold mb-3 text-secondary">เลือกรูปโปรไฟล์ใหม่:</h6>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                            <?php foreach ($avatars as $avatar): ?>
                                <label class="avatar-label">
                                    <input type="radio" name="profile_pic" value="<?php echo $avatar; ?>" <?php echo (isset($user['profile_pic']) && $user['profile_pic'] == $avatar) ? 'checked' : ''; ?>>
                                    <img src="<?php echo $avatar; ?>" alt="Avatar Option">
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold w-100 mb-3" style="background-color: #46875c; border: none;">บันทึกรูปโปรไฟล์</button>
                    </form>

                    <!-- ปุ่มออกจากระบบ -->
                    <a href="logout.php" class="btn btn-outline-danger rounded-pill px-4 fw-bold w-100">ออกจากระบบ</a>

                </div>
            </div>
        </div>
    </div>
</body>
</html>

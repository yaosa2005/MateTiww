<?php
session_start();
require_once 'includes/db_connect.php';

// 1. เช็กสิทธิ์ว่าล็อกอินอยู่และเป็น admin เท่านั้นถึงจะเข้าหน้านี้ได้
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('เฉพาะผู้ดูแลระบบเท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้!'); window.location.href='home.php';</script>";
    exit();
}

$stmt_users = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
$all_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// 3. ดึงข้อมูลประกาศทั้งหมด
$stmt_posts = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
$all_posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการหลังบ้าน (Admin) - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f4f6f9; }
        .admin-header { background: linear-gradient(45deg, #a66cd0, #ef69ac); color: white; padding: 20px 0; }
    </style>
</head>
<body>

    <div class="admin-header text-center mb-4 shadow-sm">
        <h2 class="fw-bold m-0">MateTiww - Admin Panel</h2>
        <p class="m-0 mt-2">ระบบจัดการข้อมูลหลังบ้าน</p>
    </div>

    <div class="container pb-5">
        <div class="d-flex justify-content-between mb-3">
            <h4 class="fw-bold" style="color: #6B3B80;">จัดการสมาชิก (<?php echo count($all_users); ?> คน)</h4>
            <a href="home.php" class="btn btn-outline-secondary">กลับหน้าฟีดหลัก</a>
        </div>
        
        <div class="table-responsive bg-white shadow-sm rounded mb-5">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>รูป</th>
                        <th>ชื่อ</th>
                        <th>อีเมล</th>
                        <th>สถานะ (Role)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $u): ?>
                    <tr>
                        <td><?php echo $u['user_id']; ?></td>
                        <td><img src="<?php echo htmlspecialchars($u['profile_pic']); ?>" width="40" height="40" class="rounded-circle" style="object-fit: cover;"></td>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <?php if($u['role'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h4 class="fw-bold mb-3" style="color: #6B3B80;">ประกาศทั้งหมดในระบบ (<?php echo count($all_posts); ?> โพสต์)</h4>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID โพสต์</th>
                        <th>หมวดหมู่</th>
                        <th>หัวข้อ</th>
                        <th>วันที่โพสต์</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_posts as $p): ?>
                    <tr>
                        <td><?php echo $p['post_id']; ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category']); ?></span></td>
                        <td><?php echo htmlspecialchars($p['title']); ?></td>
                        <td><?php echo $p['created_at']; ?></td>
                        <td>
                            <a href="delete_post.php?post_id=<?php echo $p['post_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('แอดมินต้องการลบโพสต์นี้ใช่หรือไม่?');">ลบโพสต์ทิ้ง</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

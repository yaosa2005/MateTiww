<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo "<script>alert('ไม่พบประกาศที่ต้องการแก้ไข!'); window.location.href='home.php';</script>";
    exit();
}

// ดึงข้อมูลโพสต์เดิมขึ้นมาแสดงในฟอร์ม
$stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = :post_id AND user_id = :user_id");
$stmt->execute([':post_id' => $post_id, ':user_id' => $_SESSION['user_id']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<script>alert('คุณไม่มีสิทธิ์แก้ไขประกาศนี้!'); window.location.href='home.php';</script>";
    exit();
}

// ถ้ามีการกดปุ่มบันทึกการแก้ไข (Form Submit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $title = trim($_POST['title']);
    $detail = trim($_POST['detail']);

    try {
        $update_stmt = $conn->prepare("UPDATE posts SET category = :category, title = :title, detail = :detail WHERE post_id = :post_id");
        $update_stmt->execute([
            ':category' => $category,
            ':title' => $title,
            ':detail' => $detail,
            ':post_id' => $post_id
        ]);

        echo "<script>alert('แก้ไขประกาศเรียบร้อยแล้ว!'); window.location.href='home.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขประกาศ - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #FCF2FB !important; font-family: 'Prompt', sans-serif;">

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card glass-form p-4">
                    <h4 class="fw-bold mb-3" style="color: #6B3B80;">แก้ไขประกาศ</h4>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted">ประเภทประกาศ</label>
                            <select class="form-select" name="category" required>
                                <option value="study" <?php echo $post['category'] == 'study' ? 'selected' : ''; ?>>หาเพื่อนติวหนังสือ</option>
                                <option value="roommate" <?php echo $post['category'] == 'roommate' ? 'selected' : ''; ?>>หารูมเมท / หอพัก</option>
                                <option value="other" <?php echo $post['category'] == 'other' ? 'selected' : ''; ?>>อื่น ๆ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">หัวข้อประกาศ</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">รายละเอียดเพิ่มเติม</label>
                            <textarea class="form-control" name="detail" rows="4" required><?php echo htmlspecialchars($post['detail']); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="home.php" class="btn btn-secondary rounded-pill px-4">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(45deg, #a66cd0, #ef69ac); border: none;">บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

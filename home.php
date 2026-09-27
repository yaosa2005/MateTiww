<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// ดึงชื่อผู้ใช้จากฐานข้อมูลเพื่อความชัวร์ (ป้องกัน Error ระบุตัวแปร Session ไม่เจอ)
$user_stmt = $conn->prepare("SELECT name FROM users WHERE user_id = :user_id");
$user_stmt->execute([':user_id' => $current_user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user_data ? $user_data['name'] : 'ผู้ใช้งาน';

// --- ระบบดึงข้อมูลและตัวกรองหมวดหมู่ ---
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($category_filter == 'study' || $category_filter == 'roommate') {
    // ดึงเฉพาะหมวดหมู่ที่เลือก
    $stmt = $conn->prepare("
        SELECT posts.*, users.name as poster_name 
        FROM posts 
        LEFT JOIN users ON posts.user_id = users.user_id 
        WHERE posts.category = :category
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([':category' => $category_filter]);
} else {
    // ดึงโพสต์ทั้งหมด (ค่าเริ่มต้น)
    $stmt = $conn->prepare("
        SELECT posts.*, users.name as poster_name 
        FROM posts 
        LEFT JOIN users ON posts.user_id = users.user_id 
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute();
}
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- เชื่อมไฟล์ CSS พร้อมเทคนิคตัดแคช -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-info" href="home.php">MateTiww</a>
            <div class="d-flex align-items-center">
                <span class="me-3 fw-medium text-light">สวัสดี, คุณ <?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mt-4 pt-5"> <!-- เพิ่ม pt-5 ดันเนื้อหาลงมาไม่ให้โดน Navbar บัง -->
        <div class="row justify-content-center">
            
            <!-- ฟอร์มสร้างประกาศใหม่ -->
            <div class="col-md-8 mb-4">
                <div class="card glass-form p-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-white">สร้างประกาศใหม่</h5>
                        <form action="post_action.php" method="POST">
                            <div class="mb-3">
                                <select class="form-select" name="category" required>
                                    <option value="" disabled selected>เลือกประเภทประกาศ...</option>
                                    <option value="study">หาเพื่อนติวหนังสือ</option>
                                    <option value="roommate">หารูมเมท / หอพัก</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="title" placeholder="หัวข้อประกาศ" required>
                            </div>
                            <div class="mb-3">
                                <!-- เปลี่ยน name="detail" กลับเป็น name="content" -->
                                <textarea class="form-control" name="content" rows="2" placeholder="รายละเอียดเพิ่มเติม" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold btn-custom-pulse">โพสต์ประกาศ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ฟีดโพสต์ล่าสุด -->
            <div class="col-md-8">
                
                <!-- ส่วนหัวของฟีด และ ปุ่มกรองหมวดหมู่ -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-white">โพสต์ล่าสุด</h5>
                    <div class="btn-group shadow-sm">
                        <a href="home.php" class="btn btn-sm <?php echo empty($category_filter) ? 'btn-light' : 'btn-outline-light'; ?>">ทั้งหมด</a>
                        <a href="home.php?category=study" class="btn btn-sm <?php echo $category_filter == 'study' ? 'btn-info' : 'btn-outline-info'; ?>">ติวหนังสือ</a>
                        <a href="home.php?category=roommate" class="btn btn-sm <?php echo $category_filter == 'roommate' ? 'btn-success' : 'btn-outline-success'; ?>">หารูมเมท</a>
                    </div>
                </div>
                
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="card glass-card p-3 mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold text-info"><?php echo htmlspecialchars($post['poster_name'] ?? 'ไม่ทราบชื่อ'); ?></span>
                                        <small class="text-white-50 ms-2"><?php echo $post['created_at']; ?></small>
                                    </div>
                                    <div>
                                        <?php if($post['category'] == 'study'): ?>
                                            <span class="badge badge-study rounded-pill px-3 py-2">ติวหนังสือ</span>
                                        <?php else: ?>
                                            <span class="badge badge-roommate rounded-pill px-3 py-2">หารูมเมท</span>
                                        <?php endif; ?>
                                        
                                        <!-- ปุ่มลบโพสต์ (แสดงเฉพาะเจ้าของโพสต์) -->
                                        <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                            <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill ms-2"
                                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประกาศนี้?');">
                                               ลบ
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-3 text-white"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="text-light opacity-75"><?php echo nl2br(htmlspecialchars($post['detail'])); ?></p>
                                
                                <hr class="border-secondary">
                                
                                <!-- ส่วนแสดงคอมเมนต์ -->
                                <div class="mb-3">
                                    <h6 class="fw-bold fs-6 mb-2 text-white-50">ความคิดเห็น:</h6>
                                    <?php 
                                    $comment_stmt = $conn->prepare("
                                        SELECT comments.*, users.name as commenter_name 
                                        FROM comments 
                                        LEFT JOIN users ON comments.user_id = users.user_id 
                                        WHERE comments.post_id = :post_id 
                                        ORDER BY comments.created_at ASC
                                    ");
                                    $comment_stmt->execute([':post_id' => $post['post_id']]);
                                    $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($comments) > 0): 
                                        foreach ($comments as $comment): 
                                    ?>
                                        <div class="comment-box">
                                            <span class="fw-bold text-info" style="font-size: 0.9rem;"><?php echo htmlspecialchars($comment['commenter_name'] ?? 'ไม่ทราบชื่อ'); ?>:</span>
                                            <span class="text-light" style="font-size: 0.9rem;"><?php echo htmlspecialchars($comment['content']); ?></span>
                                        </div>
                                    <?php 
                                        endforeach; 
                                    else: 
                                    ?>
                                        <p class="text-white-50 small mb-0">ยังไม่มีความคิดเห็น เป็นคนแรกที่คอมเมนต์สิ!</p>
                                    <?php endif; ?>
                                </div>

                                <!-- ฟอร์มพิมพ์คอมเมนต์ -->
                                <form action="comment_action.php" method="POST">
                                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="content" placeholder="เขียนความคิดเห็น..." required>
                                        <button class="btn btn-outline-info fw-bold btn-custom-pulse" type="submit">ส่ง</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card glass-card text-center py-5">
                        <p class="text-white-50 mb-0">ยังไม่มีประกาศในขณะนี้ หรือไม่พบประกาศในหมวดหมู่นี้</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>
</html>

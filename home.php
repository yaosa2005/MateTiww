<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// ดึงชื่อผู้ใช้จากฐานข้อมูลเพื่อความชัวร์
$user_stmt = $conn->prepare("SELECT name FROM users WHERE user_id = :user_id");
$user_stmt->execute([':user_id' => $current_user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user_data ? $user_data['name'] : 'ผู้ใช้งาน';

// --- ระบบดึงข้อมูลและตัวกรองหมวดหมู่ ---
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($category_filter == 'study' || $category_filter == 'roommate') {
    // เพิ่มการดึง profile_pic มาด้วย
    $stmt = $conn->prepare("
        SELECT posts.*, users.name as poster_name, users.profile_pic as poster_pic 
        FROM posts 
        LEFT JOIN users ON posts.user_id = users.user_id 
        WHERE posts.category = :category
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([':category' => $category_filter]);
} else {
    // เพิ่มการดึง profile_pic มาด้วย
    $stmt = $conn->prepare("
        SELECT posts.*, users.name as poster_name, users.profile_pic as poster_pic 
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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-info" href="home.php">MateTiww</a>
            
            <div class="d-flex align-items-center">
                <!-- เหลือแค่ลิงก์ไปหน้าโปรไฟล์ เอาปุ่มออกจากระบบออกแล้ว -->
                <a href="profile.php" class="text-white text-decoration-none fw-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z"/>
</svg> (คุณ <?php echo htmlspecialchars($user_name); ?>)
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mt-4 pt-5">
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
                                    <div class="d-flex align-items-center">
                                        <!-- เพิ่มรูปภาพโปรไฟล์วงกลมตรงนี้ -->
                                        <img src="<?php echo htmlspecialchars($post['poster_pic'] ?? 'https://api.dicebear.com/7.x/adventurer/svg?seed=default'); ?>" class="rounded-circle me-2 bg-light" width="35" height="35" alt="Avatar">
                                        <div>
                                            <span class="fw-bold text-info d-block" style="line-height: 1.2;"><?php echo htmlspecialchars($post['poster_name'] ?? 'ไม่ทราบชื่อ'); ?></span>
                                            <small class="text-white-50"><?php echo $post['created_at']; ?></small>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if($post['category'] == 'study'): ?>
                                            <span class="badge badge-study rounded-pill px-3 py-2">ติวหนังสือ</span>
                                        <?php else: ?>
                                            <span class="badge badge-roommate rounded-pill px-3 py-2">หารูมเมท</span>
                                        <?php endif; ?>
                                        
                                        <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                            <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill ms-2"
                                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประกาศนี้?');">ลบ</a>
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
                                    // เพิ่มดึงรูป profile_pic ของคนคอมเมนต์มาด้วย
                                    $comment_stmt = $conn->prepare("
                                        SELECT comments.*, users.name as commenter_name, users.profile_pic as commenter_pic 
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
                                        <div class="comment-box d-flex align-items-start mb-2">
                                            <!-- รูปโปรไฟล์วงกลมของคนคอมเมนต์ -->
                                            <img src="<?php echo htmlspecialchars($comment['commenter_pic'] ?? 'https://api.dicebear.com/7.x/adventurer/svg?seed=default'); ?>" class="rounded-circle me-2 mt-1 bg-light" width="25" height="25" alt="Avatar">
                                            <div>
                                                <span class="fw-bold text-info" style="font-size: 0.9rem;"><?php echo htmlspecialchars($comment['commenter_name'] ?? 'ไม่ทราบชื่อ'); ?>:</span>
                                                <span class="text-light" style="font-size: 0.9rem;"><?php echo htmlspecialchars($comment['content']); ?></span>
                                            </div>
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

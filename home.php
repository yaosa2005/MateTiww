<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// ดึงข้อมูลชื่อและ role ล่าสุดจากฐานข้อมูลโดยตรง
$user_stmt = $conn->prepare("SELECT name, role FROM users WHERE user_id = :user_id");
$user_stmt->execute([':user_id' => $current_user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

$user_name = $user_data ? $user_data['name'] : 'ผู้ใช้งาน';
$current_role = $user_data ? $user_data['role'] : 'user';

// --- ระบบดึงข้อมูลและตัวกรองหมวดหมู่ (แสดงเฉพาะโพสต์ภายใน 24 ชม. ล่าสุด) ---
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($category_filter == 'study' || $category_filter == 'roommate' || $category_filter == 'other' || $category_filter == 'admin_news') {
    $stmt = $conn->prepare("
        SELECT posts.*, users.name as poster_name, users.profile_pic as poster_pic 
        FROM posts 
        LEFT JOIN users ON posts.user_id = users.user_id 
        WHERE posts.category = :category AND posts.created_at >= datetime('now', '+7 hours', '-24 hours')
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([':category' =>$category_filter]);
} else {
    $stmt =$conn->prepare("
        SELECT posts.*, users.name as poster_name, users.profile_pic as poster_pic 
        FROM posts 
        LEFT JOIN users ON posts.user_id = users.user_id 
        WHERE posts.created_at >= datetime('now', '+7 hours', '-24 hours')
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute();
}
$posts =$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        @media (max-width: 768px) {
            .container { padding-left: 12px; padding-right: 12px; }
            .card { border-radius: 15px; }
            #draggableView { width: 100px !important; height: 100px !important; }
            .feed-header-actions { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .btn-group { width: 100%; display: flex; }
            .btn-group .btn { flex: 1; font-size: 0.75rem; padding: 0.375rem 0.1rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-info" href="home.php">MateTiww</a>
            <div class="d-flex align-items-center gap-2">
                
                <!-- ⚙️ ปุ่มทางเข้าหลังบ้าน (แสดงเฉพาะ Admin เท่านั้น) -->
                <?php if ($current_role === 'admin'): ?>
                    <a href="admin_dashboard.php" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 shadow-sm">
                        ⚙️ จัดการหลังบ้าน
                    </a>
                <?php endif; ?>

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
            
            <!-- ฟอร์มสร้างประกาศใหม่ (แยกตามสิทธิ์) -->
            <div class="col-md-8 mb-4">
                <div class="card glass-form p-4">
                    <div class="card-body">
                        
                        <?php if ($current_role === 'admin'): ?>
                            <!-- 📢 ฟอร์มสำหรับ Admin แจ้งข่าวสาร -->
                            <h5 class="fw-bold mb-3" style="color: #ff4d4d;">📢 ประกาศแจ้งข่าวจากผู้ดูแลระบบ</h5>
                            <form action="post_action.php" method="POST">
                                <input type="hidden" name="category" value="admin_news">
                                <div class="mb-3">
                                    <input type="text" class="form-control border-danger" name="title" placeholder="หัวข้อประกาศแจ้งข่าว..." required>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control border-danger" name="content" rows="2" placeholder="รายละเอียดข่าวสารที่ต้องการแจ้ง..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">📢 โพสต์แจ้งข่าว</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- 👤 ฟอร์มสำหรับ User ทั่วไป -->
                            <h5 class="fw-bold mb-3 text-white">สร้างประกาศใหม่</h5>
                            <form action="post_action.php" method="POST">
                                <div class="mb-3">
                                    <select class="form-select" name="category" required>
                                        <option value="" disabled selected>เลือกประเภทประกาศ...</option>
                                        <option value="study">หาเพื่อนติวหนังสือ</option>
                                        <option value="roommate">หารูมเมท / หอพัก</option>
                                        <option value="other">อื่น ๆ</option>
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
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- ฟีดโพสต์ล่าสุด -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-3 feed-header-actions">
                    <h5 class="fw-bold mb-0 text-white">โพสต์ล่าสุด (แสดงภายใน 24 ชม.)</h5>
                    <div class="btn-group shadow-sm">
                        <a href="home.php" class="btn btn-sm <?php echo empty($category_filter) ? 'btn-light' : 'btn-outline-light'; ?>">ทั้งหมด</a>
                        <a href="home.php?category=study" class="btn btn-sm <?php echo $category_filter == 'study' ? 'btn-info' : 'btn-outline-info'; ?>">ติวหนังสือ</a>
                        <a href="home.php?category=roommate" class="btn btn-sm <?php echo $category_filter == 'roommate' ? 'btn-success' : 'btn-outline-success'; ?>">หารูมเมท</a>
                        <a href="home.php?category=other" class="btn btn-sm <?php echo $category_filter == 'other' ? 'btn-warning' : 'btn-outline-warning'; ?>">อื่น ๆ</a>
                    </div>
                </div>
                
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as$post): ?>
                        <div class="card glass-card p-3 mb-3" <?php if($post['category'] == 'admin_news') echo 'style="border: 1px solid #ff4d4d; box-shadow: 0 0 15px rgba(255, 77, 77, 0.2);"'; ?>>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($post['poster_pic'] ?? 'https://api.dicebear.com/7.x/adventurer/svg?seed=default'); ?>" class="rounded-circle me-2 bg-light" width="35" height="35" alt="Avatar">
                                        <div>
                                            <a href="view_Profile.php?id=<?php echo $post['user_id']; ?>" class="fw-bold d-block" style="text-decoration: none; color: #a66cd0; line-height: 1.2;">
                                                <?php echo htmlspecialchars($post['poster_name'] ?? 'ไม่ทราบชื่อ'); ?>
                                            </a>
                                            <small class="text-white-50"><?php echo $post['created_at']; ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap justify-content-end gap-1">
                                        
                                        <!-- ป้ายสถานะ (Badge) -->
                                        <?php if($post['category'] == 'study'): ?>
                                            <span class="badge badge-study rounded-pill px-3 py-2">ติวหนังสือ</span>
                                        <?php elseif($post['category'] == 'roommate'): ?>
                                            <span class="badge badge-roommate rounded-pill px-3 py-2">หารูมเมท</span>
                                        <?php elseif($post['category'] == 'admin_news'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">📢 ประกาศจากแอดมิน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">อื่น ๆ</span>
                                        <?php endif; ?>
                                        
                                        <!-- ปุ่มแก้ไข/ลบ -->
                                        <?php if($post['user_id'] == $_SESSION['user_id'] ||$current_role === 'admin'): ?>
                                            <?php if($post['user_id'] ==$_SESSION['user_id']): ?>
                                                <a href="edit_post.php?post_id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill ms-1">แก้ไข</a>
                                            <?php endif; ?>
                                            <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill ms-1" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประกาศนี้?');">ลบ</a>
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
                                    $comment_stmt =$conn->prepare("
                                        SELECT comments.*, users.name as commenter_name, users.profile_pic as commenter_pic 
                                        FROM comments 
                                        LEFT JOIN users ON comments.user_id = users.user_id 
                                        WHERE comments.post_id = :post_id 
                                        ORDER BY comments.created_at ASC
                                    ");
                                    $comment_stmt->execute([':post_id' =>$post['post_id']]);
                                    $comments =$comment_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($comments) > 0): 
                                        foreach ($comments as$comment): 
                                    ?>
                                        <div class="comment-box d-flex align-items-start mb-2">
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
                        <p class="text-white-50 mb-0">ยังไม่มีประกาศในขณะนี้ หรือโพสต์ทั้งหมดหมดอายุ (เกิน 24 ชม.) แล้ว</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

<div id="draggableView" style="position: fixed; bottom: 20px; left: 20px; z-index: 1000; width: 130px; height: 130px; cursor: grab; touch-action: none;">
    <img src="vedio/Blue Working Cat Animation.svg" alt="Working Cat" style="width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.15)); pointer-events: none;">
</div>

<script>
const draggable = document.getElementById('draggableView');
let isDragging = false;
let startX, startY, initialLeft, initialTop;

function startDrag(clientX, clientY) {
    isDragging = true;
    draggable.style.cursor = 'grabbing';
    startX = clientX;
    startY = clientY;
    const rect = draggable.getBoundingClientRect();
    initialLeft = rect.left;
    initialTop = rect.top;
    draggable.style.bottom = 'auto';
    draggable.style.right = 'auto';
    draggable.style.left = initialLeft + 'px';
    draggable.style.top = initialTop + 'px';
}

function onDrag(clientX, clientY) {
    if (!isDragging) return;
    const dx = clientX - startX;
    const dy = clientY - startY;
    draggable.style.left = (initialLeft + dx) + 'px';
    draggable.style.top = (initialTop + dy) + 'px';
}

function stopDrag() {
    if (isDragging) {
        isDragging = false;
        draggable.style.cursor = 'grab';
    }
}

draggable.addEventListener('mousedown', (e) => { startDrag(e.clientX, e.clientY); e.preventDefault(); });
document.addEventListener('mousemove', (e) => onDrag(e.clientX, e.clientY));
document.addEventListener('mouseup', stopDrag);
draggable.addEventListener('touchstart', (e) => { if (e.touches.length > 0) startDrag(e.touches[0].clientX, e.touches[0].clientY); });
document.addEventListener('touchmove', (e) => { if (isDragging && e.touches.length > 0) onDrag(e.touches[0].clientX, e.touches[0].clientY); }, { passive: false });
document.addEventListener('touchend', stopDrag);
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if(isset($_GET['status']) &&$_GET['status'] == 'success'): ?>
<script>
    Swal.fire({
        title: 'โพสต์สำเร็จ! ✨',
        text: 'ประกาศของคุณถูกแสดงบนฟีดเรียบร้อยแล้ว',
        icon: 'success',
        iconColor: '#ef69ac', 
        color: '#6B3B80', 
        background: '#ffffff',
        timer: 2500,
        showConfirmButton: false,
        backdrop: `rgba(166, 108, 208, 0.4)`
    }).then(() => {
        window.history.replaceState(null, null, window.location.pathname);
    });
</script>
<?php endif; ?>

</body>
</html>


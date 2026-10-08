<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
header("Location: index.php");
exit();
}

$target_user_id = $_GET['id'] ?? null;

if (!$target_user_id) {
echo "<script>alert('ไม่พบผู้ใช้งาน!'); window.location.href='home.php';</script>";
exit();
}

// ดึงข้อมูลเจ้าของโปรไฟล์
$stmt = $conn->prepare("SELECT user_id, name, email, profile_pic FROM users WHERE user_id = ?");
$stmt->execute([$target_user_id]);
$target_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target_user) {
    echo "<script>alert('ผู้ใช้งานนี้ไม่มีอยู่ในระบบแล้ว'); window.location.href='home.php';</script>";
    exit();
}

//  ดึงรีวิวทั้งหมด (แก้ไขให้ JOIN กับ reviewer_id และหาข้อมูลเป้าหมายจาก target_user_id)
$review_stmt = $conn->prepare("
    SELECT reviews.*, users.name as reviewer_name, users.profile_pic as reviewer_pic 
    FROM reviews 
    LEFT JOIN users ON reviews.reviewer_id = users.user_id 
    WHERE reviews.target_user_id = :target_id 
    ORDER BY reviews.created_at DESC
");
$review_stmt->execute([':target_id' => $target_user_id]);
$reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณหาค่าเฉลี่ยคะแนนดาว
$avg_rating = 0;
if (count($reviews) > 0) {
    $total_score = array_sum(array_column($reviews, 'rating'));
    $avg_rating = round($total_score / count($reviews), 1);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของ <?php echo htmlspecialchars($target_user['name']); ?> - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
 .rating-stars {
display: flex;
flex-direction: row-reverse;
justify-content: center;
gap: 10px;
        }
        .rating-stars input { display: none; }
        .rating-stars label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: 0.2s;
        }
        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #ef69ac; 
        }
        .review-card {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(166, 108, 208, 0.2);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            text-align: left;
        }
    </style>
</head>
<body style="background-color: #FCF2FB !important; font-family: 'Prompt', sans-serif;">


<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #d8a0e4;">
<div class="container">
<a class="navbar-brand fw-bold" href="home.php">MateTiww</a>
<div class="d-flex">
<a href="home.php" class="btn btn-outline-light btn-sm rounded-pill px-3">กลับหน้าฟีด</a>
         </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                
                <!-- ข้อมูลโปรไฟล์หลัก -->
                <div class="glass-card p-4 text-center mb-4">
                    <img src="<?php echo htmlspecialchars($target_user['profile_pic'] ?? 'pic/pro1.jpg'); ?>" class="rounded-circle mb-3 border border-3 shadow-sm" width="120" height="120" style="border-color: #a66cd0; object-fit: cover;">
                    <h4 class="fw-bold" style="color: #6B3B80;"><?php echo htmlspecialchars($target_user['name']); ?></h4>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($target_user['email']); ?></p>
                    
                    <!-- แสดงดาว -->
                    <div class="mt-2">
                        <span class="fs-5 fw-bold" style="color: #ef69ac;">★ <?php echo $avg_rating; ?></span>
                        <span class="text-muted small">(จาก <?php echo count($reviews); ?> รีวิว)</span>
                    </div>
                </div>

                <!-- ฟอร์มรีวิวซ่อนไว้ถ้าเป็นโปรไฟล์ตัวเอง) -->
                <?php if ($_SESSION['user_id'] != $target_user_id): ?>
                <div class="glass-form p-4 mb-4">
                    <h5 class="fw-bold text-center mb-3" style="color: #a66cd0;">เขียนรีวิวให้เพื่อนคนนี้</h5>
                    <form action="review_action.php" method="POST">
                        <input type="hidden" name="target_user_id" value="<?php echo $target_user_id; ?>">
                        
                        <div class="text-center mb-3">
                            <div class="rating-stars">
                                <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
                                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <textarea name="comment" class="form-control" rows="3" placeholder="เพื่อนคนนี้ติวเป็นยังไงบ้าง? นิสัยดีไหม?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill" style="background: linear-gradient(45deg, #a66cd0, #ef69ac); border: none;">ส่งรีวิว</button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- รายการรีวิวทั้งหมด -->
<h5 class="fw-bold mb-3" style="color: #6B3B80;">รีวิวจากเพื่อนๆ (<?php echo count($reviews); ?>)</h5>     
<?php if (count($reviews) > 0): ?>
<?php foreach ($reviews as $rev): ?>
 <div class="review-card shadow-sm">
 <div class="d-flex align-items-center mb-2">
  <img src="<?php echo htmlspecialchars($rev['reviewer_pic'] ?? 'pic/pro1.jpg'); ?>" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover;">
 <div>
    <h6 class="fw-bold mb-0" style="color: #6B3B80; font-size: 0.95rem;"><?php echo htmlspecialchars($rev['reviewer_name'] ?? 'ผู้ใช้งาน'); ?></h6>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo $rev['created_at']; ?></small>
                                </div>
                                <div class="ms-auto text-warning fw-bold">
                                    <?php 
                                    for($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rev['rating'] ? '<span style="color: #ef69ac;">★</span>' : '<span style="color: #ddd;">★</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="glass-card text-center py-4">
                        <p class="text-muted mb-0">ยังไม่มีรีวิวสำหรับเพื่อนคนนี้ เป็นคนแรกที่มารีวิวสิ!</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>
</html>

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

// โค้ดป้องกัน Error
if (!$user) {
    session_destroy(); 
    echo "<script>alert('เซสชันหมดอายุหรือบัญชีถูกรีเซ็ต กรุณาสมัครสมาชิกใหม่ครับ!'); window.location.href='index.php';</script>";
    exit();
}

$avatars = [
    "pic/pro6.jpg",
    "pic/pro2.jpg",
    "pic/pro3.jpg",
    "pic/pro4.jpg",
    "pic/pro5.jpg",
    "pic/pro7.jpg"
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
        /* พื้นหลังหน้าโปรไฟล์โทนชมพูอ่อน */
        body {
            background-color: #FCF2FB !important; 
        }
        .avatar-label input[type="radio"] { display: none; }
        .avatar-label img {
            width: 80px; height: 80px; border-radius: 50%;
            cursor: pointer; border: 3px solid transparent;
            transition: 0.3s; background-color: #f8f9fa;
            object-fit: cover; 
        }
        /* กรอบตอนเอาเมาส์ชี้ เป็นสีม่วงอ่อน */
        .avatar-label img:hover { border-color: #d5b2ca; }
        /* กรอบตอนคลิกเลือก เป็นสีชมพูเข้ม */
        .avatar-label input[type="radio"]:checked + img {
            border-color: #ef69ac; background-color: #fce8f3; transform: scale(1.1);
        }
        .main-profile-img {
            object-fit: cover; 
        }
        /* เปลี่ยนสีกรอบรูปโปรไฟล์หลักเป็นสีม่วง */
        .border-theme {
            border-color: #a66cd0 !important;
        }
        /* สีข้อความหัวข้อ */
        .text-theme {
            color: #a66cd0 !important;
        }
        /* ปุ่มบันทึกสีม่วง-ชมพู */
        .btn-theme {
            background: linear-gradient(45deg, #a66cd0, #ef69ac);
            border: none;
            color: #ffffff;
            transition: all 0.3s;
        }
        .btn-theme:hover {
            background: linear-gradient(45deg, #8B52A1, #d35996);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(166, 108, 208, 0.4);
        }
    </style>
</head>
<body style="font-family: 'Prompt', sans-serif;">

    <!-- แถบเมนูด้านบนสีม่วงอ่อน -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #d8a0e4; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php" style="text-shadow: 0 0 5px rgba(0,0,0,0.1);">MateTiww</a>
            <div class="d-flex align-items-center">
                <a href="home.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2">กลับหน้าฟีด</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- การ์ดกระจกโปร่งแสงนิดๆ โทนสีม่วง -->
                <div class="card shadow-sm border-0 rounded-4 p-4 text-center" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(166, 108, 208, 0.2) !important;">
                    
                    <h4 class="fw-bold mb-4 text-theme">โปรไฟล์ของฉัน</h4>
                    
                    <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'pic/pro1.jpg'); ?>" alt="Profile" class="rounded-circle mb-3 border border-3 border-theme bg-light main-profile-img d-block mx-auto" width="120" height="120">
                    
                    <h5 class="fw-bold" style="color: #6B3B80;"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                    <hr style="border-color: #d5b2ca;">

                    <form action="update_profile.php" method="POST">
                        <h6 class="fw-bold mb-3" style="color: #6B3B80;">เลือกรูปโปรไฟล์ : </h6>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                            <?php foreach ($avatars as $avatar): ?>
                                <label class="avatar-label">
                                    <input type="radio" name="profile_pic" value="<?php echo $avatar; ?>" <?php echo (isset($user['profile_pic']) && $user['profile_pic'] == $avatar) ? 'checked' : ''; ?>>
                                    <img src="<?php echo $avatar; ?>" alt="Avatar Option">
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-theme rounded-pill px-4 fw-bold w-100 mb-3">บันทึกรูปโปรไฟล์</button>
                    </form>

                    <!-- ปุ่มออกจากระบบโทนสีแดงอมม่วง ให้ดูเข้ากับตีมแต่ยังชัดว่าเป็นปุ่มอันตราย -->
                    <a href="logout.php" class="btn btn-outline-danger rounded-pill px-4 fw-bold w-100" style="color: #d9534f; border-color: #d9534f;">ออกจากระบบ</a>

                </div>
            </div>
        </div>
    </div>

    <script>
        const avatarOptions = document.querySelectorAll('input[name="profile_pic"]');
        const mainProfileImg = document.querySelector('.main-profile-img');

        avatarOptions.forEach(option => {
            option.addEventListener('change', function() {
                if(this.checked) {
                    mainProfileImg.src = this.value; 
                }
            });
        });
    </script>
</body>
</html>
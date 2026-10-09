<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ / สมัครสมาชิก - MateTiww</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="login-page"> 
<div class="login-wrapper">
<div class="login-container">
<div class="card glass-form p-4 shadow-lg text-white">
<div class="card-body text-center">
<h2 class="fw-bold mb-2 text-info">MateTiww</h2>
<p class="text-white-50 mb-4" style="font-size: 0.9rem;">ระบบหาเพื่อนติวและหารูมเมท</p>
                    
<ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
         <button class="nav-link active btn-sm text-white" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab">เข้าสู่ระบบ</button>
     </li>
     <li class="nav-item" role="presentation">
         <button class="nav-link btn-sm text-white-50" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab">สมัครสมาชิก</button>
        </li>
        </ul>

 <div class="tab-content" id="pills-tabContent">
 <!-- แท็บเข้าสู่ระบบ (เพิ่มช่องรหัสผ่าน) -->
<div class="tab-pane fade show active" id="pills-login" role="tabpanel">
<form action="login_action.php" method="POST">
<div class="mb-3 text-start">
<label class="form-label text-white-50 small">อีเมลมหาวิทยาลัย</label>
<input type="email" class="form-control" name="email" placeholder="กรอกอีเมลของคุณ" required>
     </div>
<!-- เพิ่มช่องกรอกรหัสผ่าน -->
<div class="mb-3 text-start">
<label class="form-label text-white-50 small">รหัสผ่าน</label>
<input type="password" class="form-control" name="password" placeholder="กรอกรหัสผ่านของคุณ" required>
    </div>
<button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold btn-custom-pulse mt-2">เข้าสู่ระบบ</button>
    </form>
    </div>

    <!-- แท็บสมัครสมาชิก เพิ่มช่องตั้งรหัสผ่านสำหรับผู้ใช้ใหม่ด้วย-->
<div class="tab-pane fade" id="pills-register" role="tabpanel">
<form action="register_action.php" method="POST">
<div class="mb-2 text-start">
<label class="form-label text-white-50 small">ชื่อเล่น</label>
<input type="text" class="form-control" name="name" placeholder="กรอกชื่อเล่นของคุณ" required>
     </div>
<div class="mb-2 text-start">
<label class="form-label text-white-50 small">อีเมลมหาวิทยาลัย</label>
<input type="email" class="form-control" name="email" placeholder="กรอกอีเมลเพื่อสมัคร" required>
    </div>
    <!-- เพิ่มช่องตั้งรหัสผ่านตอนสมัคร -->
<div class="mb-3 text-start">
<label class="form-label text-white-50 small">รหัสผ่าน</label>
<input type="password" class="form-control" name="password" placeholder="ตั้งรหัสผ่าน" required>
 </div>
<button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold btn-custom-pulse mt-2" style="background: linear-gradient(45deg, #46875c, #749F77); border: none;">ยืนยันสมัครสมาชิก</button>
 </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    try {
        // ใช้ชื่อคอลัมน์ target_user_id และ reviewer_id ให้ตรงกับฐานข้อมูลใหม่
        $stmt = $conn->prepare("INSERT INTO reviews (target_user_id, reviewer_id, rating, comment) VALUES (:target_user_id, :reviewer_id, :rating, :comment)");
        
        $stmt->execute([
            ':target_user_id' => $target_user_id,
            ':reviewer_id' => $reviewer_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        echo "<script>alert('ส่งรีวิวเรียบร้อยแล้ว!'); window.location.href='view_Profile.php?id=" . $target_user_id . "';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); window.history.back();</script>";
    }

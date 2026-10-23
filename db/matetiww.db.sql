BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS comments (
        comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    );
CREATE TABLE IF NOT EXISTS posts (
        post_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        category TEXT NOT NULL,
        title TEXT NOT NULL,
        detail TEXT NOT NULL,
        status TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    );
CREATE TABLE IF NOT EXISTS reviews (
        review_id INTEGER PRIMARY KEY AUTOINCREMENT,
        target_user_id INTEGER NOT NULL,
        reviewer_id INTEGER NOT NULL,
        rating INTEGER NOT NULL,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (target_user_id) REFERENCES users(user_id),
        FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
    );
CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        profile_pic TEXT DEFAULT 'pic/pro1.jpg',
        role TEXT DEFAULT 'user' 
    );
INSERT INTO "comments" VALUES (1,2,3,'เข้าใจแล้วคค่ะแอดด','2026-10-08 09:11:13');
INSERT INTO "comments" VALUES (2,3,5,'สนใจครับ','2026-10-08 09:15:10');
INSERT INTO "comments" VALUES (3,2,5,'เข้าใจแล้วค้าบบ','2026-10-08 09:15:39');
INSERT INTO "comments" VALUES (4,3,6,'สนใจครับสมาชิก 🥺','2026-10-08 09:17:06');
INSERT INTO "comments" VALUES (5,2,6,'ok','2026-10-08 09:17:43');
INSERT INTO "comments" VALUES (6,1,7,'สนใจค่ะ','2026-10-08 09:19:46');
INSERT INTO "comments" VALUES (7,10,4,'สนใจครับ','2026-10-09 09:58:10');
INSERT INTO "comments" VALUES (8,12,3,'สนใจค่าาาาาาาาาาาาา','2026-10-10 02:52:59');
INSERT INTO "comments" VALUES (9,12,6,'สนใจครับ','2026-10-10 02:53:42');
INSERT INTO "posts" VALUES (1,2,'roommate','หอ 11','หาเพื่อนอยู่ด้วยค่ะ ขอผู้หญิง''''''',NULL,'2026-10-08 16:08:04');
INSERT INTO "posts" VALUES (2,1,'study','ประกาศ ๆ ช่วงนี้อ่านหนังสือแล้วรือยังงงงงงง','ช่วงสอบกำลังเริ่มเเล้ว',NULL,'2026-10-08 16:09:30');
INSERT INTO "posts" VALUES (3,4,'other','หาเพื่อนผู้ชายนัดเตะบอล','วันนี้เวลา 1 ทุ่ม รวมตัวลานอิฐ ⚽',NULL,'2026-10-08 16:14:06');
INSERT INTO "posts" VALUES (4,8,'study','ติวบัญชี','ใต้ตึกคณะ',NULL,'2026-10-08 16:24:59');
INSERT INTO "posts" VALUES (5,2,'study','หาเพื่อนติว','ใต้ติวคณะวิทยาการสื่อสาร 1 ทุ่ม',NULL,'2026-10-09 10:34:48');
INSERT INTO "posts" VALUES (6,3,'roommate','หอนอกมอ','หอนอกมอ หาเพื่อนผญ........',NULL,'2026-10-09 14:28:29');
INSERT INTO "posts" VALUES (8,1,'admin_news','ประกาศจากแอดมิน','📣 ประกาศด่วนจากแอดมิน!

ตอนนี้แอดมินยังมีชีวิตอยู่ และกำลังเร่งเคลียร์งาน/ตอบแชทให้ทุกคนอยู่นะครับ/คะ ใครทักมาแล้วยังไม่ได้คำตอบ อดใจรอสักนิด อย่าเพิ่งงอนกันน้า ขอบคุณทุกคนที่คอยสนับสนุนกันตลอดครับ/ค่ะ 🚀',NULL,'2026-10-09 14:38:28');
INSERT INTO "posts" VALUES (10,5,'roommate','หอพักหลังมอ','มีเพื่อนผู้ชายสนใจไหมครับ ขอชายเเท้เตะบอล',NULL,'2026-10-09 16:57:28');
INSERT INTO "posts" VALUES (11,3,'other','ปรึกษาปัญหาโปรเจคจบ........','วิชาิะไรก็ได้ใต้ตึก วสส.',NULL,'2026-10-10 06:22:15');
INSERT INTO "posts" VALUES (12,2,'study','ติววิชาเว็บ','ที่ตึกวสส. เวลา 7 โมง',NULL,'2026-10-10 09:50:39');
INSERT INTO "reviews" VALUES (1,1,3,5,'แอดมินเริ่ด
','2026-10-08 09:11:01');
INSERT INTO "reviews" VALUES (2,1,4,5,'ดีมากครับ ทันเวลาตลอด','2026-10-08 09:12:42');
INSERT INTO "reviews" VALUES (3,1,6,4,'DDDD','2026-10-08 09:17:19');
INSERT INTO "reviews" VALUES (4,1,7,5,'ดีมากค่าาาาาาาาาาาาาาา ','2026-10-08 09:19:31');
INSERT INTO "reviews" VALUES (5,1,8,5,'ดีมากกก','2026-10-08 09:25:28');
INSERT INTO "reviews" VALUES (6,1,2,5,'ดีมาก','2026-10-08 09:45:20');
INSERT INTO "reviews" VALUES (7,4,2,5,'น่ารัก','2026-10-09 07:25:50');
INSERT INTO "reviews" VALUES (8,2,3,5,'คนนี้เริ่ดดดด','2026-10-09 07:29:55');
INSERT INTO "reviews" VALUES (9,5,3,5,'DD

','2026-10-09 23:23:43');
INSERT INTO "reviews" VALUES (10,2,6,5,'โอเคมาก','2026-10-10 02:54:40');
INSERT INTO "users" VALUES (1,'Admin','admin@matetiww.com','$2y$10$vLgwyF5h3Wvie33h.d/JtuN6pMFwDs03yUxIq/hT.6FOSoWaG0bCO','pic/pro8.jpg','admin');
INSERT INTO "users" VALUES (2,'yaoyao','6720610134@psu.ac.th','$2y$10$vywDlPX5yjVOOf5OkSTN4ObGGWjad4PzOoPJTmelTFn9ve2m8ghmS','pic/pro4.jpg','user');
INSERT INTO "users" VALUES (3,'ซุลซุล','6720610003@psu.ac.th','$2y$10$ppfZWIkLCwHPLJP7DUGI9.eoMGB0jvvlLtKQTAEfAF3UDy/Y34J9W','pic/pro7.jpg','user');
INSERT INTO "users" VALUES (4,'Anas','6720610026@psu.ac.th','$2y$10$6cK4kbDhAUp875kwp5r.8.r3i8a/TMbfxQxyPg0Bx04eByES4lJG6','pic/pro5.jpg','user');
INSERT INTO "users" VALUES (5,'อรัช','6720610032@psu.ac.th','$2y$10$KlvcqroWeta1plYqeMfmUeAqISSQs8ToJ.wfiYuFKDvSHXFF3H3aa','pic/pro3.jpg','user');
INSERT INTO "users" VALUES (6,'บาติน','6720610013@psu.ac.th','$2y$10$gaHaunTKQnQn60mxGmJDZOsI4JHhcQ3gc.uLUQDnNhhuvBZ3q.Pu.','pic/pro10.jpg','user');
INSERT INTO "users" VALUES (7,'ม่อน','6720610015@psu.ac.th','$2y$10$aTH1TBq7u.MUG/7MIwiW0Ov/Ce5POz4g3a10Dq/s1po11tii7OAqK','pic/pro7.jpg','user');
INSERT INTO "users" VALUES (8,'ปังปิ้ง','66666666@psu.ac.th','$2y$10$lDGw5J5JgO0q/DiwP.FnFOpkAYUpbs1cya/lzlw3kOPhC0bOMkNjS','pic/pro6.jpg','user');
COMMIT;

# Supansa Pharma

Supansa Pharma เป็นโปรเจกต์เว็บไซต์สำหรับธุรกิจด้านเภสัชกรรม พัฒนาโดยใช้ PHP และโครงสร้างไฟล์แบบ MVC

## โครงสร้างโปรเจกต์

- `application/controllers/` : โฟลเดอร์สำหรับไฟล์ควบคุมการทำงานของแต่ละหน้า
- `application/controllers/page_types/` : ประเภทของหน้า เช่น global
- `application/themes/mongzen/` : ธีมหลักของเว็บไซต์
  - `default.php`, `full.php`, `home.php`, `page_forbidden.php`, `page_not_found.php`, `page_theme.php`, `view.php` : ไฟล์เทมเพลตสำหรับแต่ละหน้า
  - `assets/` : ไฟล์ static เช่น CSS, JS, รูปภาพ, ฟอนต์
    - `css/` : สไตล์หลักและเฉพาะหน้า
    - `fonts/` : ฟอนต์ที่ใช้ในเว็บ
    - `images/` : รูปภาพต่าง ๆ
    - `js/` : สคริปต์ JavaScript
    - `libs/` : ไลบรารีภายนอก เช่น Bootstrap, Swiper
    - `scss/` : ไฟล์ SCSS สำหรับปรับแต่งสไตล์
    - `svg/` : ไฟล์ SVG และโลโก้
  - `elements/` : ส่วนประกอบของหน้า เช่น header, footer, function

## วิธีการเริ่มต้น

1. Clone โปรเจกต์นี้
2. ตั้งค่าเซิร์ฟเวอร์ PHP ให้ชี้ไปที่โฟลเดอร์โปรเจกต์
3. ตรวจสอบสิทธิ์การเข้าถึงไฟล์และโฟลเดอร์
4. เปิดใช้งานผ่านเบราว์เซอร์

## ข้อมูลเพิ่มเติม

- สามารถปรับแต่งธีมและ assets ได้ที่ `application/themes/mongzen/`
- หากต้องการเพิ่มหน้าใหม่ ให้สร้าง controller และ view ตามโครงสร้าง

## ผู้พัฒนา

Supansa Pharma Team

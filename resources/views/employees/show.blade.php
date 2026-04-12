<!DOCTYPE html>
<html>
<body>
    <h1>รายละเอียดพนักงาน</h1>
    <p><strong>ชื่อ-นามสกุล:</strong> {{ $employee->name }}</p>
    <p><strong>ตำแหน่ง:</strong> {{ $employee->position }}</p>
    <p><strong>อีเมล:</strong> {{ $employee->email }}</p>
    <p><strong>ประวัติย่อ:</strong> {{ $employee->bio }}</p>
    
    <hr>
    <a href="/employees">กลับไปหน้ารายชื่อพนักงาน</a>
</body>
</html>
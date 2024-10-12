CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name NVARCHAR(100) NOT NULL,
    last_name NVARCHAR(100) NOT NULL,
    email NVARCHAR(100) UNIQUE NOT NULL,
    password NVARCHAR(255) NOT NULL,
    phone_number NVARCHAR(10),
    employee_id NVARCHAR(50) UNIQUE NOT NULL,
    office NVARCHAR(100),
    role NVARCHAR(10) DEFAULT 'user',
    profile_image VARCHAR(255) NULL,
    account_created TIME DEFAULT (TIME(CONVERT_TZ(NOW(), '+00:00', '+07:00'))),
    last_password_change DATETIME NULL
);

CREATE TABLE otp_requests (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each OTP request
    email NVARCHAR(100) NOT NULL, -- User's email
    otp NVARCHAR(6) NOT NULL, -- OTP code
    expiration DATETIME NOT NULL, -- OTP expiration time
    used BIT DEFAULT 0 -- Flag indicating if OTP has been used
);

CREATE TABLE office_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    office_name NVARCHAR(MAX) NOT NULL
);

CREATE TABLE bus_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Adding a primary key for uniqueness
    bus NVARCHAR(MAX),                -- Bus column with variable length Unicode text
    route NVARCHAR(MAX),              -- Route column with variable length Unicode text
    origin NVARCHAR(MAX),             -- Origin column with variable length Unicode text
    destination NVARCHAR(MAX)         -- Destination column with variable length Unicode text
);

CREATE TABLE reserve_seat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name NVARCHAR(100) NOT NULL,             -- ชื่อ
    last_name NVARCHAR(100) NOT NULL,              -- นามสกุล
    email NVARCHAR(100) NOT NULL,                  -- อีเมล
    travel_date DATE NOT NULL,                     -- วันที่เดินทาง (เก็บเฉพาะวันเดือนปี)
    travel_time TIME DEFAULT (TIME(CONVERT_TZ(NOW(), '+00:00', '+07:00'))),  -- เวลาเดินทาง (เก็บเวลาในเขตประเทศไทย)
    travel_type NVARCHAR(20) NOT NULL,             -- ประเภทรอบเดินทาง (เที่ยวเดียว หรือ ไป-กลับ)
    outbound_travel NVARCHAR(100) NOT NULL DEFAULT '',  -- เส้นทางขาออก (บันทึกเฉพาะเมื่อเป็น เที่ยวเดียว)
    return_travel NVARCHAR(100) NOT NULL DEFAULT '',   -- เส้นทางขากลับ (บันทึกเฉพาะเมื่อเป็น ไป-กลับ)
    seats NVARCHAR(255) NOT NULL,                  -- ที่นั่งที่จอง (เก็บเป็นตัวเลขที่นั่ง)
    travel_reason NVARCHAR(255) NOT NULL,          -- เหตุผลในการเดินทาง
    phone_number NVARCHAR(10) NOT NULL,            -- เบอร์โทรศัพท์
    office NVARCHAR(100) NOT NULL,                 -- สำนักงาน
    status NVARCHAR(20) NOT NULL DEFAULT 'รอยืนยัน', -- สถานะการจอง
    status_seats NVARCHAR(20) NOT NULL,            -- สถานะที่นั่ง
    reason_cancellation NVARCHAR(MAX),             -- เหตุผลในการยกเลิกการจอง
    is_new BIT NOT NULL DEFAULT 1  
);

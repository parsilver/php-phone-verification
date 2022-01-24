<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\SMS\Providers\NexmoProvider;
use Farzai\PhoneVerification\Verifier;

// การใช้งาน
// เริ่มที่ตั้งต่าการใช้งาน

// ที่เก็บข้อมูลของเรา
$repository = new MyRepository();

// ตัวส่ง SMS
// ในตัวอย่างนี้ เราเลือกใช้ Nexmo เป็นตัวอย่างก่อน
$sms = new NexmoProvider([
    'key'   => 'xxxxxxxx',
    'from'  => 'xxxxxxxx',
]);

// สร้าง Adapter
$adapter = new SmsAdapter($sms, $repository);

// สร้างตัวดำเนินการ
$verifier = new Verifier($adapter);

// ส่ง SMS-OTP
$entity = $verifier->create(
    $phoneNumber = "0988887777"
);

// ผลลัพท์ที่ได้ ท่านสามารถนำมาใช้งานต่อได้
echo $entity->phone_number;
echo $entity->reference;
echo $entity->code;

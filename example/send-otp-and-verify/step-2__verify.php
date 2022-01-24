<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\SMS\Providers\NexmoProvider;
use Farzai\PhoneVerification\Verifier;
use Farzai\PhoneVerification\Exceptions\InvalidVerificationException;
use Farzai\PhoneVerification\Exceptions\VerificationHasExpired;

// รับ Input จากระบบของท่าน
$phoneNumber = "0988887777";
$reference = "xxxxxx";
$code = "xxxxxx";

// ที่เก็บข้อมูลของเรา
$repository = new MyRepository();

// สร้างตัวดำเนินการ
$sms = new NexmoProvider([
    'key'   => 'xxxxxxxx',
    'from'  => 'xxxxxxxx',
]);

$adapter = new SmsAdapter($sms, $repository);
$verifier = new Verifier($adapter);

// ทำการตรวจสอบ
try {
    $entity = $verifier->verify($phoneNumber, $reference, $code);

    // ผลลัพท์ที่ได้ ท่านสามารถนำมาใช้งานต่อได้
    echo $entity->phone_number;
    echo $entity->reference;
    echo $entity->code;

    // ....
} catch (InvalidVerificationException $exception) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวสสอบไม่ถูกต้อง
    echo $exception->getMessage();
} catch (VerificationHasExpired $exception) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวจสอบหมดอายุแล้ว
    echo $exception->getMessage();
}

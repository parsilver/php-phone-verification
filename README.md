## PHP - Phone Verification

เป็นระบบที่ใช้ในการยืนยันผู้ใช้งานผ่าน SMS OTP 

เช่น ใช้ในกรณีที่ระบบของท่านมีระบบสมาชิก และต้องการที่จะยืนยันผู้ใช้งานผ่าน SMS OTP ท่านสามารถใช้ Library ตัวนี้ช่วยท่านได้อีกแรง เพื่อความเป็นระบบเรียบร้อยของระบบของท่าน และสามารถดูแลได้ง่าย


### ตัวอย่างการใช้งานคร่าวๆ

```php
use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\SMS\Providers\NexmoProvider;
use Farzai\PhoneVerification\Verifier;
use Farzai\PhoneVerification\Exceptions\InvalidVerificationException;
use Farzai\PhoneVerification\Exceptions\VerificationHasExpired;

// การใช้งาน
// เริ่มที่ตั้งต่าการใช้งาน
$adapter = new SmsAdapter(new NexmoProvider(), $repository);

// สร้างการยืนยัน และส่ง SMS-OTP ไปยังเบอร์ปลายทาง
$verifier = new Verifier($adapter);
$entity = $verifier->create(
    $phoneNumber = "0988887777"
);

// ผลลัพท์ที่ได้ ท่านสามารถนำมาใช้งานต่อได้
$entity->phone_number;
$entity->reference;
$entity->code;


// ---------------------------
// การยืนยัน OTP
$phoneNumber = "0988887777";
$reference = "xxxxxx";
$code = "xxxxxx";

try {
    $entity = $verifier->verify($phoneNumber, $reference, $code);
    
    // ผลลัพท์ที่ได้ ท่านสามารถนำมาใช้งานต่อได้
    $entity->phone_number;
    $entity->reference;
    $entity->code;
    
    // ....
} catch (InvalidVerificationException) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวสสอบไม่ถูกต้อง
} catch (VerificationHasExpired) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวจสอบหมดอายุแล้ว
}
```

### ความต้องการระบบ
```json
{
  "php": "^7.4|^8.0",
  "ext-json": "*"
}
```


---

## การใช้งาน

### ติดตั้งผ่าน Composer
```
composer require farzai/phone-verification
```

### เริ่มต้นการตั้งค่า

เนื่องจากการใช้งานจะมีการเก็บข้อมูล และเรียกข้อมูลที่ท่านถือไว้จากฐานข้อมูล

ดังนั้น ท่านจึงจำเป็นต้องบอกเราหน่อยว่าท่านเก็บข้อมูลอย่างไร และเรียกอย่างไร โดยการสร้าง Repository และ `implements` Interface เช่นตัวอย่างด้านล่าง
```php
use Farzai\PhoneVerification\Repositories\VerificationRepository;
use Farzai\PhoneVerification\Entities\Verification;

class MyRepository implements VerificationRepository
{
     /**
     * เมื่อระบบต้องการสร้างการยินยัน
     * 
     * @param string $phoneNumber
     * @return Verification
     */
    public function create(string $phoneNumber): Verification
    {
        // บันทึกลงฐานข้อมูลของท่าน ตรงนี้....
        // -----
        
        // จากนั้นสร้าง Entity และคืนค่า Entity ให้เรา
        return new Verification([
            'phone_number' => $phoneNumber,
            'reference' => (string)random_int(10000, 99999),
            'code' => (string)random_int(1000, 9999),
        ]);
    }

    /**
     * เมื่อระบบต้องการค้นหาเพื่อยืนยันว่ารหัสที่ส่งมาถูกหรือไม่?
     * 
     * @param string $phoneNumber
     * @param string $reference
     * @param string $code
     * @return Verification|null
     */
    public function findForValidate(string $phoneNumber, string $reference, string $code): ?Verification
    {
        // ค้นหาจากฐานข้อมูลของท่าน
        // ....
        
        // ถ้าไม่พบ ให้ return null ให้เรา
        // ....
        // return null;
        
        // ถ้าค้นพบให้ส่ง Entity กลับมาหาเรา
        return new Verification([
            'phone_number' => $phoneNumber,
            'reference' => $reference,
            'code' => $code,
            
            // (optional)
            // ใส่ parameter: expires_at ถ้าอยากให้เราตรวจสอบว่าหมดอายุหรือยัง
            'expires_at' => new \DateTime('now'),
        ])
    }

    /**
     * เมื่อระบบต้องการทำให้การยืนยันของ record นี้หมดอายุ
     * 
     * @param Verification $verifier
     */
    public function markAsExpired(Verification $verifier): Verification
    {
        // ทำให้ record นั้น "หมดอายุ"

        return $verifier;
    }

    /**
     * เมื่อระบบต้องการทำให้การยืนยันของ record นี้ถูกยืนยันไปแล้ว
     * 
     * @param Verification $verifier
     */
    public function markAsVerified(Verification $verifier): Verification
    {
        // ทำให้ record นั้น "ยืนยันแล้ว"

        return $verifier;
    }
}
```



### ขั้นตอนการใช้งาน
### 1. ส่ง SMS-OTP
```php
use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\SMS\Providers\NexmoProvider;
use Farzai\PhoneVerification\Verifier;
use Farzai\PhoneVerification\Exceptions\InvalidVerificationException;
use Farzai\PhoneVerification\Exceptions\VerificationHasExpired;

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
$entity->phone_number;
$entity->reference;
$entity->code;
```


### 2. ยืนยันและตรวจสอบ SMS-OTP
```php
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
    $entity->phone_number;
    $entity->reference;
    $entity->code;
    
    // ....
} catch (InvalidVerificationException) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวสสอบไม่ถูกต้อง
} catch (VerificationHasExpired) {
    // เกิดข้อผิดพลาด กรณีรหัสตรวจสอบหมดอายุแล้ว
}
```

## ตัวอย่าง
เราได้แนบไฟล์ตัวอย่างไว้ที่นี่เพื่อเป็นตัวอย่างประกอบ [ไฟล์ตัวอย่าง](example/README.md)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
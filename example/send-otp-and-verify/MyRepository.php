<?php

use Farzai\PhoneVerification\Repositories\VerificationRepository;
use Farzai\PhoneVerification\Entities\Verification;
use PDO;
use DateTime;

class MyRepository implements VerificationRepository
{
    /**
     * @var \PDO
     */
    private PDO $connection;

    public function __construct()
    {
        $host = "localhost";
        $databaseName = "stock";
        $username = "root";
        $password = "mypassword";

        $this->connection = new PDO("mysql:host={$host};dbname={$databaseName}",$username,$password);
    }

    /**
     * @param string $phoneNumber
     * @return Verification
     */
    public function create(string $phoneNumber): Verification
    {
        $sql = "INSERT INTO verifications 
            (phone_number, reference, code) 
            VALUES (:phone_number, :reference, :code) 
            RETURNING id";

        $reference = (string)rand(10000, 99999);
        $code = (string)rand(1000, 9999);

        $statement = $this->prepare($sql);
        $statement->bindParam(":phone_number", $phoneNumber, PDO::PARAM_STR);
        $statement->bindParam(":reference", $reference, PDO::PARAM_STR);
        $statement->bindParam(":code", $code, PDO::PARAM_STR);

        $statement->execute();

        return new Verification([
            'reference' => $reference,
            'code' => $code,
            'phone_number' => $phoneNumber,
        ]);
    }

    public function findForValidate(string $phoneNumber, string $reference, string $code): ?Verification
    {
        $sql = "
            SELECT * 
            FROM verifications 
            WHERE phone_number = '{$phoneNumber}'
              AND reference = '{$reference}' 
              AND code = '{$code}'
            LIMIT 1";

        foreach ($this->connection->query($sql) as $row) {
            return new Verification([
                'reference' => $row['reference'],
                'code' => $row['code'],
                'phone_number' => $row['phone_number'],
            ]);
        }

        return null;
    }

    /**
     * @param Verification $verifier
     * @return Verification
     */
    public function markAsExpired(Verification $verifier): Verification
    {
        $sql = "UPDATE verifications
                SET expires_at = :expires_at
                WHERE phone_number = :phone_number
                AND reference = :reference 
                AND code = :code
                LIMIT 1";

        $statement = $this->prepare($sql);

        $expiresAt = new DateTime('now');

        $statement->bindParam(":phone_number", $verifier->phone_number, PDO::PARAM_STR);
        $statement->bindParam(":reference", $verifier->reference, PDO::PARAM_STR);
        $statement->bindParam(":code", $verifier->code, PDO::PARAM_STR);
        $statement->bindParam(":expires_at", $expiresAt->format("Y-m-d H:i:s"), PDO::PARAM_STR);

        $statement->execute();

        $verifier->expires_at = $expiresAt;

        return $verifier;
    }

    /**
     * @param Verification $verifier
     * @return Verification
     */
    public function markAsVerified(Verification $verifier): Verification
    {
        $sql = "UPDATE verifications
                SET expires_at = :verified_at
                WHERE phone_number = :phone_number
                AND reference = :reference 
                AND code = :code
                LIMIT 1";

        $statement = $this->prepare($sql);

        $expiresAt = new DateTime('now');

        $statement->bindParam(":phone_number", $verifier->phone_number, PDO::PARAM_STR);
        $statement->bindParam(":reference", $verifier->reference, PDO::PARAM_STR);
        $statement->bindParam(":code", $verifier->code, PDO::PARAM_STR);
        $statement->bindParam(":verified_at", $expiresAt->format("Y-m-d H:i:s"), PDO::PARAM_STR);

        $statement->execute();

        $verifier->verified_at = $expiresAt;

        return $verifier;
    }
}
<?php
// tests/integration/DatabaseTest.php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'test_db';
        $user = getenv('DB_USER') ?: 'test_user';
        $pass = getenv('DB_PASS') ?: 'test_pass';

        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$name;charset=utf8mb4",
            $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /** @test */
    public function database_connection_succeeds(): void
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    /** @test */
    public function customers_table_exists(): void
    {
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'customers'");
        $this->assertNotEmpty($stmt->fetchAll());
    }

    /** @test */
    public function can_retrieve_all_customers(): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, created_at FROM customers ORDER BY id ASC'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertGreaterThan(0, count($rows));
        $this->assertArrayHasKey('email', $rows[0]);
    }

    /** @test */
    public function prepared_statement_prevents_sql_injection(): void
    {
        $malicious = "' OR '1'='1";
        $stmt = $this->pdo->prepare(
            "SELECT * FROM customers WHERE name = :name"
        );
        $stmt->execute([':name' => $malicious]);
        $result = $stmt->fetchAll();
        $this->assertEmpty($result); // Injection should return no rows
    }
}

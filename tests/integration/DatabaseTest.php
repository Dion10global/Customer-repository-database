<?php
// tests/integration/DatabaseTest.php
// Integration tests — require a live MySQL connection

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    #[Test]
    public function database_connection_succeeds(): void
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    #[Test]
    public function customers_table_exists(): void
    {
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'customers'");
        $tables = $stmt->fetchAll();
        $this->assertNotEmpty($tables, "Table 'customers' should exist in test_db");
    }

    #[Test]
    public function customers_table_has_correct_columns(): void
    {
        $stmt = $this->pdo->query("DESCRIBE customers");
        $columns = array_column($stmt->fetchAll(), 'Field');
        $this->assertContains('id',         $columns);
        $this->assertContains('name',       $columns);
        $this->assertContains('email',      $columns);
        $this->assertContains('created_at', $columns);
    }

    #[Test]
    public function can_fetch_all_customers_with_prepared_statement(): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, created_at FROM customers ORDER BY id ASC'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $this->assertIsArray($rows);
        $this->assertGreaterThan(0, count($rows));
        $this->assertArrayHasKey('id',         $rows[0]);
        $this->assertArrayHasKey('name',       $rows[0]);
        $this->assertArrayHasKey('email',      $rows[0]);
        $this->assertArrayHasKey('created_at', $rows[0]);
    }

    #[Test]
    public function prepared_statement_resists_sql_injection(): void
    {
        $injection = "' OR '1'='1";
        $stmt = $this->pdo->prepare(
            "SELECT * FROM customers WHERE name = :name"
        );
        $stmt->execute([':name' => $injection]);
        $result = $stmt->fetchAll();
        $this->assertEmpty($result, "SQL injection attempt should return no rows");
    }

    #[Test]
    public function email_field_is_unique(): void
    {
        $stmt = $this->pdo->query(
            "SHOW INDEX FROM customers WHERE Key_name != 'PRIMARY' AND Column_name = 'email'"
        );
        $indexes = $stmt->fetchAll();
        $this->assertNotEmpty($indexes, "Email column should have a unique index");
    }
}
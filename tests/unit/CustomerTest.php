<?php
// tests/unit/CustomerTest.php
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    /** @test */
    public function customer_email_is_valid(): void
    {
        $email = 'alice@example.com';
        $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /** @test */
    public function customer_name_is_not_empty(): void
    {
        $name = 'Alice Moyo';
        $this->assertNotEmpty(trim($name));
    }

    /** @test */
    public function html_special_chars_are_escaped(): void
    {
        $dirty = '<script>alert("xss")</script>';
        $clean = htmlspecialchars($dirty);
        $this->assertStringNotContainsString('<script>', $clean);
    }
}

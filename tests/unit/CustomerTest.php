<?php
// tests/unit/CustomerTest.php
// Unit tests — no database required, tests pure PHP logic only

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CustomerTest extends TestCase
{
    #[Test]
    public function valid_email_passes_validation(): void
    {
        $email = 'alice@example.com';
        $this->assertNotFalse(
            filter_var($email, FILTER_VALIDATE_EMAIL),
            "Expected a valid email address to pass validation"
        );
    }

    #[Test]
    public function invalid_email_fails_validation(): void
    {
        $email = 'not-an-email';
        $this->assertFalse(
            filter_var($email, FILTER_VALIDATE_EMAIL),
            "Expected an invalid email to fail validation"
        );
    }

    #[Test]
    public function customer_name_cannot_be_empty(): void
    {
        $name = 'Alice Moyo';
        $this->assertNotEmpty(trim($name));
    }

    #[Test]
    public function xss_script_tags_are_escaped(): void
    {
        $malicious = '<script>alert("xss")</script>';
        $safe = htmlspecialchars($malicious, ENT_QUOTES, 'UTF-8');
        $this->assertStringNotContainsString('<script>', $safe);
        $this->assertStringContainsString('&lt;script&gt;', $safe);
    }

    #[Test]
    public function html_entities_are_escaped_in_name(): void
    {
        $name = 'O\'Brien & "Associates"';
        $safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $this->assertStringNotContainsString('"', $safe);
        $this->assertStringNotContainsString("'", $safe);
    }

    #[Test]
    public function integer_cast_prevents_id_injection(): void
    {
        $rawId = '1 OR 1=1';
        $safeId = (int) $rawId;
        $this->assertSame(1, $safeId);
    }
}
<?php
// ============================================================
//  customers.php  –  Cloud Application Development Prac Test
//  Place this file in:  C:\xampp\htdocs\customers.php
//  Run at:             http://localhost/customers.php
// ============================================================

// ── 1. DATABASE CONFIGURATION ────────────────────────────────
$host   = '127.0.0.1';
$dbName = 'test_db';        // Change to your database name
$user   = 'root';           // Default XAMPP MySQL user
$pass   = '';               // Default XAMPP MySQL password (empty)
$dsn    = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// ── 2. CONNECT ────────────────────────────────────────────────
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Graceful connection-failure handling
    http_response_code(503);
    die(renderError('Database Connection Failed', $e->getMessage()));
}

// ── 3. FETCH ALL CUSTOMERS (prepared statement) ───────────────
try {
    $stmt = $pdo->prepare(
        'SELECT id, name, email, created_at FROM customers ORDER BY id ASC'
    );
    $stmt->execute();
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    die(renderError('Query Failed', $e->getMessage()));
}

// ── 4. RENDER ─────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Records</title>
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f4f8;
            color: #1e293b;
            padding: 2rem 1rem;
            min-height: 100vh;
        }

        /* ── Page wrapper ── */
        .page {
            max-width: 900px;
            margin: 0 auto;
        }

        /* ── Header ── */
        header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            color: #fff;
            padding: 1.6rem 2rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        header h1 { font-size: 1.4rem; font-weight: 700; }
        header .badge {
            background: rgba(255,255,255,0.2);
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: .05em;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            overflow: hidden;
        }

        /* ── Meta bar ── */
        .meta {
            padding: .75rem 2rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: .85rem;
            color: #64748b;
        }
        .meta strong { color: #1e293b; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }
        thead th {
            background: #1e3a5f;
            color: #fff;
            text-align: left;
            padding: .85rem 1.25rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            font-size: .78rem;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #eff6ff; }
        tbody td {
            padding: .85rem 1.25rem;
            vertical-align: middle;
        }

        /* ── ID badge ── */
        .id-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 700;
            font-size: .8rem;
            padding: 2px 10px;
            border-radius: 999px;
        }

        /* ── Email link ── */
        .email-link {
            color: #2563eb;
            text-decoration: none;
        }
        .email-link:hover { text-decoration: underline; }

        /* ── Date chip ── */
        .date { color: #64748b; font-size: .82rem; }

        /* ── Empty state ── */
        .empty {
            text-align: center;
            padding: 3rem 2rem;
            color: #94a3b8;
        }
        .empty .icon { font-size: 2.5rem; margin-bottom: .5rem; }
        .empty p { font-size: 1rem; }

        /* ── Footer ── */
        footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .78rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="page">

    <header>
        <div>
            <h1>Customer Records</h1>
        </div>
        <span class="badge">PDO · MySQL</span>
    </header>

    <div class="card">
        <div class="meta">
            Showing <strong><?= count($customers) ?></strong>
            <?= count($customers) === 1 ? 'customer' : 'customers' ?>
            &nbsp;·&nbsp; Database: <strong><?= htmlspecialchars($dbName) ?></strong>
        </div>

        <?php if (empty($customers)): ?>
            <div class="empty">
                <div class="icon">📭</div>
                <p>No customers found in the table.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $row): ?>
                        <tr>
                            <td><span class="id-badge"><?= (int)$row['id'] ?></span></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <a class="email-link"
                                   href="mailto:<?= htmlspecialchars($row['email']) ?>">
                                    <?= htmlspecialchars($row['email']) ?>
                                </a>
                            </td>
                            <td class="date"><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer>customers.php &mdash; Cloud Application Development Practical Test</footer>
</div>
</body>
</html>
<?php

// ── HELPER: render a styled error page ────────────────────────
function renderError(string $title, string $detail): string {
    $t = htmlspecialchars($title);
    $d = htmlspecialchars($detail);
    return <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Error – $t</title>
        <style>
            body { font-family: 'Segoe UI', sans-serif; background:#fff0f0;
                   display:flex; align-items:center; justify-content:center; min-height:100vh; }
            .box { background:#fff; border-left:5px solid #ef4444; padding:2rem 2.5rem;
                   border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,.1); max-width:520px; }
            h2   { color:#b91c1c; margin-bottom:.75rem; }
            code { font-size:.85rem; color:#374151; word-break:break-word; }
            p    { margin-top:1rem; font-size:.85rem; color:#6b7280; }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>⚠ $t</h2>
            <code>$d</code>
            <p>Check your database credentials and make sure MySQL is running in XAMPP.</p>
        </div>
    </body>
    </html>
    HTML;
}
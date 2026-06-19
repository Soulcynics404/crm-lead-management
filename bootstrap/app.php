<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle database connection errors with a friendly page
        $exceptions->renderable(function (QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? null;

            // 2002 = Connection refused, 1045 = Access denied, 1049 = Unknown database
            if (in_array($errorCode, [2002, 1045, 1049])) {
                $messages = [
                    2002 => [
                        'title' => 'Database Connection Refused',
                        'detail' => 'The MySQL/MariaDB service is not running or is not accessible.',
                        'fix' => 'Run: <code>sudo systemctl start mariadb</code> (or <code>mysql</code>), then run <code>./setup.sh</code>',
                    ],
                    1045 => [
                        'title' => 'Database Access Denied',
                        'detail' => 'The database username or password in your .env file is incorrect.',
                        'fix' => 'Check DB_USERNAME and DB_PASSWORD in your .env file, or run <code>./setup.sh</code> to create the user.',
                    ],
                    1049 => [
                        'title' => 'Database Not Found',
                        'detail' => 'The database specified in your .env file does not exist.',
                        'fix' => 'Run: <code>./setup.sh</code> to create the database automatically.',
                    ],
                ];

                $info = $messages[$errorCode];

                $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HK CRM - ' . $info['title'] . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: #1e293b;
            border-radius: 16px;
            padding: 2.5rem;
            border: 1px solid #334155;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        h1 { font-size: 1.5rem; color: #f87171; margin-bottom: 0.5rem; }
        .detail { color: #94a3b8; line-height: 1.6; margin-bottom: 1.5rem; }
        .fix-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .fix-box h3 { color: #38bdf8; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; }
        .fix-box p { color: #cbd5e1; line-height: 1.8; }
        code {
            background: #334155;
            color: #67e8f9;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .footer { text-align: center; color: #64748b; font-size: 0.8rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔌</div>
        <h1>' . $info['title'] . '</h1>
        <p class="detail">' . $info['detail'] . '</p>
        <div class="fix-box">
            <h3>💡 How to fix</h3>
            <p>' . $info['fix'] . '</p>
        </div>
        <div class="fix-box">
            <h3>🔧 Quick setup</h3>
            <p>Run <code>./setup.sh</code> from the project root to automatically check and fix all database issues.</p>
        </div>
        <p class="footer">HK CRM &bull; Refresh this page after fixing the issue</p>
    </div>
</body>
</html>';

                return new Response($html, 503);
            }
        });
    })->create();

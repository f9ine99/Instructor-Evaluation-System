<?php
/**
 * HOPE Evaluation System - Database Connection Factory
 * 
 * Reads credentials from .env file and returns a configured PDO instance.
 * Supports TiDB Cloud with SSL connections.
 */

class Database {
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Load environment variables from .env file
     */
    private static function loadEnv(): void {
        if (!empty(self::$config)) return;

        $envFile = __DIR__ . '/../../.env';
        if (!file_exists($envFile)) {
            throw new RuntimeException(
                '.env file not found. Copy .env.example to .env and configure your database credentials.'
            );
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;

            [$key, $value] = explode('=', $line, 2);
            self::$config[trim($key)] = self::normalizeEnvValue($value);
        }
    }

    /**
     * Normalize .env value (trim whitespace and optional quotes).
     */
    private static function normalizeEnvValue(string $value): string {
        $value = trim($value);
        if (
            (str_starts_with($value, "'") && str_ends_with($value, "'")) ||
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
        ) {
            return substr($value, 1, -1);
        }
        return $value;
    }

    /**
     * Get a configured PDO database connection (singleton)
     */
    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::loadEnv();

        $host = self::$config['DB_HOST'] ?? '127.0.0.1';
        $port = self::$config['DB_PORT'] ?? '3306';
        // Accept both modern and legacy env key variants.
        $name = self::$config['DB_NAME'] ?? self::$config['DB_DATABASE'] ?? 'ievaluation';
        $user = self::$config['DB_USER'] ?? self::$config['DB_USERNAME'] ?? 'root';
        $pass = self::$config['DB_PASS'] ?? self::$config['DB_PASSWORD'] ?? '';
        $ssl  = (self::$config['DB_SSL'] ?? 'false') === 'true';
        $sslCa = self::$config['DB_SSL_CA'] ?? '';
        if (!empty($sslCa) && $sslCa[0] !== '/') {
            $sslCa = realpath(__DIR__ . '/../../' . ltrim($sslCa, '/')) ?: $sslCa;
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        // TiDB Cloud SSL configuration
        if ($ssl && !empty($sslCa)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
            return self::$instance;
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get a config value from .env
     */
    public static function getConfig(string $key, string $default = ''): string {
        self::loadEnv();
        return self::$config[$key] ?? $default;
    }

    /**
     * Close the database connection
     */
    public static function close(): void {
        self::$instance = null;
    }
}

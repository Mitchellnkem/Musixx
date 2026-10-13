<?php

/**
 * Opens the application's MySQL connection using constants from initialize.php.
 */
class DBConnection
{
    /** @var mysqli */
    public $conn;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
            $this->conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            http_response_code(500);
            exit('The application could not connect to its database. Check the server configuration.');
        }
    }

    public function __destruct()
    {
        if ($this->conn instanceof mysqli) {
            $this->conn->close();
        }
    }
}

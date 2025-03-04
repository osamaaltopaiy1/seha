<?php
class Database {
    private $host = "sql202.infinityfree.com";
    private $db_name = "if0_38446522_shhhhhh";
    private $username = "if0_38446522";
    private $password = "W9fWEzb4pR";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }
}
?>

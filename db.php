<?php

    class DB {
        private $host = 'localhost';
        private $username = 'user';
        private $password = 'password';
        private $database = 'database';
        public static $conn;

        public function __construct() {
            $this->populateFromEnv();

            self::$conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            if (self::$conn->connect_error) {
                die('Connection failed: ' . self::$conn->connect_error);
            }
        }

        public static function getConnection() {
            if (!self::$conn) {
                new DB();
            }

            return self::$conn;
        }

        public static function closeConnection() {
            if (self::$conn) {
                self::$conn->close();
            }
        }

        public static function query($sql) {
            $conn = self::getConnection();
            $result = $conn->query($sql);
            return $result;
        }

        private function populateFromEnv() {
            $env = file_get_contents('.env');
            $env = explode("\n", $env);
            
            foreach ($env as $line) {
                $line = explode('=', $line);
                if (count($line) == 2) {
                    $key = $line[0];
                    $value = $line[1];

                    //trim
                    $value = trim($value);
                    
                    if ($key == 'DB_HOST') {
                        $this->host = $value;
                    } else if ($key == 'DB_USERNAME') {
                        $this->username = $value;
                    } else if ($key == 'DB_PASSWORD') {
                        $this->password = $value;
                    } else if ($key == 'DB_DATABASE') {
                        $this->database = $value;
                    }
                }
            }
        }
    }
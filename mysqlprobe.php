<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Connection Probe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: radial-gradient(circle, #000000, #0a0a0a, #1c1c1c);
            overflow: hidden;
            color: #ffffff;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 200%;
            background: linear-gradient(270deg, #ff00ff, #00ffff, #ff6600, #0066ff);
            background-size: 800% 800%;
            animation: wave 12s ease infinite;
            opacity: 0.1;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes wave {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.6), 0 0 60px rgba(255, 0, 255, 0.4);
            width: 350px;
            text-align: center;
            color: #00ffcc;
        }

        .container h1 {
            margin-bottom: 20px;
            font-size: 30px;
            color: #00ffcc;
            text-shadow: 0 0 10px #00ffcc, 0 0 20px #00ffcc, 0 0 40px #00ffcc;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: 1px solid #ff00ff;
            border-radius: 5px;
            background: transparent;
            color: #ffffff;
            font-size: 16px;
            outline: none;
            box-shadow: 0 0 12px rgba(255, 0, 255, 0.6);
            transition: box-shadow 0.4s ease, border-color 0.4s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            box-shadow: 0 0 20px #ff00ff, 0 0 40px #ff00ff;
            border-color: #ff00ff;
        }

        .btn {
            background-color: #ff00ff;
            color: white;
            padding: 12px 25px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 0 15px #ff00ff, 0 0 30px #ff00ff, 0 0 60px #ff00ff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.15);
            box-shadow: 0 0 20px #ff00ff, 0 0 40px #ff00ff, 0 0 80px #ff00ff;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>MySQL Probe</h1>
    <form method="POST">
        <input type="text" name="host" placeholder="MySQL Host" required>
        <input type="text" name="port" placeholder="MySQL Port" required>
        <input type="text" name="username" placeholder="MySQL Username" required>
        <input type="password" name="password" placeholder="MySQL Password">
        <input type="text" name="database" placeholder="Database Name" required>
        <button type="submit" class="btn">Connect</button>
    </form>
</div>

<script>
    function showAlert(message) {
        alert(message);
    }
</script>

<?php
error_reporting(0);
class Mysql {
    public $debug = 1;
    public $database;
    public $hostname;
    public $port;
    public $charset = "utf8";
    public $username;
    public $password;
    public function __construct($database, $hostname, $port, $username, $password) {
        $this->database = $database;
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }
    protected function connect() {
        $dsn = "mysql:host=".$this->hostname.";port=".$this->port.";dbname=".$this->database.";charset=".$this->charset;
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, array(
                PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($this->debug) {
                echo "<script>showAlert('Connection Successful!');</script>";
                $this->query("select flag from flag");

            }
        } catch (PDOException $e) {
            if ($this->debug) {
                echo "<script>showAlert('Connection Failed: " . addslashes($e->getMessage()) . "');</script>";
            }

        }
    }
    public function query($sql) {
        return $this->pdo->query($sql);
    }
    public function __destruct()
    {
        $this->connect();
    }
}
class Backdooor{
    public $cmd;
    public function __toString(){
        exec($this->cmd);
        return '';
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $database = $_POST['database'];
    $port = intval($_POST['port']);
    $mysql = new Mysql($database, $host, $port, $username, $password);
    $mysqlser = serialize($mysql);
    $mysqlser = str_replace("flag", "", $mysqlser);
    unserialize($mysqlser);
}
?>
</body>
</html>

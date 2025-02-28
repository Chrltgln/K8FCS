<?php
class User
{
    private $conn;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    public function sanitize_input($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public function emailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function register($first_name, $middle_name, $last_name, $age, $gender, $dob, $address, $email, $phone, $password, $confirm_password)
    {
        if ($password !== $confirm_password) {
            return "Passwords do not match.";
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'Client';

        $stmt = $this->conn->prepare("INSERT INTO users (first_name, middle_name, last_name, age, gender, dob, address, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssssss", $first_name, $middle_name, $last_name, $age, $gender, $dob, $address, $email, $phone, $hashed_password, $role);

        $execution_result = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        if ($execution_result) {
            return true;
        } else {
            return "Error: " . $error;
        }
    }

    public function login($email, $password)
{
    $sql = "SELECT id, password, first_name, role FROM users WHERE email = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();
    $num_rows = $result->num_rows;

    $stmt->close();

    if ($num_rows > 0) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];

            // Set cookies
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('email', $email, time() + (86400 * 30), "/");
            setcookie('first_name', $user['first_name'], time() + (86400 * 30), "/");
            setcookie('role', $user['role'], time() + (86400 * 30), "/");

            return $user['role'];
        } else {
            return "Invalid email or password.";
        }
    } else {
        return "Invalid email or password.";
    }
}

    public function getUserIdByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user_id = null;
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
        return $user_id;
    }

    public function logout() {
        session_start();
        $session_id = session_id();
        $logout_time = date('Y-m-d H:i:s');
    
        // Update the session record with logout time
        $stmt = $this->conn->prepare("UPDATE sessions SET logout_time = ? WHERE session_id = ? AND logout_time IS NULL");
        $stmt->bind_param("ss", $logout_time, $session_id);
        $stmt->execute();
        $stmt->close();
    
        $_SESSION = array();
    
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    
        // Clear cookies
        setcookie('user_id', '', time() - 3600, "/");
        setcookie('email', '', time() - 3600, "/");
        setcookie('first_name', '', time() - 3600, "/");
        setcookie('role', '', time() - 3600, "/");
    
        session_unset();
        session_destroy();
    }

    public function getUserDetailsById($user_id) {
        $stmt = $this->conn->prepare("SELECT first_name, middle_name, last_name, phone, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_details = $result->fetch_assoc();
        $stmt->close();
        return $user_details;
    }
}
?>
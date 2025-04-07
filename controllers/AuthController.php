<?php
require_once __DIR__ . '/../utils/database.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showRegisterForm() {
        include __DIR__ . '/../views/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($username) || strlen($username) < 3) {
            $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        if ($count > 0) {
            $errors[] = "Le nom d'utilisateur ou l'e-mail est déjà utilisé.";
        }
        $stmt->close();

        if (!empty($errors)) {
            include __DIR__ . '/../views/register.php';
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userType = 'student';
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $passwordHash, $userType);

        if ($stmt->execute()) {
            header('Location: /login?success=Inscription réussie ! Veuillez vous connecter.');
        } else {
            $errors[] = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
            include __DIR__ . '/../views/register.php';
        }
        $stmt->close();
    }

    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /welcome');
            exit;
        }
        include __DIR__ . '/../views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        }
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis.";
        }

        if (!empty($errors)) {
            include __DIR__ . '/../views/login.php';
            return;
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "E-mail ou mot de passe incorrect.";
            include __DIR__ . '/../views/login.php';
            $stmt->close();
            return;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        if (!password_verify($password, $user['password_hash'])) {
            $errors[] = "E-mail ou mot de passe incorrect.";
            include __DIR__ . '/../views/login.php';
            return;
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        // Rediriger vers la page de bienvenue
        header('Location: /welcome');
        exit;
    }

    public function logout() {
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: /login');
        exit;
    }
}
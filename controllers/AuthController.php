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

        // Récupérer l'ID du rang "Fer 3"
        $stmt = $conn->prepare("SELECT rank_id FROM ranks WHERE rank_name = ? AND sub_rank = ?");
        $rankName = 'Fer';
        $subRank = 3;
        $stmt->bind_param("si", $rankName, $subRank);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Erreur : Le rang Fer 3 n'existe pas dans la base de données.";
            include __DIR__ . '/../views/register.php';
            $stmt->close();
            return;
        }
        $rankId = $result->fetch_assoc()['rank_id'];
        $stmt->close();

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userType = 'student';

        // Ajouter rank_id dans l'insertion
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_type, rank_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $email, $passwordHash, $userType, $rankId);

        if ($stmt->execute()) {
            header('Location: /login');
        } else {
            $errors[] = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
            include __DIR__ . '/../views/register.php';
        }
        $stmt->close();
    }

    // Les autres méthodes (showLoginForm, login, logout, etc.) restent inchangées
    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            if($_SESSION["user_type"] == "spectator") {
                header('Location: /spectator/dashboard');
            }
            else if($_SESSION["user_type"] == "student") {
                header('Location: /student/dashboard');
            }
            else {
                header('Location: /');
            }
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

        if($_SESSION["user_type"] == "spectator") {
            header('Location: /spectator/dashboard');
        }
        else if($_SESSION["user_type"] == "student") {
            header('Location: /student/dashboard');
        }
        else {
            header('Location: /');
        }
        exit;
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /');
        exit;
    }

    public function showSpectatorRegisterForm() {
        include __DIR__ . '/../views/spectator/register.php';
    }

    public function registerSpectator() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /spectator/register');
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
            include __DIR__ . '/../views/spectator/register.php';
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userType = 'spectator';
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $passwordHash, $userType);

        if ($stmt->execute()) {
            header('Location: /login');
        } else {
            $errors[] = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
            include __DIR__ . '/../views/spectator/register.php';
        }
        $stmt->close();
    }
}
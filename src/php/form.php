<!--

<form action="auth.php" method="POST">
  <input type="text" name="username" placeholder="Username" required />
  <input type="password" name="password" placeholder="Password" required />

  <input type="hidden" name="action" id="formAction" value="login" />

  <button type="submit">Login</button>
  <button type="button" onclick="switchToRegister()">Register</button>
</form>

<script>
  function switchToRegister() {
    document.getElementById("formAction").value = "register";
    document.querySelector("form").submit();
  }
</script>

-->

<?php
require "db.php"; // Database connection

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"]; // either "login" or "register"
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        die("Please fill in both fields.");
    }

    if ($action === "login") {
        // LOGIN LOGIC
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["username"] = $username;
            header("Location: home.html");
            exit;
        } else {
            echo "Invalid username or password.";
        }
    } elseif ($action === "register") {
        // REGISTRATION LOGIC
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->fetch()) {
            echo "Username already taken.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $insert->bindParam(":username", $username);
            $insert->bindParam(":password", $hashedPassword);
            if ($insert->execute()) {
                $_SESSION["username"] = $username;
                header("Location: home.html");
                exit;
            } else {
                echo "Registration failed. Please try again.";
            }
        }
    } else {
        echo "Invalid action.";
    }
}
?>
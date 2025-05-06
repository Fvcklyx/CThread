<?php
include "../service/db.php";
session_start();

$login_message = "";
$usernameErr = $passwordErr = "";

if (isset($_SESSION["is_login"])) {
    header("location: ../Dashboard/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = data_input($_POST["username"]);
    $password = data_input($_POST["password"]);

    if (empty($username)) {
        $usernameErr = "Username kosong!";
    }

    if (empty($password)) {
        $passwordErr = "Password kosong!";
    }

    if (empty($usernameErr) && empty($passwordErr)) {
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                $_SESSION["username"] = $user["username"];
                $_SESSION["id"] = $user["id"];
                $_SESSION["is_login"] = true;

                // Redirect admin
                if ($username === 'admin' && $password === 'admin123') {
                    header("location: ../Admin/admin.php");
                } else {
                    header("location: ../Dashboard/dashboard.php");
                }

                exit();
            } else {
                $login_message = "Password salah!";
            }
        } else {
            $login_message = "Username tidak ditemukan!";
        }
    }
}

function data_input($data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="../Assets/cthreadbgr.png" />
    <title>Masuk</title>
  </head>
  <body>
    <nav>
        <div class="logo">
            <a href="../index.php">
                <img src="../Assets/cthreadbgr.png" alt="C-Thread">
            </a>
        </div>
        <div class="visNav" id="visible">
            Masuk
        </div>
        <ul class="listLink" id="hide">
            <li class="link"><a href="../Profile/profil.php">Profil</a></li>
            <li class="link"><a href="#">Masuk</a></li>
            <li class="link"><a href="../SignIn/signin.php">Daftar</a></li>
        </ul>
        <button class="profil" id="barsButt">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="menu size-6">
                <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
            </svg>
        </button>
    </nav>
    <section class="container">
      <h1>Masuk Sebagai Pengguna</h1>
      <div class="card">
        <form class="content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
          <p>
            Username <span class="error">* <?php echo $usernameErr;?></span>
            <input
              type="text"
              name="username"
              placeholder="masukkan username (max 10 huruf)"
              id="usn"
            />
          </p>
          <p>
            Password <span class="error">* <?php echo $passwordErr;?></span>
            <input
              type="password"
              name="password"
              placeholder="masukkan password (max 8 huruf)"
              id="pw1"
            />
          </p>
          <p>
            <span class="error"><?php echo $login_message?></span>
          </p>
          <button id="logButton" class="login" type="submit" name="login">Masuk</button>
        </form>
      </div>
    </section>
    <footer class="copyright">
        Copyright © 2025 C-Thread. All Rights Reserved.
    </footer>
    <script src="../script.js"></script>
  </body>
</html>
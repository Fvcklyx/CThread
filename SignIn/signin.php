<?php
include "../service/db.php";
session_start();

$regist_message = "";
$usernameErr = $passwordErr = "";

if (isset($_SESSION["is_login"])) {
    header("location: ../Dashboard/Dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["regist"])) {
    $username = data_input($_POST["username"]);
    $password1 = data_input($_POST["password"]);
    $password2 = data_input($_POST["verif_password"]);

    if (empty($username)) {
        $usernameErr = "Username kosong!";
    }
    if (empty($password1)) {
        $passwordErr = "Password kosong!";
    } elseif ($password1 !== $password2) {
        $passwordErr = "Password tidak sama!";
    } elseif (strlen($username) > 10 || strlen($password1) > 8) {
        $regist_message = "Username maks 10 huruf, Password maks 8 huruf!";
    }

    if (empty($usernameErr) && empty($passwordErr)) {
        // Cek duplikat username
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $regist_message = "Username telah digunakan!";
        } else {
            $hashed_pw = password_hash($password1, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_pw);

            if ($stmt->execute()) {
                $user_id = $db->insert_id;
                $empty = ""; // default kosong
                $stmt = $db->prepare("INSERT INTO profile (id, username, name, gender, job, email, website, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssss", $user_id, $username, $empty, $empty, $empty, $empty, $empty, $empty);
                $stmt->execute();
                header("location: ../LogIn/login.php");
                exit();
            } else {
                $regist_message = "Gagal menyimpan data!";
            }
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
    <title>Daftar</title>
  </head>
  <body>
    <nav>
        <div class="logo">
            <a href="../index.php">
                <img src="../Assets/cthreadbgr.png" alt="C-Thread">
            </a>
        </div>
        <div class="visNav" id="visible">
            Daftar
        </div>
        <ul class="listLink" id="hide">
            <li class="link"><a href="../Profile/profil.php">Profil</a></li>
            <li class="link"><a href="../LogIn/login.php">Masuk</a></li>
            <li class="link"><a href="#">Daftar</a></li>
        </ul>
        <button class="profil" id="barsButt">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="menu size-6">
                <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
            </svg>
        </button>
    </nav>
    <section class="container">
      <h1>Daftar Sebagai Pengguna</h1>
      <div class="card">
      <form class="content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" onsubmit="return validateForm()">
  <p>
    Username <span class="error">* <?php echo $usernameErr;?></span>
    <input type="text" name="username" id="usn" placeholder="masukkan username (max 10 huruf)" maxlength="10" />
  </p>
  <p>
    Password <span class="error">* <?php echo $passwordErr;?></span>
    <input type="password" name="password" id="pw1" placeholder="masukkan password (max 8 huruf)" maxlength="8" />
  </p>
  <p>
    Verifikasi Password <span class="error">* <?php echo $passwordErr;?></span>
    <input type="password" name="verif_password" id="pw2" placeholder="verifikasi password" maxlength="8" />
  </p>
  <button class="regist" type="submit" name="regist">Daftar</button>
</form>
      </div>
    </section>
    <footer class="copyright">
        Copyright © 2025 C-Thread. All Rights Reserved.
    </footer>
    <script src="../script.js"></script>
  </body>
</html>

<?php
include "../service/db.php";
session_start();

$edit_message = "";

if (!isset($_SESSION["is_login"])) {
  header("location: ../index.php");
  exit();
}

$user_id = $_SESSION["id"];
$username = $_SESSION["username"];
$name = $gender = $job = $email = $website = $bio = "";

$query = "SELECT * FROM profile WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
  $data = $result->fetch_assoc();
  $name = $data['name'];
  $gender = $data['gender'];
  $job = $data['job'];
  $email = $data['email'];
  $website = $data['website'];
  $bio = $data['bio'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save"])) {
  $name = data_input($_POST["name"]);
  $gender = data_input($_POST["gender"]);
  $job = data_input($_POST["job"]);
  $email = data_input($_POST["email"]);
  $website = data_input($_POST["website"]);
  $bio = data_input($_POST["bio"]);

  // Cek apakah data sudah ada
  $check = $db->prepare("SELECT 1 FROM profile WHERE id = ?");
  $check->bind_param("i", $user_id);
  $check->execute();
  $check_result = $check->get_result();

  if ($check_result->num_rows > 0) {
    // Update
    $stmt = $db->prepare("UPDATE profile SET name=?, gender=?, job=?, email=?, website=?, bio=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $gender, $job, $email, $website, $bio, $user_id);
  } else {
    // Insert
    $stmt = $db->prepare("INSERT INTO profile (id, name, gender, job, email, website, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $name, $gender, $job, $email, $website, $bio);
  }

  if ($stmt->execute()) {
    header("Location: ../Profile/profil.php");
    exit();
  } else {
    $edit_message = "Gagal menyimpan data. Coba lagi.";
  }
}

function data_input($data): string
{
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
  <link rel="stylesheet" href="./style.css" />
  <link rel="icon" href="../Assets/cthreadbgr.png" />
  <title>Edit</title>
</head>

<body>
  <nav>
    <div class="logo">
      <a href="../Dashboard/dashboard.php">
        <img src="../Assets/cthreadbgr.png" alt="C-Thread">
      </a>
    </div>
    <div class="visNav" id="visible">
      <?= $_SESSION["username"] ?>
    </div>
    <ul class="listLink" id="hide">
      <li class="link"><a href="./profil.php">Profil</a></li>
      <li class="link"><a href="../LogIn/login.php">Masuk</a></li>
      <li class="link"><a href="../SignIn/signin.php">Daftar</a></li>
    </ul>
    <button class="profil" id="barsButt">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="menu size-6">
        <path fill-rule="evenodd"
          d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
          clip-rule="evenodd" />
      </svg>
    </button>
  </nav>
  <section class="container">
    <h1>Silahkan Isi Profil, <?= $_SESSION["username"] ?>!</h1>
    <div class="card">
      <form class="content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <p>
          Nama
          <input type="text" name="name" placeholder="masukkan nama" value="<?= htmlspecialchars($name) ?>" />
        </p>
        <p>
          Gender
          <select name="gender">
            <option disabled selected>Pilih Gender</option>
            <option value="Laki-Laki" <?= $gender == "Laki-Laki" ? "selected" : "" ?>>Laki-Laki</option>
            <option value="Perempuan" <?= $gender == "Perempuan" ? "selected" : "" ?>>Perempuan</option>
          </select>
        </p>
        <p>
          Pekerjaan
          <input type="text" name="job" placeholder="masukkan pekerjaan" value="<?= htmlspecialchars(string: $job) ?>" />
        </p>
        <p>
          Email
          <input type="email" name="email" placeholder="masukkan email"
            value="<?= htmlspecialchars(string: $email) ?>" />
        </p>
        <p>
          Website
          <input type="url" name="website" placeholder="masukkan url link" value="<?= htmlspecialchars($website) ?>" />
        </p>
        <p>
          Bio
          <textarea name="bio" rows="7" cols="40"><?= htmlspecialchars($bio) ?></textarea>
        </p>
        <button id="saveButton" class="save" type="submit" name="save">Simpan</button>
        <button id="backButton" class="back" type="button" name="back" onclick="window.history.back()">Kembali</button>
      </form>
    </div>
  </section>
  <footer class="copyright">
    Copyright © 2025 C-Thread. All Rights Reserved.
  </footer>
  <script src="../script.js"></script>
</body>

</html>
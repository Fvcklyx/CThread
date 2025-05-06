<?php
include "../service/db.php";
session_start();

$username = $_SESSION["username"];

if (isset($_SESSION["is_login"])) {
  if ($username !== 'admin' && $password !== 'admin123') {
    header("location: ../Dashboard/dashboard.php");
    exit();
  }
}

if (!isset($_SESSION["is_login"])) {
  header("location: ../index.php");
  exit();
}

if (isset($_POST["logout"])) {
  session_unset();
  session_destroy();
  header("location: ../index.php");
  exit();
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$result = mysqli_query($db, "SELECT * FROM profile ORDER BY id DESC LIMIT $start, $limit");
$profiles = mysqli_fetch_all($result, MYSQLI_ASSOC);
$countResult = mysqli_query($db, "SELECT COUNT(*) AS total FROM profile");
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Tambah profile
if (isset($_POST["submitProfile"])) {
  $id = intval($_POST["id"]);
  $name = trim($_POST["name"]);
  $gender = trim($_POST["gender"]);
  $job = trim($_POST["job"]);
  $email = trim($_POST["email"]);
  $website = trim($_POST["website"]);
  $bio = trim($_POST["bio"]);
  $created_at = date("Y-m-d H:i:s");

  $stmt = $db->prepare("INSERT INTO profile (id, name, gender, job, email, website, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssss", $id, $name, $gender, $job, $email, $website, $bio, $created_at);
  $stmt->execute();
  $stmt->close();

  header("Location: profile.php");
  exit();
}

$id = $name = $gender = $job = $email = $website = $bio = "";

// Edit profile
if (isset($_POST["editProfile"])) {
  $id = intval($_POST["id"]);
  $name = trim($_POST["name"]);
  $gender = trim($_POST["gender"]);
  $job = trim($_POST["job"]);
  $email = trim($_POST["email"]);
  $website = trim($_POST["website"]);
  $bio = trim($_POST["bio"]);

  $stmt = $db->prepare("UPDATE profile SET name=?, gender=?, job=?, email=?, website=?, bio=? WHERE id=?");
  $stmt->bind_param("sssssssi", $name, $gender, $job, $email, $website, $bio, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: profile.php");
  exit();
}

// Hapus profile
if (isset($_POST["deleteProfile"])) {
  $id = intval($_POST["id"]);
  $stmt = $db->prepare("DELETE FROM profile WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: profile.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin: Profile</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="../Assets/cthreadbgr.png" />
</head>

<body>
  <nav>
    <div class="logo">
      <a href="#"><img src="../Assets/cthreadbgr.png" alt="C-Thread" /></a>
    </div>
    <div class="visNav" id="visible">Profil</div>
    <ul class="listLink" id="hide">
      <li class="link"><a href="admin.php">Admin</a></li>
      <form method="POST" class="link" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit" name="logout" class="outButton">Keluar</button>
      </form>
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
    <div class="add">
      <div class="line"></div>
      <button id="newProfile" class="new" type="button">Baru</button>
      <div class="line"></div>
    </div>
    <table>
      <tr class="head">
        <th>ID</th>
        <th>Nama</th>
        <th>Gender</th>
        <th>Pekerjaan</th>
        <th>Email</th>
        <th>Website</th>
        <th>Bio</th>
        <th>Dibuat Pada</th>
        <th>Kelola</th>
      </tr>
      <?php foreach ($profiles as $p): ?>
        <tr class="data">
          <td><?= htmlspecialchars($p['id']); ?></td>
          <td><?= htmlspecialchars(substr($p['name'], 0, 10)); ?>...</td>
          <td><?= htmlspecialchars($p['gender']) ?></td>
          <td><?= htmlspecialchars($p['job']) ?></td>
          <td><?= htmlspecialchars(substr($p['email'], 0, 10)); ?>...</td>
          <td><?= htmlspecialchars(substr($p['website'], 0, 10)); ?>...</td>
          <td><?= htmlspecialchars(substr($p['bio'], 0, 10)); ?>...</td>
          <td><?= htmlspecialchars($p['created_at']); ?></td>
          <td>
            <button type="button" class="editProfile" onclick='openEditModal(<?= json_encode($p) ?>)'>Edit</button>
            <form method="POST" style="display:inline;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" name="deleteProfile" class="deleteUsers"
                onclick="return confirm('Hapus Profil Ini?')">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php if ($totalPages > 1): ?>
      <div class="pagination" style="text-align: center;">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1; ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        <?php
        $adjacents = 2;
        $start = ($page - $adjacents > 1) ? $page - $adjacents : 1;
        $end = ($page + $adjacents < $totalPages) ? $page + $adjacents : $totalPages;
        ?>
        <?php if ($start > 1): ?>
          <a href="?page=1">1</a>
          <?php if ($start > 2): ?> ... <?php endif; ?>
        <?php endif; ?>
        <?php for ($i = $start; $i <= $end; $i++): ?>
          <?php if ($i == $page): ?>
            <strong><?= $i; ?></strong>
          <?php else: ?>
            <a href="?page=<?= $i; ?>"><?= $i; ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($end < $totalPages): ?>
          <?php if ($end < $totalPages - 1): ?> ... <?php endif; ?>
          <a href="?page=<?= $totalPages; ?>"><?= $totalPages; ?></a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1; ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>
  <footer class="copyright">
    Copyright © 2025 C-Thread. All Rights Reserved.
  </footer>
</body>
<!-- Modal Tambah -->
<div class="modal hidden" id="modalProfileNew">
  <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <p>
      ID
      <input type="text" name="id" placeholder="masukkan id" />
    </p>
    <p>
      Nama
      <input type="text" name="name" placeholder="masukkan nama" />
    </p>
    <p>
      Gender
      <select name="gender">
        <option disabled selected>Pilih Gender</option>
        <option value="Laki-Laki">Laki-Laki</option>
        <option value="Perempuan">Perempuan</option>
      </select>
    </p>
    <p>
      Pekerjaan
      <input type="text" name="job" placeholder="masukkan pekerjaan" />
    </p>
    <p>
      Email
      <input type="email" name="email" placeholder="masukkan email" />
    </p>
    <p>
      Website
      <input type="url" name="website" placeholder="masukkan url link" />
    </p>
    <p>
      Bio
      <textarea name="bio" rows="3" cols="40"></textarea>
    </p>
    <button class="submitModal" type="submit" name="submitProfile">Tambahkan</button>
    <button class="backModal" type="button" name="back">Batal</button>
  </form>
</div>
<!-- Modal Edit -->
<div class="modal hidden" id="modalProfileEdit">
  <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <p>
      Nama
      <input type="text" name="name" placeholder="masukkan nama" id="edit-name" />
    </p>
    <p>
      Gender
      <select name="gender" id="edit-gender">
        <option disabled selected>Pilih Gender</option>
        <option value="Laki-Laki">Laki-Laki</option>
        <option value="Perempuan">Perempuan</option>
      </select>
    </p>
    <p>
      Pekerjaan
      <input type="text" name="job" placeholder="masukkan pekerjaan" id="edit-job" />
    </p>
    <p>
      Email
      <input type="email" name="email" placeholder="masukkan email" id="edit-email" />
    </p>
    <p>
      Website
      <input type="url" name="website" placeholder="masukkan url link" id="edit-website" />
    </p>
    <p>
      Bio
      <textarea name="bio" rows="3" cols="40" id="edit-bio"></textarea>
    </p>
    <button class="submitModal" type="submit" name="editProfile">Simpan</button>
    <button class="backModal" type="button" name="back">Batal</button>
  </form>
</div>
<script>
  document.getElementById("barsButt").addEventListener("click", () => {
    const visible = document.getElementById("visible");
    const hide = document.getElementById("hide");
    visible.style.display = visible.style.display !== "none" ? "none" : "flex";
    hide.style.display = hide.style.display === "flex" ? "none" : "flex";
  });
  document.getElementById("hide").style.display = "none";

  document.addEventListener("DOMContentLoaded", () => {
    const modalNew = document.getElementById("modalProfileNew");
    const modalEdit = document.getElementById("modalProfileEdit");

    document.getElementById("newProfile").onclick = () => {
      modalNew.classList.remove("hidden");
    };
    modalNew.querySelector(".backModal").onclick = () => {
      modalNew.classList.add("hidden");
    };
    modalEdit.querySelector(".backModal").onclick = () => {
      modalEdit.classList.add("hidden");
    };

    window.openEditModal = (data) => {
      modalEdit.classList.remove("hidden");
      document.getElementById("edit-name").value = data.name;
      document.getElementById("edit-gender").value = data.gender;
      document.getElementById("edit-job").value = data.job;
      document.getElementById("edit-email").value = data.email;
      document.getElementById("edit-website").value = data.website;
      document.getElementById("edit-bio").value = data.bio;
    };
  });
</script>

</html>
<?php
include "../service/db.php";
session_start();

$username = $_SESSION["username"];
$id = $_SESSION["id"];

if (isset($_SESSION["is_login"])) {
  if ($username !== 'admin') {
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

$result = mysqli_query($db, "SELECT * FROM threads ORDER BY id_order DESC LIMIT $start, $limit");
$threads = mysqli_fetch_all($result, MYSQLI_ASSOC);
$countResult = mysqli_query($db, "SELECT COUNT(*) AS total FROM threads");
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Tambah thread
if (isset($_POST["submitThread"])) {
  $id = intval($_POST["id"]);
  $title = trim($_POST["title"]);
  $core = trim($_POST["core"]);
  $status = $_POST["showT"];

  if (!empty($id) && !empty($username) && !empty($title) && !empty($core)) {
    $stmt = $db->prepare("INSERT INTO threads (id, title, core, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $id, $title, $core, $status);
    $stmt->execute();
    $stmt->close();
  }
  header("Location: threads.php");
  exit();
}

// Edit thread
if (isset($_POST["editThread"])) {
  $id_order = intval($_POST["id_order"]);
  $title = trim($_POST["title"]);
  $core = trim($_POST["core"]);
  $status = $_POST["showT"];

  if (!empty($title) && !empty($core)) {
    $stmt = $db->prepare("UPDATE threads SET title=?, core=?, status=? WHERE id_order=?");
    $stmt->bind_param("sssi", $title, $core, $status, $id_order);
    $stmt->execute();
    $stmt->close();
  }
  header("Location: threads.php");
  exit();
}

// Hapus thread
if (isset($_POST["deleteThread"])) {
  $id_order = intval($_POST["id_order"]);
  $stmt = $db->prepare("DELETE FROM threads WHERE id_order=?");
  $stmt->bind_param("i", $id_order);
  $stmt->execute();
  $stmt->close();
  header("Location: threads.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="../Assets/cthreadbgr.png" />
  <title>Admin: Threads</title>
</head>

<body>
  <nav>
    <div class="logo">
      <a href="#"><img src="../Assets/cthreadbgr.png" alt="C-Thread" /></a>
    </div>
    <div class="visNav" id="visible">Threads</div>
    <ul class="listLink" id="hide">
      <li class="link"><a href="admin.php">Admin</a></li>
      <form method="POST" class="link">
        <button type="submit" name="logout" class="outButton">Keluar</button>
      </form>
    </ul>
    <button class="profil" id="barsButt">
      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="menu size-6" viewBox="0 0 24 24">
        <path
          d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" />
      </svg>
    </button>
  </nav>
  <section class="container">
    <div class="add">
      <div class="line"></div>
      <button id="newThreadBtn" class="new">Baru</button>
      <div class="line"></div>
    </div>
    <table>
      <tr class="head">
        <th>ID User</th>
        <th>Judul</th>
        <th>Konten</th>
        <th>Status</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
      <?php foreach ($threads as $thread): ?>
        <tr class="data">
          <td><?= htmlspecialchars($thread['id']); ?></td>
          <td><?= htmlspecialchars(substr($thread['title'], 0, 10)); ?>...</td>
          <td><?= htmlspecialchars(substr($thread['core'], 0, 30)); ?>...</td>
          <td><?= htmlspecialchars($thread['status']); ?></td>
          <td><?= htmlspecialchars($thread['created_at']); ?></td>
          <td>
            <button
              onclick="openEditModal(<?= $thread['id_order']; ?>, `<?= htmlspecialchars($thread['title'], ENT_QUOTES); ?>`, `<?= htmlspecialchars($thread['core'], ENT_QUOTES); ?>`, `<?= $thread['status']; ?>`)"
              class="editUsers">Edit</button>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="id_order" value="<?= $thread['id_order']; ?>" />
              <button type="submit" name="deleteThread" onclick="return confirm('Hapus thread ini?')"
                class="deleteUsers">Hapus</button>
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
  <!-- Modal Tambah Thread -->
  <div class="modal hidden" id="modalThreadNew">
    <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <p>
        ID User
        <input type="text" name="id" placeholder="masukkan id" required />
      </p>
      <p>
        Judul
        <input type="text" name="title" placeholder="masukkan judul" required />
      </p>
      <p>
        Konten
        <textarea name="core" rows="3" cols="40" required></textarea>
      </p>
      <p>
        Status Thread
        <label><input type="radio" name="showT" value="Public" checked> Public</label>
        <label><input type="radio" name="showT" value="Private"> Private</label>
      </p>
      <button class="submitModal" type="submit" name="submitThread">Tambahkan</button>
      <button class="backModal" type="button" name="back">Batal</button>
    </form>
  </div>
  <!-- Modal Edit Thread -->
  <div class="modal hidden" id="modalThreadEdit">
    <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <p>
        Judul
        <input type="text" name="title" placeholder="masukkan judul" id="edit-title" />
      </p>
      <p>
        Konten
        <textarea name="core" rows="3" cols="40" id="edit-core"></textarea>
      </p>
      <p>
        Status Thread
        <label><input type="radio" name="showT" value="Public" id="edit-status-public" checked> Public</label>
        <label><input type="radio" name="showT" value="Private" id="edit-status-private"> Private</label>
      </p>
      <button class="submitModal" type="submit" name="editThread">Simpan</button>
      <button class="backModal" type="button" name="back">Batal</button>
      <input type="hidden" name="id_order" id="edit-id-order">
    </form>
  </div>
  <script>
    // Navbar toggle
    document.getElementById("barsButt").addEventListener("click", function () {
      const visible = document.getElementById("visible");
      const hide = document.getElementById("hide");
      if (visible.style.display !== "none") {
        visible.style.display = "none";
        hide.style.display = "flex";
      } else {
        visible.style.display = "flex";
        hide.style.display = "none";
      }
    });
    document.getElementById("hide").style.display = "none";

    // Modal Tambah
    const newBtn = document.getElementById("newThreadBtn");
    const modalNew = document.getElementById("modalThreadNew");
    newBtn.onclick = () => modalNew.classList.remove("hidden");
    modalNew.querySelector(".backModal").onclick = () => modalNew.classList.add("hidden");

    // Modal Edit
    // Modal Edit
    const modalEdit = document.getElementById("modalThreadEdit");

    window.openEditModal = function (id_order, title, core, status) {
      document.getElementById("edit-id-order").value = id_order;
      document.getElementById("edit-title").value = title;
      document.getElementById("edit-core").value = core;

      if (status === "Public") {
        document.getElementById("edit-status-public").checked = true;
      } else {
        document.getElementById("edit-status-private").checked = true;
      }

      modalEdit.classList.remove("hidden");
    };

    modalEdit.querySelector(".backModal").onclick = () => modalEdit.classList.add("hidden");
  </script>
</body>

</html>
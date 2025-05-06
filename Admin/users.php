<?php
include "../service/db.php";
session_start();

$username = $_SESSION["username"];

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

$result = mysqli_query($db, "SELECT * FROM users ORDER BY id DESC LIMIT $start, $limit");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
$countResult = mysqli_query($db, "SELECT COUNT(*) AS total FROM users");
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Tambah pengguna
if (isset($_POST["submitUsr"])) {
    $username = trim($_POST["usersUsr"]);
    $password = trim($_POST["usersPw"]);
    if (!empty($username) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit();
}

// Edit pengguna
if (isset($_POST["editUsr"])) {
    $id = intval($_POST["id"]);
    $username = trim($_POST["usersUsr"]);
    $password = trim($_POST["usersPw"]);

    if (!empty($username)) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET username=?, password=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $hashedPassword, $id);
        } else {
            $stmt = $db->prepare("UPDATE users SET username=? WHERE id=?");
            $stmt->bind_param("si", $username, $id);
        }
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit();
}

// Hapus pengguna
if (isset($_POST["deleteUsers"])) {
    $id = intval($_POST["id"]);
    $stmt = $db->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="../Assets/cthreadbgr.png" />
    <title>Admin: Users</title>
</head>

<body>
    <nav>
        <div class="logo">
            <a href="#">
                <img src="../Assets/cthreadbgr.png" alt="C-Thread">
            </a>
        </div>
        <div class="visNav" id="visible">
            Pengguna
        </div>
        <ul class="listLink" id="hide">
            <li class="link"><a href="admin.php">Admin</a></li>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="link">
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
            <button id="newUsers" class="new" type="button" name="new">Baru</button>
            <div class="line"></div>
        </div>
        <table>
            <tr class="head">
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Dibuat Pada</th>
                <th>Kelola</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr class="data">
                    <td><?= htmlspecialchars($user['id']); ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars(substr($user['password'], 0, 20)); ?>...</td>
                    <td><?= htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <!--
                        <button class="editUsers" type="button"
                            onclick="openEditModal(<?= $user['id']; ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES); ?>')">Edit</button>
            -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                            style="display:inline;">
                            <input type="hidden" name="id" value="<?= $user['id']; ?>">
                            <button name="deleteUsers" class="deleteUsers" type="submit"
                                onclick="return confirm('Hapus User Ini?')">Hapus</button>
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
<div class="modal hidden" id="modalUsersNew">
    <form class="modal-content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <p>
            Nama
            <input type="text" name="usersUsr" placeholder="masukkan nama" />
        </p>
        <p>
            Password
            <input type="password" name="usersPw" placeholder="masukkan password" />
        </p>
        <div class="buttonUsr">
            <button class="submitModal" type="submit" name="submitUsr">Tambahkan</button>
            <button class="backModal" type="button">Batal</button>
        </div>
    </form>
</div>
<!-- Modal Edit -->
<!--
<div class="modal hidden" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="modalUsersEdit">
    <form class="modal-content" method="POST">
        <p>
            Nama
            <input type="text" name="usersUsr" placeholder="masukkan nama" />
        </p>
        <p>
            Password
            <input type="password" name="usersPw" placeholder="kosongkan jika tidak ada perubahan" />
        </p>
        <div class="buttonUsr">
            <button class="submitModal" type="submit" name="editUsr">Simpan</button>
            <button class="backModal" type="button">Batal</button>
        </div>
    </form>
</div>
                -->
<script>
    // Ganti nav bar
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

    // Set kondisi awal
    document.getElementById("hide").style.display = "none";

    document.addEventListener("DOMContentLoaded", function () {
        // Modal Tambah Baru
        const newButton = document.getElementById("newUsers");
        const modalUsersNew = document.getElementById("modalUsersNew");
        const backNew = modalUsersNew.querySelector(".backModal");

        newButton.onclick = () => {
            modalUsersNew.classList.remove("hidden");
        };
        backNew.onclick = () => {
            modalUsersNew.classList.add("hidden");
        };

        // Modal Edit
        /*
        const modalUsersEdit = document.getElementById("modalUsersEdit");
        const editBackButton = modalUsersEdit.querySelector(".backModal");

        window.openEditModal = function (id, username) {
            modalUsersEdit.querySelector('[name="usersUsr"]').value = username;
            modalUsersEdit.querySelector('[name="usersPw"]').value = ""; // Kosongkan password

            const form = modalUsersEdit.querySelector("form");
            form.action = "";
            let hiddenInput = form.querySelector('input[name="id"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "id";
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = id;

            modalUsersEdit.classList.remove("hidden");
        };

        editBackButton.onclick = () => {
            modalUsersEdit.classList.add("hidden");
        };
        */
    });
</script>

</html>
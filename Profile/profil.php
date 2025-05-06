<?php
include "../service/db.php";
session_start();

if (!isset($_SESSION["is_login"])) {
    header("location: ../index.php");
    exit();
}

$user_id = $_SESSION["id"];
$username = $_SESSION["username"];

$threadErr = "";

if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("location: ../index.php");
    exit();
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1);
$limit = 5;
$offset = ($page - 1) * $limit;

$sqlCount = "SELECT COUNT(*) as total FROM threads WHERE id = ?";
$stmtCount = $db->prepare($sqlCount);
$stmtCount->bind_param("i", $user_id);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();
$totalThreads = $rowCount['total'];

$totalPages = ceil($totalThreads / $limit);

$sqlThreads = "SELECT * FROM threads WHERE id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmtThreads = $db->prepare($sqlThreads);
$stmtThreads->bind_param("iii", $user_id, $limit, $offset);
$stmtThreads->execute();
$resultThreads = $stmtThreads->get_result();

$sql = "SELECT * FROM profile WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$name = $gender = $job = $email = $website = $bio = "";

if ($result && $result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    $name = $profile["name"];
    $gender = $profile["gender"];
    $job = $profile["job"];
    $email = $profile["email"];
    $website = $profile["website"];
    $bio = $profile["bio"];
}

if (isset($_POST["deleteThread"]) && isset($_POST["created_at"])) {
    $created_at = $_POST["created_at"];

    $sqlDelete = "DELETE FROM threads WHERE created_at = ? AND id = ?";
    $stmtDelete = $db->prepare($sqlDelete);
    $stmtDelete->bind_param("ss", $created_at, $user_id);
    $stmtDelete->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST["editModal"]) && isset($_POST["thread_id"])) {
    $thread_id = intval($_POST["thread_id"]);
    $newTitle = trim($_POST["titleT"]);
    $newContent = trim($_POST["coreT"]);
    $status = isset($_POST["showT"]) ? $_POST["showT"] : 'Public';

    if (!empty($newTitle) && !empty($newContent)) {
        $sqlUpdate = "UPDATE threads SET title = ?, core = ?, status = ? WHERE id_order = ? AND id = ?";
        $stmtUpdate = $db->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sssis", $newTitle, $newContent, $status, $thread_id, $user_id);
        $stmtUpdate->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $threadErr = "Judul dan isi tidak boleh kosong!";
    }
}

if (isset($_POST["submitModal"])) {
    $title = trim($_POST["titleT"]);
    $content = trim($_POST["coreT"]);
    $status = isset($_POST["showT"]) ? $_POST["showT"] : 'Public';

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO threads (id, title, core, status) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("isss", $user_id, $title, $content, $status);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $threadErr = "Gagal Mengunggah Thread Baru!";
        }
    } else {
        $threadErr = "Judul dan Isi Kosong!";
    }
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
    <title>Profil</title>
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
            <li class="link"><a href="../Dashboard/dashboard.php">Dashboard</a></li>
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
        <h1>Selamat Datang, <?= $_SESSION["username"] ?>!</h1>
        <div class="card">
            <div class="identity">
                <h3>Username</h3>
                <h5><?= $_SESSION["username"] ?></h5>
            </div>
            <div class="identity">
                <h3>Nama</h3>
                <h5><?= htmlspecialchars($name) ?></h5>
            </div>
            <div class="identity">
                <h3>Gender</h3>
                <h5><?= htmlspecialchars($gender) ?></h5>
            </div>
            <div class="identity">
                <h3>Pekerjaan</h3>
                <h5><?= htmlspecialchars($job) ?></h5>
            </div>
            <div class="identity">
                <h3>Email</h3>
                <h5><?= htmlspecialchars($email) ?></h5>
            </div>
            <div class="identity">
                <h3>Website</h3>
                <h5><?= htmlspecialchars($website) ?></h5>
            </div>
            <div class="identity">
                <h3>Bio</h3>
                <h5><?= htmlspecialchars($bio) ?></h5>
            </div>
            <form class="content" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <button id="editButton" class="edit" type="button" name="edit">Edit</button>
                <button id="outButton" class="logout" type="submit" name="logout">Keluar</button>
            </form>
        </div>
    </section>
    <section class="container">
        <div class="add">
            <div class="line"></div>
            <button id="newButton" class="new" type="button" name="new">Baru</button>
            <div class="line"></div>
        </div>
        <?php if ($resultThreads && $resultThreads->num_rows > 0): ?>
            <?php while ($thread = $resultThreads->fetch_assoc()): ?>
                <div class="contentT">
                    <h2><?= htmlspecialchars($thread["title"]); ?></h2>
                    <p><?= nl2br(htmlspecialchars($thread["core"])); ?></p>
                    <h6>Dibuat oleh <?= htmlspecialchars($username); ?> pada
                        <?= date('d M Y H:i', strtotime($thread["created_at"])); ?>
                    </h6>
                    <p>Status: <strong><?= htmlspecialchars(ucfirst($thread["status"])); ?></strong></p>
                    <div class="buttonT">
                        <button class="editThread" type="button" data-id="<?= $thread["id_order"]; ?>"
                            data-title="<?= htmlspecialchars($thread["title"], ENT_QUOTES); ?>"
                            data-core="<?= htmlspecialchars($thread["core"], ENT_QUOTES); ?>">
                            Edit Thread
                        </button>
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="created_at" value="<?= $thread["created_at"]; ?>">
                            <button class="deleteThread" type="submit" name="deleteThread">Hapus Thread</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="emptyCard">
                ~ Belum Ada Thread yang Dibuat ~
            </div>
        <?php endif; ?>
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
<!-- Hidden Add Form Thread -->
<div class="modal hidden" id="modalThread">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="modal-content" method="POST">
        <div class="inputT">
            <p>Judul Thread <span class="error">* <?php echo $threadErr; ?></span></p>
            <input type="text" name="titleT" placeholder="masukkan judul konten">
        </div>
        <div class="inputT">
            <p>Isi Thread <span class="error">* <?php echo $threadErr; ?></span></p>
            <textarea name="coreT" rows="5"></textarea>
        </div>
        <div class="inputT" radio>
            <p>Status Thread</p>
            <label><input type="radio" name="showT" value="Public" checked> Public</label>
            <label><input type="radio" name="showT" value="Private"> Private</label>
        </div>
        <div class="buttonT">
            <button class="submitModal" id="submitM" type="submit" name="submitModal">Tambahkan</button>
            <button class="backModal" id="backModal" type="button" name="backModal">Batal</button>
        </div>
    </form>
</div>
<!-- Hidden Edit Form Thread -->
<div class="modal hidden" id="editThread">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="modal-content" method="POST">
        <div class="inputT">
            <p>Judul Thread <span class="error">* <?php echo $threadErr; ?></span></p>
            <input type="text" name="titleT" placeholder="masukkan judul konten">
        </div>
        <div class="inputT">
            <p>Isi Thread <span class="error">* <?php echo $threadErr; ?></span></p>
            <textarea name="coreT" rows="5"></textarea>
        </div>
        <div class="inputT radio">
            <p>Status Thread</p>
            <label><input class="radioT" type="radio" name="showT" value="Public" checked> Public</label>
            <label><input class="radioT" type="radio" name="showT" value="Private"> Private</label>
        </div>
        <div class="buttonT">
            <button class="submitModal" id="editM" type="submit" name="editModal">Edit</button>
            <button class="backModal" id="backEdit" type="button" name="backModal">Batal</button>
        </div>
    </form>
</div>
<script src="../script.js"></script>

</html>
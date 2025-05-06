<?php
session_start();
include "../service/db.php";

if (!isset($_SESSION["is_login"])) {
    header("location: ../index.php");
    exit();
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1);
$limit = 5; // Tampilkan 5 thread per halaman
$offset = ($page - 1) * $limit;

$sqlCount = "SELECT COUNT(*) as total FROM threads";
$resultCount = $db->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalThreads = $rowCount['total'];

$totalPages = ceil($totalThreads / $limit);
$result = mysqli_query($db, "
    SELECT threads.*, users.username 
    FROM threads 
    JOIN users ON threads.id = users.id 
    WHERE threads.status = 'Public' 
    ORDER BY threads.created_at DESC 
    LIMIT $limit OFFSET $offset
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="../Assets/cthreadbgr.png">
    <title>C-Thread</title>
</head>

<body>
    <nav>
        <div class="logo">
            <a href="dashboard.php">
                <img src="../Assets/cthreadbgr.png" alt="C-Thread">
            </a>
        </div>
        <div class="visNav" id="visible">
            <?= $_SESSION["username"]; ?>
        </div>
        <ul class="listLink" id="hide">
            <li class="link"><a href="../Profile/profil.php">Profil</a></li>
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
    <header class="container">
        <h1>Selamat Datang!</h1>
        <h4>Forum Diskusi dan Informasi Bersama <span>C-Thread</span></h4>
    </header>
    <section class="container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($thread = $result->fetch_assoc()): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($thread["title"]); ?></h2>
                    <p><?= nl2br(htmlspecialchars($thread["core"])); ?></p>
                    <h6>Dibuat oleh <?= htmlspecialchars($thread["username"]); ?> pada
                        <?= date('d M Y H:i', strtotime($thread["created_at"])); ?>
                    </h6>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="emptyCard">
                ~ Belum Ada Thread yang Dibuat ~
            </div>
        <?php endif; ?>
    </section>
    <?php if ($totalPages > 1): ?>
        <div class="pagination" style="text-align: center">
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
    <footer class="copyright">
        Copyright © 2025 C-Thread. All Rights Reserved.
    </footer>
    <script src="../script.js"></script>
</body>

</html>
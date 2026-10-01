<?php
if (!isset($page_title)) {
    $page_title = "Equipment Borrowing System";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($page_title); ?></title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header class="navbar">

    <div class="nav-container">

        <a href="index.php" class="logo">
            Equipment System
        </a>

        <nav>

            <a href="index.php">Dashboard</a>

            <a href="equipment.php">Equipment</a>

            <?php if (isset($_SESSION['staff_id'])): ?>

                <a href="add_equipment.php">Add Equipment</a>

                <a href="borrow.php">Borrow</a>

                <a href="return.php">Return</a>

                <a href="transactions.php">Records</a>

                <a href="logout.php">Logout</a>

            <?php else: ?>

                <a href="login.php">Staff Login</a>

            <?php endif; ?>

        </nav>

    </div>

</header>

<main class="container">
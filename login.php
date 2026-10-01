<?php

require_once 'config/database.php';

if (isset($_SESSION['staff_id'])) {

    header("Location: index.php");

    exit;
}

$page_title = "Staff Login";

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username =
        trim($_POST['username']);

    $password =
        $_POST['password'];


    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id,
            username,
            password,
            full_name
         FROM users
         WHERE username = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $username
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result(
        $stmt,
        $id,
        $db_username,
        $db_password,
        $full_name
    );


    if (
        mysqli_stmt_fetch($stmt) &&
        password_verify(
            $password,
            $db_password
        )
    ) {

        $_SESSION['staff_id'] = $id;

        $_SESSION['staff_name'] =
            $full_name;

        header("Location: index.php");

        exit;

    } else {

        $error =
            "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}


include 'includes/header.php';

?>

<h1>Staff Login</h1>

<p>
Only authorized staff members can manage borrowing and returning equipment.
</p>


<?php if ($error): ?>

<div class="alert alert-error">

<?php echo $error; ?>

</div>

<?php endif; ?>


<div class="form-container">

<form method="POST">

<label>Username</label>

<input
type="text"
name="username"
required
>


<label>Password</label>

<input
type="password"
name="password"
required
>


<button type="submit">
Login
</button>

</form>

</div>

<?php include 'includes/footer.php'; ?>
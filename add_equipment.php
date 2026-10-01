<?php

require_once 'config/auth.php';

$page_title = "Add Equipment";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $equipment_name =
        trim($_POST['equipment_name']);

    $category =
        trim($_POST['category']);

    $quantity =
        (int) $_POST['quantity'];

    if (
        $equipment_name === "" ||
        $category === "" ||
        $quantity <= 0
    ) {

        $error = "Please enter valid equipment information.";

    } else {

        $status = "Available";

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO equipment
            (
                equipment_name,
                category,
                total_quantity,
                available_quantity,
                status
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssiis",
            $equipment_name,
            $category,
            $quantity,
            $quantity,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Equipment added successfully.";

        } else {

            $error = "Unable to add equipment.";

        }

        mysqli_stmt_close($stmt);
    }
}

include 'includes/header.php';

?>

<h1>Add Equipment</h1>

<?php if ($message): ?>

<div class="alert alert-success">
    <?php echo $message; ?>
</div>

<?php endif; ?>


<?php if ($error): ?>

<div class="alert alert-error">
    <?php echo $error; ?>
</div>

<?php endif; ?>


<div class="form-container">

<form method="POST">

<label>Equipment Name</label>

<input
type="text"
name="equipment_name"
required
>


<label>Category</label>

<input
type="text"
name="category"
required
>


<label>Quantity</label>

<input
type="number"
name="quantity"
min="1"
required
>


<button type="submit">
    Add Equipment
</button>

</form>

</div>

<?php include 'includes/footer.php'; ?>
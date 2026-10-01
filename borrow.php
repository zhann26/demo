<?php

require_once 'config/auth.php';

$page_title = "Borrow Equipment";

$error = "";

$selected_equipment = isset($_GET['equipment_id'])
    ? (int) $_GET['equipment_id']
    : 0;


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $borrower_name =
        trim($_POST['borrower_name']);

    $borrower_type =
        $_POST['borrower_type'];

    $equipment_id =
        (int) $_POST['equipment_id'];

    $quantity =
        (int) $_POST['quantity'];

    $due_date =
        $_POST['due_date'];


    if (
        $borrower_name === "" ||
        $equipment_id <= 0 ||
        $quantity <= 0 ||
        empty($due_date)
    ) {

        $error = "Please complete all fields.";

    } elseif (
        !in_array(
            $borrower_type,
            ['Student', 'Faculty']
        )
    ) {

        $error = "Invalid borrower type.";

    } elseif ($due_date < date("Y-m-d")) {

        $error =
            "Due date cannot be earlier than today.";

    } else {

        mysqli_begin_transaction($conn);

        try {

            $stmt = mysqli_prepare(
                $conn,
                "SELECT available_quantity
                 FROM equipment
                 WHERE id = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $equipment_id
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_bind_result(
                $stmt,
                $available_quantity
            );

            if (!mysqli_stmt_fetch($stmt)) {
                throw new Exception(
                    "Equipment not found."
                );
            }

            mysqli_stmt_close($stmt);


            if ($available_quantity < $quantity) {

                throw new Exception(
                    "Not enough equipment available."
                );

            }


            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO borrowings
                (
                    borrower_name,
                    borrower_type,
                    equipment_id,
                    quantity,
                    due_date,
                    status
                )
                VALUES (?, ?, ?, ?, ?, 'Borrowed')"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssiis",
                $borrower_name,
                $borrower_type,
                $equipment_id,
                $quantity,
                $due_date
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);


            $remaining =
                $available_quantity - $quantity;

            $equipment_status =
                $remaining > 0
                ? "Available"
                : "Unavailable";


            $stmt = mysqli_prepare(
                $conn,
                "UPDATE equipment
                 SET available_quantity = ?,
                     status = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "isi",
                $remaining,
                $equipment_status,
                $equipment_id
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);


            mysqli_commit($conn);

            header(
                "Location: transactions.php?borrowed=1"
            );

            exit;

        } catch (Exception $e) {

            mysqli_rollback($conn);

            $error = $e->getMessage();
        }
    }
}


$equipment_result = mysqli_query(
    $conn,
    "SELECT *
     FROM equipment
     WHERE available_quantity > 0
     ORDER BY equipment_name"
);


include 'includes/header.php';

?>

<h1>Borrow Equipment</h1>

<?php if ($error): ?>

<div class="alert alert-error">
    <?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>


<div class="form-container">

<form method="POST">

<label>Borrower Name</label>

<input
type="text"
name="borrower_name"
required
>


<label>Borrower Type</label>

<select name="borrower_type" required>

<option value="Student">
Student
</option>

<option value="Faculty">
Faculty
</option>

</select>


<label>Equipment</label>

<select name="equipment_id" required>

<option value="">
Select Equipment
</option>

<?php while (
    $equipment =
    mysqli_fetch_assoc($equipment_result)
): ?>

<option
value="<?php echo $equipment['id']; ?>"
<?php
if (
    $selected_equipment ==
    $equipment['id']
) {
    echo "selected";
}
?>
>

<?php
echo htmlspecialchars(
    $equipment['equipment_name']
);
?>

-
<?php echo $equipment['available_quantity']; ?>
available

</option>

<?php endwhile; ?>

</select>


<label>Quantity</label>

<input
type="number"
name="quantity"
value="1"
min="1"
required
>


<label>Due Date</label>

<input
type="date"
name="due_date"
min="<?php echo date('Y-m-d'); ?>"
required
>


<button type="submit">
Borrow Equipment
</button>

</form>

</div>

<?php include 'includes/footer.php'; ?>
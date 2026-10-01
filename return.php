<?php

require_once 'config/auth.php';

$page_title = "Return Equipment";

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $borrowing_id =
        (int) $_POST['borrowing_id'];

    mysqli_begin_transaction($conn);

    try {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT
                equipment_id,
                quantity,
                status
             FROM borrowings
             WHERE id = ?
             FOR UPDATE"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $borrowing_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result(
            $stmt,
            $equipment_id,
            $quantity,
            $status
        );

        if (!mysqli_stmt_fetch($stmt)) {

            throw new Exception(
                "Borrowing record not found."
            );

        }

        mysqli_stmt_close($stmt);


        if ($status !== "Borrowed") {

            throw new Exception(
                "This item has already been returned."
            );

        }


        $stmt = mysqli_prepare(
            $conn,
            "UPDATE borrowings
             SET
                status = 'Returned',
                return_date = NOW()
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $borrowing_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);


        $stmt = mysqli_prepare(
            $conn,
            "UPDATE equipment
             SET
                available_quantity =
                    LEAST(
                        total_quantity,
                        available_quantity + ?
                    ),
                status = 'Available'
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $quantity,
            $equipment_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);


        mysqli_commit($conn);


        header(
            "Location: transactions.php?returned=1"
        );

        exit;

    } catch (Exception $e) {

        mysqli_rollback($conn);

        $error = $e->getMessage();
    }
}


$result = mysqli_query(
    $conn,
    "SELECT
        b.id,
        b.borrower_name,
        b.borrower_type,
        b.quantity,
        b.borrow_date,
        b.due_date,
        e.equipment_name

     FROM borrowings b

     JOIN equipment e
        ON b.equipment_id = e.id

     WHERE b.status = 'Borrowed'

     ORDER BY b.borrow_date DESC"
);


include 'includes/header.php';

?>

<h1>Return Equipment</h1>

<?php if ($error): ?>

<div class="alert alert-error">
    <?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>


<div class="table-container">

<table>

<thead>

<tr>
    <th>Borrower</th>
    <th>Type</th>
    <th>Equipment</th>
    <th>Quantity</th>
    <th>Borrowed</th>
    <th>Due Date</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php if (mysqli_num_rows($result) > 0): ?>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td>
<?php echo htmlspecialchars($row['borrower_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['borrower_type']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['equipment_name']); ?>
</td>

<td>
<?php echo $row['quantity']; ?>
</td>

<td>
<?php echo $row['borrow_date']; ?>
</td>

<td>
<?php echo $row['due_date']; ?>
</td>

<td>

<form method="POST">

<input
type="hidden"
name="borrowing_id"
value="<?php echo $row['id']; ?>"
>

<button
type="submit"
class="btn-success"
>

Return

</button>

</form>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="7">
No currently borrowed equipment.
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<?php include 'includes/footer.php'; ?>
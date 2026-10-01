<?php

require_once 'config/auth.php';

$page_title = "Borrowing Records";


$result = mysqli_query(
    $conn,
    "SELECT

        b.id,
        b.borrower_name,
        b.borrower_type,
        b.quantity,
        b.borrow_date,
        b.due_date,
        b.return_date,
        b.status,

        e.equipment_name

    FROM borrowings b

    JOIN equipment e
        ON b.equipment_id = e.id

    ORDER BY b.borrow_date DESC"
);


include 'includes/header.php';

?>

<h1>Borrowing Records</h1>


<?php if (isset($_GET['borrowed'])): ?>

<div class="alert alert-success">
Equipment borrowed successfully.
</div>

<?php endif; ?>


<?php if (isset($_GET['returned'])): ?>

<div class="alert alert-success">
Equipment returned successfully.
</div>

<?php endif; ?>


<div class="table-container">

<table>

<thead>

<tr>
    <th>ID</th>
    <th>Borrower</th>
    <th>Type</th>
    <th>Equipment</th>
    <th>Quantity</th>
    <th>Borrow Date</th>
    <th>Due Date</th>
    <th>Return Date</th>
    <th>Status</th>
</tr>

</thead>

<tbody>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td>
<?php echo $row['id']; ?>
</td>


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

<?php

echo $row['return_date']
    ? $row['return_date']
    : "-";

?>

</td>


<td>

<?php

if (
    $row['status'] === "Borrowed" &&
    $row['due_date'] < date("Y-m-d")
):

?>

<span class="badge overdue">
Overdue
</span>


<?php elseif ($row['status'] === "Borrowed"): ?>

<span class="badge borrowed">
Borrowed
</span>


<?php else: ?>

<span class="badge returned">
Returned
</span>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include 'includes/footer.php'; ?>
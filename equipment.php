<?php

require_once 'config/database.php';

$page_title = "Equipment";

$result = mysqli_query(
    $conn,
    "SELECT * FROM equipment
     ORDER BY equipment_name"
);

include 'includes/header.php';

?>

<h1>Equipment</h1>

<p>
    View the equipment currently available in the department.
</p>

<?php if (isset($_SESSION['staff_id'])): ?>

<a href="add_equipment.php" class="btn">
    Add Equipment
</a>

<?php endif; ?>

<br><br>

<div class="table-container">

<table>

<thead>

<tr>
    <th>ID</th>
    <th>Equipment</th>
    <th>Category</th>
    <th>Total</th>
    <th>Available</th>
    <th>Status</th>

    <?php if (isset($_SESSION['staff_id'])): ?>
        <th>Action</th>
    <?php endif; ?>

</tr>

</thead>

<tbody>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td>
    <?php echo $row['id']; ?>
</td>

<td>
    <?php echo htmlspecialchars($row['equipment_name']); ?>
</td>

<td>
    <?php echo htmlspecialchars($row['category']); ?>
</td>

<td>
    <?php echo $row['total_quantity']; ?>
</td>

<td>
    <?php echo $row['available_quantity']; ?>
</td>

<td>

<?php if ($row['available_quantity'] > 0): ?>

<span class="badge available">
    Available
</span>

<?php else: ?>

<span class="badge unavailable">
    Unavailable
</span>

<?php endif; ?>

</td>

<?php if (isset($_SESSION['staff_id'])): ?>

<td>

<?php if ($row['available_quantity'] > 0): ?>

<a
href="borrow.php?equipment_id=<?php echo $row['id']; ?>"
class="btn"
>
Borrow
</a>

<?php else: ?>

Not available

<?php endif; ?>

</td>

<?php endif; ?>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include 'includes/footer.php'; ?>
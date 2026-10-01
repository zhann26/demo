<?php

require_once 'config/database.php';

$page_title = "Dashboard";

$total_result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_quantity), 0) AS total FROM equipment"
);

$total_equipment =
    mysqli_fetch_assoc($total_result)['total'];


$available_result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(available_quantity), 0) AS total FROM equipment"
);

$available_equipment =
    mysqli_fetch_assoc($available_result)['total'];


$borrowed_result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(quantity), 0) AS total
     FROM borrowings
     WHERE status = 'Borrowed'"
);

$borrowed_equipment =
    mysqli_fetch_assoc($borrowed_result)['total'];


$overdue_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM borrowings
     WHERE status = 'Borrowed'
     AND due_date < CURDATE()"
);

$overdue =
    mysqli_fetch_assoc($overdue_result)['total'];


$equipment_result = mysqli_query(
    $conn,
    "SELECT * FROM equipment
     ORDER BY equipment_name"
);

include 'includes/header.php';

?>

<h1>Equipment Borrowing Management System</h1>

<p class="subtitle">
    Check equipment availability and monitor borrowing activities.
</p>

<div class="cards">

    <div class="card">
        <h3>Total Equipment</h3>
        <div class="card-number">
            <?php echo $total_equipment; ?>
        </div>
    </div>

    <div class="card">
        <h3>Available</h3>
        <div class="card-number">
            <?php echo $available_equipment; ?>
        </div>
    </div>

    <div class="card">
        <h3>Currently Borrowed</h3>
        <div class="card-number">
            <?php echo $borrowed_equipment; ?>
        </div>
    </div>

    <div class="card">
        <h3>Overdue Records</h3>
        <div class="card-number">
            <?php echo $overdue; ?>
        </div>
    </div>

</div>

<h2>Equipment Availability</h2>

<div class="table-container">

<table>

    <thead>
        <tr>
            <th>Equipment</th>
            <th>Category</th>
            <th>Total</th>
            <th>Available</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

    <?php while ($equipment = mysqli_fetch_assoc($equipment_result)): ?>

        <tr>

            <td>
                <?php echo htmlspecialchars($equipment['equipment_name']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($equipment['category']); ?>
            </td>

            <td>
                <?php echo $equipment['total_quantity']; ?>
            </td>

            <td>
                <?php echo $equipment['available_quantity']; ?>
            </td>

            <td>

                <?php if ($equipment['available_quantity'] > 0): ?>

                    <span class="badge available">
                        Available
                    </span>

                <?php else: ?>

                    <span class="badge unavailable">
                        Unavailable
                    </span>

                <?php endif; ?>

            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>

</div>

<?php include 'includes/footer.php'; ?>
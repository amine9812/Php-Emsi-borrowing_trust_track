<?php
// Borrower details page: profile, loan history, and trust events.

$borrowerRepo = new BorrowerRepo($db);

$borrowerId = (int) ($_GET['id'] ?? 0);
$borrower = $borrowerRepo->find($borrowerId);

if (!$borrower) {
    echo '<section class="card"><h1>Borrower not found</h1></section>';
    return;
}

$loans = $borrowerRepo->loansForBorrower($borrowerId);
$events = $borrowerRepo->trustEventsForBorrower($borrowerId);
?>

<section class="card">
    <h1><?php echo e($borrower['name']); ?></h1>
    <p class="helper">Current trust score</p>
    <div class="stat">
        <div class="value"><?php echo e((string) $borrower['trust_score']); ?></div>
    </div>
    <p>
        <?php echo e($borrower['email']); ?>
        <?php if ($borrower['phone']) : ?>
            • <?php echo e($borrower['phone']); ?>
        <?php endif; ?>
    </p>
</section>

<section class="card">
    <h2>Loan history</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Loan date</th>
                <th>Due date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($loans)) : ?>
                <tr><td colspan="4">No loans recorded.</td></tr>
            <?php endif; ?>
            <?php foreach ($loans as $loan) : ?>
                <tr>
                    <td><?php echo e($loan['item_name']); ?></td>
                    <td><?php echo e($loan['loan_date']); ?></td>
                    <td><?php echo e($loan['due_date']); ?></td>
                    <td>
                        <span class="badge <?php echo $loan['status'] === 'open' ? 'warn' : ($loan['status'] === 'lost' ? 'danger' : 'ok'); ?>">
                            <?php echo e($loan['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="card">
    <h2>Trust events</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reason</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)) : ?>
                <tr><td colspan="3">No trust events yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($events as $event) : ?>
                <tr>
                    <td><?php echo e($event['created_at']); ?></td>
                    <td><?php echo e($event['reason']); ?></td>
                    <td><?php echo e((string) $event['points_delta']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

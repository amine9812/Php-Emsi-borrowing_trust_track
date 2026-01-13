<?php
// Dashboard page: summary stats and overdue items.

$borrowerRepo = new BorrowerRepo($db);
$loanRepo = new LoanRepo($db);

$today = today_date();
$openCount = $loanRepo->countOpen();
$overdueCount = $loanRepo->countOverdue($today);
$topBorrowers = $borrowerRepo->topTrusted(5);
$bottomBorrowers = $borrowerRepo->bottomTrusted(5);
$overdueLoans = $loanRepo->overdueList($today);
?>

<section class="card">
    <h1>Dashboard</h1>
    <div class="grid grid-3">
        <div class="stat">
            <div class="label">Open loans</div>
            <div class="value"><?php echo e((string) $openCount); ?></div>
        </div>
        <div class="stat">
            <div class="label">Overdue loans</div>
            <div class="value"><?php echo e((string) $overdueCount); ?></div>
        </div>
        <div class="stat">
            <div class="label">Trust scale</div>
            <div class="value">0 - 100</div>
        </div>
    </div>
</section>

<section class="grid grid-2">
    <div class="card">
        <h2>Top trusted borrowers</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($topBorrowers)) : ?>
                    <tr><td colspan="2">No borrowers yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($topBorrowers as $borrower) : ?>
                    <tr>
                        <td>
                            <a href="/?page=borrower&id=<?php echo e((string) $borrower['id']); ?>">
                                <?php echo e($borrower['name']); ?>
                            </a>
                        </td>
                        <td><?php echo e((string) $borrower['trust_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Lowest trusted borrowers</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bottomBorrowers)) : ?>
                    <tr><td colspan="2">No borrowers yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($bottomBorrowers as $borrower) : ?>
                    <tr>
                        <td>
                            <a href="/?page=borrower&id=<?php echo e((string) $borrower['id']); ?>">
                                <?php echo e($borrower['name']); ?>
                            </a>
                        </td>
                        <td><?php echo e((string) $borrower['trust_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Overdue loans</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Borrower</th>
                <th>Item</th>
                <th>Due date</th>
                <th>Days late</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($overdueLoans)) : ?>
                <tr><td colspan="4">No overdue loans. Great job!</td></tr>
            <?php endif; ?>
            <?php foreach ($overdueLoans as $loan) : ?>
                <?php $daysLate = max(0, date_diff_days($today, $loan['due_date'])); ?>
                <tr>
                    <td><?php echo e($loan['borrower_name']); ?></td>
                    <td><?php echo e($loan['item_name']); ?></td>
                    <td><?php echo e($loan['due_date']); ?></td>
                    <td><span class="badge danger"><?php echo e((string) $daysLate); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

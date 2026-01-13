<?php
// Loans page: create loans, list loans, and process returns.

$borrowerRepo = new BorrowerRepo($db);
$itemRepo = new ItemRepo($db);
$loanRepo = new LoanRepo($db);
$trustService = new TrustService($db, $borrowerRepo, $loanRepo);

$errors = [];
$returnErrors = [];
$returnLoanId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $borrowerId = (int) ($_POST['borrower_id'] ?? 0);
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $dueDate = trim($_POST['due_date'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($borrowerId <= 0) {
            $errors[] = 'Borrower is required.';
        }
        if ($itemId <= 0) {
            $errors[] = 'Item is required.';
        }
        if ($dueDate === '') {
            $errors[] = 'Due date is required.';
        }

        if (empty($errors)) {
            $created = $loanRepo->create([
                'borrower_id' => $borrowerId,
                'item_id' => $itemId,
                'loan_date' => today_date(),
                'due_date' => $dueDate,
                'notes' => $notes,
            ]);

            if ($created) {
                flash_set('success', 'Loan created.');
                redirect('/?page=loans');
            }
            $errors[] = 'Item is currently on an open loan.';
        }
    }

    if ($action === 'return') {
        $loanId = (int) ($_POST['loan_id'] ?? 0);
        $returnedAt = trim($_POST['returned_at'] ?? '');
        $condition = trim($_POST['return_condition'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $returnLoanId = $loanId;

        if ($loanId <= 0) {
            $returnErrors[] = 'Loan is required.';
        }
        if ($returnedAt === '') {
            $returnedAt = today_date();
        }
        if (!in_array($condition, ['ok', 'damaged_minor', 'damaged_major', 'lost'], true)) {
            $returnErrors[] = 'Return condition is required.';
        }

        if (empty($returnErrors)) {
            $result = $trustService->processReturn($loanId, $returnedAt, $condition, $notes);
            if ($result['ok']) {
                flash_set('success', 'Loan updated and trust score adjusted.');
                redirect('/?page=loans');
            }
            $returnErrors[] = $result['error'] ?? 'Unable to process return.';
        }
    }
}

$statusFilter = $_GET['status'] ?? 'open';
$statusFilter = $statusFilter === 'all' ? null : $statusFilter;

$loans = $loanRepo->all($statusFilter);
$borrowers = $borrowerRepo->all();
$availableItems = $itemRepo->availableItems();

$returnLoan = null;
if ($returnLoanId > 0) {
    $returnLoan = $loanRepo->findWithDetails($returnLoanId);
} elseif (isset($_GET['return'])) {
    $returnLoan = $loanRepo->findWithDetails((int) $_GET['return']);
}

if ($returnLoan && $returnLoan['status'] !== 'open') {
    flash_set('error', 'This loan is already closed.');
    $returnLoan = null;
}
?>

<section class="card">
    <h1>Loans</h1>
    <p class="helper">Create loans, manage returns, and keep trust scores fair and transparent.</p>
</section>

<section class="card">
    <h2>New loan</h2>
    <?php if (!empty($errors)) : ?>
        <div class="flash flash-error">
            <?php echo e(implode(' ', $errors)); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/?page=loans">
        <input type="hidden" name="action" value="create">
        <div class="form-row grid-2">
            <div>
                <label for="borrower_id">Borrower *</label>
                <select id="borrower_id" name="borrower_id" required>
                    <option value="">Select borrower</option>
                    <?php foreach ($borrowers as $borrower) : ?>
                        <option value="<?php echo e((string) $borrower['id']); ?>">
                            <?php echo e($borrower['name']); ?> (<?php echo e((string) $borrower['trust_score']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="item_id">Item *</label>
                <select id="item_id" name="item_id" required>
                    <option value="">Select available item</option>
                    <?php foreach ($availableItems as $item) : ?>
                        <option value="<?php echo e((string) $item['id']); ?>">
                            <?php echo e($item['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="due_date">Due date *</label>
                <input id="due_date" name="due_date" type="date" required>
            </div>
            <div>
                <label for="notes">Notes</label>
                <input id="notes" name="notes" placeholder="Optional notes">
            </div>
        </div>
        <button class="button" type="submit">Create loan</button>
    </form>
</section>

<?php if ($returnLoan) : ?>
<section class="card">
    <h2>Return loan</h2>
    <p class="helper">Loan: <?php echo e($returnLoan['borrower_name']); ?> • <?php echo e($returnLoan['item_name']); ?></p>
    <?php if (!empty($returnErrors)) : ?>
        <div class="flash flash-error">
            <?php echo e(implode(' ', $returnErrors)); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/?page=loans">
        <input type="hidden" name="action" value="return">
        <input type="hidden" name="loan_id" value="<?php echo e((string) $returnLoan['id']); ?>">
        <div class="form-row grid-2">
            <div>
                <label for="returned_at">Returned date</label>
                <input id="returned_at" name="returned_at" type="date" value="<?php echo e(today_date()); ?>">
            </div>
            <div>
                <label for="return_condition">Condition *</label>
                <select id="return_condition" name="return_condition" required>
                    <option value="">Select condition</option>
                    <option value="ok">OK</option>
                    <option value="damaged_minor">Damaged (minor)</option>
                    <option value="damaged_major">Damaged (major)</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="return_notes">Notes</label>
                <textarea id="return_notes" name="notes"></textarea>
            </div>
        </div>
        <button class="button" type="submit">Finalize return</button>
        <a class="button secondary" href="/?page=loans">Cancel</a>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <h2>Loan list</h2>
    <div class="inline-actions" style="margin-bottom: 1rem;">
        <a class="button secondary" href="/?page=loans&status=open">Open</a>
        <a class="button secondary" href="/?page=loans&status=returned">Returned</a>
        <a class="button secondary" href="/?page=loans&status=lost">Lost</a>
        <a class="button secondary" href="/?page=loans&status=all">All</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Borrower</th>
                <th>Item</th>
                <th>Loan date</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($loans)) : ?>
                <tr><td colspan="6">No loans match this filter.</td></tr>
            <?php endif; ?>
            <?php foreach ($loans as $loan) : ?>
                <?php
                $statusClass = $loan['status'] === 'open' ? 'warn' : ($loan['status'] === 'lost' ? 'danger' : 'ok');
                $daysLate = $loan['status'] === 'open' ? max(0, date_diff_days(today_date(), $loan['due_date'])) : 0;
                ?>
                <tr>
                    <td><?php echo e($loan['borrower_name']); ?></td>
                    <td><?php echo e($loan['item_name']); ?></td>
                    <td><?php echo e($loan['loan_date']); ?></td>
                    <td>
                        <?php echo e($loan['due_date']); ?>
                        <?php if ($daysLate > 0) : ?>
                            <span class="badge danger"><?php echo e((string) $daysLate); ?> late</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo e($loan['status']); ?></span></td>
                    <td>
                        <?php if ($loan['status'] === 'open') : ?>
                            <a class="button secondary" href="/?page=loans&return=<?php echo e((string) $loan['id']); ?>">Return</a>
                        <?php else : ?>
                            <span class="helper">Closed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

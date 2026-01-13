<?php
// Borrowers page: list borrowers and handle add/edit/delete forms.

$borrowerRepo = new BorrowerRepo($db);

$errors = [];
$form = [
    'id' => '',
    'name' => '',
    'email' => '',
    'phone' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $form['id'] = trim($_POST['id'] ?? '');
        $form['name'] = trim($_POST['name'] ?? '');
        $form['email'] = trim($_POST['email'] ?? '');
        $form['phone'] = trim($_POST['phone'] ?? '');

        if ($form['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if (empty($errors)) {
            if ($form['id'] !== '') {
                $borrowerRepo->update((int) $form['id'], $form);
                flash_set('success', 'Borrower updated.');
            } else {
                $borrowerRepo->create($form);
                flash_set('success', 'Borrower added.');
            }
            redirect('/?page=borrowers');
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            if ($borrowerRepo->delete($id)) {
                flash_set('success', 'Borrower deleted.');
            } else {
                flash_set('error', 'Cannot delete borrower with existing loans.');
            }
        }
        redirect('/?page=borrowers');
    }
}

if (isset($_GET['edit'])) {
    $borrower = $borrowerRepo->find((int) $_GET['edit']);
    if ($borrower) {
        $form['id'] = (string) $borrower['id'];
        $form['name'] = $borrower['name'];
        $form['email'] = $borrower['email'];
        $form['phone'] = $borrower['phone'];
    }
}

$borrowers = $borrowerRepo->all();
?>

<section class="card">
    <h1>Borrowers</h1>
    <p class="helper">Track who is borrowing items and how their trust score changes over time.</p>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Trust score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($borrowers)) : ?>
                <tr><td colspan="4">No borrowers yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($borrowers as $borrower) : ?>
                <tr>
                    <td>
                        <a href="/?page=borrower&id=<?php echo e((string) $borrower['id']); ?>">
                            <?php echo e($borrower['name']); ?>
                        </a>
                    </td>
                    <td>
                        <div><?php echo e($borrower['email']); ?></div>
                        <div class="helper"><?php echo e($borrower['phone']); ?></div>
                    </td>
                    <td><?php echo e((string) $borrower['trust_score']); ?></td>
                    <td>
                        <div class="inline-actions">
                            <a class="button secondary" href="/?page=borrowers&edit=<?php echo e((string) $borrower['id']); ?>">Edit</a>
                            <form method="post" action="/?page=borrowers" onsubmit="return confirm('Delete this borrower?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo e((string) $borrower['id']); ?>">
                                <button class="button danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="card">
    <h2><?php echo $form['id'] !== '' ? 'Edit Borrower' : 'Add Borrower'; ?></h2>
    <?php if (!empty($errors)) : ?>
        <div class="flash flash-error">
            <?php echo e(implode(' ', $errors)); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/?page=borrowers">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo e($form['id']); ?>">
        <div class="form-row grid-2">
            <div>
                <label for="name">Name *</label>
                <input id="name" name="name" value="<?php echo e($form['name']); ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" value="<?php echo e($form['email']); ?>">
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="<?php echo e($form['phone']); ?>">
            </div>
        </div>
        <button class="button" type="submit">Save borrower</button>
        <?php if ($form['id'] !== '') : ?>
            <a class="button secondary" href="/?page=borrowers">Cancel</a>
        <?php endif; ?>
    </form>
</section>

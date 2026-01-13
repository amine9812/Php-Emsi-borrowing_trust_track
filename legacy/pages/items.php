<?php
// Items page: list items and handle add/edit/delete forms.

$itemRepo = new ItemRepo($db);

$errors = [];
$form = [
    'id' => '',
    'name' => '',
    'category' => '',
    'serial' => '',
    'notes' => '',
    'is_active' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $form['id'] = trim($_POST['id'] ?? '');
        $form['name'] = trim($_POST['name'] ?? '');
        $form['category'] = trim($_POST['category'] ?? '');
        $form['serial'] = trim($_POST['serial'] ?? '');
        $form['notes'] = trim($_POST['notes'] ?? '');
        $form['is_active'] = isset($_POST['is_active']) ? 1 : 0;

        if ($form['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if (empty($errors)) {
            if ($form['id'] !== '') {
                $itemRepo->update((int) $form['id'], $form);
                flash_set('success', 'Item updated.');
            } else {
                $itemRepo->create($form);
                flash_set('success', 'Item added.');
            }
            redirect('/?page=items');
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            if ($itemRepo->delete($id)) {
                flash_set('success', 'Item deleted.');
            } else {
                flash_set('error', 'Cannot delete item with existing loans.');
            }
        }
        redirect('/?page=items');
    }
}

if (isset($_GET['edit'])) {
    $item = $itemRepo->find((int) $_GET['edit']);
    if ($item) {
        $form['id'] = (string) $item['id'];
        $form['name'] = $item['name'];
        $form['category'] = $item['category'];
        $form['serial'] = $item['serial'];
        $form['notes'] = $item['notes'];
        $form['is_active'] = (int) $item['is_active'];
    }
}

$items = $itemRepo->allWithAvailability();
?>

<section class="card">
    <h1>Items</h1>
    <p class="helper">Manage items that can be loaned out to borrowers.</p>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Serial</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)) : ?>
                <tr><td colspan="5">No items yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($items as $item) : ?>
                <tr>
                    <td><?php echo e($item['name']); ?></td>
                    <td><?php echo e($item['category']); ?></td>
                    <td><?php echo e($item['serial']); ?></td>
                    <td>
                        <?php if ((int) $item['is_active'] === 0) : ?>
                            <span class="badge">Inactive</span>
                        <?php else : ?>
                            <span class="badge <?php echo (int) $item['is_available'] === 1 ? 'ok' : 'warn'; ?>">
                                <?php echo (int) $item['is_available'] === 1 ? 'Available' : 'On loan'; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="inline-actions">
                            <a class="button secondary" href="/?page=items&edit=<?php echo e((string) $item['id']); ?>">Edit</a>
                            <form method="post" action="/?page=items" onsubmit="return confirm('Delete this item?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo e((string) $item['id']); ?>">
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
    <h2><?php echo $form['id'] !== '' ? 'Edit Item' : 'Add Item'; ?></h2>
    <?php if (!empty($errors)) : ?>
        <div class="flash flash-error">
            <?php echo e(implode(' ', $errors)); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/?page=items">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo e($form['id']); ?>">
        <div class="form-row grid-2">
            <div>
                <label for="name">Name *</label>
                <input id="name" name="name" value="<?php echo e($form['name']); ?>" required>
            </div>
            <div>
                <label for="category">Category</label>
                <input id="category" name="category" value="<?php echo e($form['category']); ?>">
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="serial">Serial</label>
                <input id="serial" name="serial" value="<?php echo e($form['serial']); ?>">
            </div>
            <div>
                <label for="is_active">
                    <input id="is_active" type="checkbox" name="is_active" value="1" <?php echo (int) $form['is_active'] === 1 ? 'checked' : ''; ?>>
                    Active
                </label>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes"><?php echo e($form['notes']); ?></textarea>
            </div>
        </div>
        <button class="button" type="submit">Save item</button>
        <?php if ($form['id'] !== '') : ?>
            <a class="button secondary" href="/?page=items">Cancel</a>
        <?php endif; ?>
    </form>
</section>

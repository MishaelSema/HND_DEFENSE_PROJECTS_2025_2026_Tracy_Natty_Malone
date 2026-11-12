<div class="dashboard-page-header">
    <h1>Users</h1>
    <p>Manage all users on the platform</p>
</div>

<div style="background: white; border-radius: 12px; overflow: hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Verified</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user->id ?></td>
                <td><?= htmlspecialchars($user->name) ?></td>
                <td><?= htmlspecialchars($user->email) ?></td>
                <td>
                    <span class="admin-badge admin-badge-<?= $user->role ?>">
                        <?= ucfirst($user->role) ?>
                    </span>
                </td>
                <td>
                    <?php if ($user->is_verified): ?>
                        <span style="color: #188038;">✓ Yes</span>
                    <?php else: ?>
                        <span style="color: #6D5D57;">- No</span>
                    <?php endif; ?>
                </td>
                <td><?= date('M d, Y', strtotime($user->created_at ?? 'now')) ?></td>
                <td>
                    <?php if ($user->role !== 'admin'): ?>
                    <form method="POST" action="/AFROTOUCH/public/admin/users/<?= $user->id ?>/delete" style="display: inline;">
                        <button type="submit" class="btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($users)): ?>
<p style="text-align: center; padding: 40px; color: #6D5D57;">No users found.</p>
<?php endif; ?>

<script>
lucide.createIcons();
</script>

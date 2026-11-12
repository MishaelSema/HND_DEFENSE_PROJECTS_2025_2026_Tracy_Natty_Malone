<?php
$page_title = 'Shops';
$current_page = 'shops';
?>

<h1>All Shops</h1>

<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop Name</th>
                <th>Owner</th>
                <th>Email</th>
                <th>Location</th>
                <th>Products</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($shops as $shop): $seller = is_array($shop) ? $shop['user'] : $shop; ?>
            <tr>
                <td>#<?= $seller->id ?></td>
                <td><?= htmlspecialchars($seller->shop_name ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($seller->name) ?></td>
                <td><?= htmlspecialchars($seller->email) ?></td>
                <td><?= htmlspecialchars($seller->shop_location ?? 'N/A') ?></td>
                <td><?= is_array($shop) ? $shop['product_count'] : 0 ?></td>
                <td>
                    <span class="admin-badge <?= $seller->is_verified ? 'admin-badge-seller' : '' ?>">
                        <?= $seller->is_verified ? 'Verified' : 'Pending' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.table-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>

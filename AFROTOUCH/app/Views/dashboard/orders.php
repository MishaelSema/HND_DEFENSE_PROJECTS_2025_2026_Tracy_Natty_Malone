<div class="dashboard-page-header">
    <h1>Orders</h1>
    <p>Manage your customer orders</p>
</div>

<div style="background: white; border-radius: 12px; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #fafafa;">
            <tr>
                <th style="padding: 15px; text-align: left; font-size: 14px; color: #6D5D57;">Order</th>
                <th style="padding: 15px; text-align: left; font-size: 14px; color: #6D5D57;">Buyer</th>
                <th style="padding: 15px; text-align: left; font-size: 14px; color: #6D5D57;">Date</th>
                <th style="padding: 15px; text-align: left; font-size: 14px; color: #6D5D57;">Total</th>
                <th style="padding: 15px; text-align: left; font-size: 14px; color: #6D5D57;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
            <tr>
                <td colspan="5" style="padding: 40px; text-align: center; color: #6D5D57;">No orders yet</td>
            </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr style="border-top: 1px solid #E8E2DE;">
                    <td style="padding: 15px; font-weight: 600; color: #D35400;"><?= $order->order_number ?></td>
                    <td style="padding: 15px;"><?= $order->buyer_name ?? 'Unknown' ?></td>
                    <td style="padding: 15px; color: #6D5D57;"><?= date('Y-m-d', strtotime($order->created_at ?? 'now')) ?></td>
                    <td style="padding: 15px; font-weight: 600;"><?= number_format($order->total, 0, ',', ' ') ?> XAF</td>
                    <td style="padding: 15px;">
                        <span style="background: #E9F7EF; color: #188038; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                            <?= ucfirst($order->status) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    lucide.createIcons();
</script>

<?php
// admin/admin_reviews.php
require_once '../includes/db.php';

// Delete a review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $id");
    header("Location: admin_reviews.php");
    exit();
}

// Fetch all reviews
$res = $conn->query("SELECT r.*, c.name AS client_name, p.name AS provider_name 
                     FROM reviews r 
                     LEFT JOIN clients c ON r.client_id = c.client_id 
                     LEFT JOIN providers p ON r.provider_id = p.provider_id 
                     ORDER BY p.name ASC, r.created_at DESC");
$grouped_reviews = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $provider_name = $row['provider_name'] ?? 'Unknown Provider';
        if (!isset($grouped_reviews[$provider_name])) {
            $grouped_reviews[$provider_name] = [];
        }
        $grouped_reviews[$provider_name][] = $row;
    }
}

include 'includes/admin_header.php';
?>

<div class="table-container" style="background: transparent; box-shadow: none; padding: 0;">
    <?php if (empty($grouped_reviews)): ?>
        <div style="background: white; padding: 30px; text-align: center; border-radius: 12px; color: #64748b; font-weight: 600;">No reviews found.</div>
    <?php else: ?>
        <?php foreach ($grouped_reviews as $provider => $reviews): ?>
            <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: #1e293b; font-size: 16px;"><i class="fas fa-user-tie" style="color: var(--primary); margin-right: 8px;"></i> <?= htmlspecialchars($provider) ?></h3>
                    <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;"><?= count($reviews) ?> <?= count($reviews) === 1 ? 'Review' : 'Reviews' ?></span>
                </div>
                <div style="width: 100%; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin: 0;">
                        <thead>
                            <tr>
                                <th style="padding: 12px 20px; text-align: left; background: white; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;">Client</th>
                                <th style="padding: 12px 20px; text-align: left; background: white; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;">Rating</th>
                                <th style="padding: 12px 20px; text-align: left; background: white; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;">Comment</th>
                                <th style="padding: 12px 20px; text-align: left; background: white; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;">Date</th>
                                <th style="padding: 12px 20px; text-align: right; background: white; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $a): ?>
                                <tr>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9;"><?= htmlspecialchars($a['client_name'] ?? 'Unknown'); ?></td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; color: #fbbf24; font-size: 1.1rem;"><?= str_repeat('★', intval($a['rating'])) . str_repeat('☆', 5 - intval($a['rating'])) ?></td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #475569; max-width: 300px;"><?= htmlspecialchars($a['content'] ?? ''); ?></td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8;"><?= date('M j, Y', strtotime($a['created_at'])); ?></td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                        <a href="admin_reviews.php?delete=<?= $a['id']; ?>" style="color: var(--danger); text-decoration: none; font-size: 12px; font-weight: bold; background: #fee2e2; padding: 6px 10px; border-radius: 6px;" onclick="return confirm('Delete this review?');"><i class="fas fa-trash"></i> Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


</div> <!-- Close main-content -->
</body>
</html>
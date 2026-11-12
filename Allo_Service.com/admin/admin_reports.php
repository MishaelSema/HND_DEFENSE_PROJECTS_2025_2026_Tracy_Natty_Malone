<?php
// admin/admin_reports.php
require_once '../includes/db.php';

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending'
)");

// Delete a report
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reports WHERE id = $id");
    header("Location: admin_reports.php");
    exit();
}

// Handle dispute resolution
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resolve_dispute'])) {
    $report_id = intval($_POST['report_id']);
    $decision = clean_input($_POST['decision']); // 'client_wins' or 'provider_wins'
    $admin_notes = clean_input($_POST['admin_notes'] ?? '');
    
    // Update report status
    $stmt = $conn->prepare("UPDATE reports SET status = 'reviewed', resolution = ?, admin_notes = ? WHERE id = ?");
    $resolution = $decision === 'client_wins' ? 'Refund client' : 'Dismiss report';
    $stmt->bind_param("ssi", $resolution, $admin_notes, $report_id);
    $stmt->execute();
    
    // If provider wins dispute, add warning to provider
    if ($decision === 'provider_wins') {
        $res = $conn->query("SELECT provider_id FROM reports WHERE id = $report_id");
        if ($row = $res->fetch_assoc()) {
            $prov_id = $row['provider_id'];
            $conn->query("UPDATE providers SET warning_count = COALESCE(warning_count, 0) + 1 WHERE provider_id = $prov_id");
        }
    }
    
    header("Location: admin_reports.php");
    exit();
}

// Add columns safely if they don't exist
$cols = $conn->query("SHOW COLUMNS FROM reports")->fetch_all(MYSQLI_ASSOC);
$existing_cols = array_column($cols, 'Field');

if (!in_array('resolution', $existing_cols)) {
    $conn->query("ALTER TABLE reports ADD COLUMN resolution VARCHAR(50)");
}
if (!in_array('admin_notes', $existing_cols)) {
    $conn->query("ALTER TABLE reports ADD COLUMN admin_notes TEXT");
}
if (!in_array('description', $existing_cols)) {
    $conn->query("ALTER TABLE reports ADD COLUMN description TEXT");
}

// Add warning_count to providers if not exists
$prov_cols = $conn->query("SHOW COLUMNS FROM providers")->fetch_all(MYSQLI_ASSOC);
$prov_existing = array_column($prov_cols, 'Field');
if (!in_array('warning_count', $prov_existing)) {
    $conn->query("ALTER TABLE providers ADD COLUMN warning_count INT DEFAULT 0");
}

// Fetch all reports
$res = $conn->query("SELECT s.*, c.name AS client_name, p.name AS provider_name 
                     FROM reports s 
                     LEFT JOIN clients c ON s.client_id = c.client_id 
                     LEFT JOIN providers p ON s.provider_id = p.provider_id 
                     ORDER BY s.created_at DESC");
$reports = [];
if ($res) {
    while ($row = $res->fetch_assoc()) $reports[] = $row;
}

include 'includes/admin_header.php';
?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Reporter (Client)</th>
                <th>Reported (Provider)</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Status</th>
                <th>Resolution</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr><td colspan="8" style="text-align:center; padding: 20px;">No reports found.</td></tr>
            <?php else: ?>
                <?php foreach ($reports as $sig): ?>
                    <tr>
                        <td><?= $sig['id']; ?></td>
                        <td><?= htmlspecialchars($sig['client_name'] ?? 'Unknown'); ?> (<?= $sig['client_id'] ?>)</td>
                        <td><strong><?= htmlspecialchars($sig['provider_name'] ?? 'Unknown'); ?></strong> (<?= $sig['provider_id'] ?>)</td>
                        <td style="font-size: 14px; max-width: 200px;"><?= htmlspecialchars($sig['reason']); ?></td>
                        <td style="font-size: 12px; color: #888;"><?= date('M j, Y', strtotime($sig['created_at'])); ?></td>
                        <td><span style="background: <?= $sig['status'] == 'pending' ? '#fff3cd' : '#dcfce7'; ?>; color: <?= $sig['status'] == 'pending' ? '#856404' : '#166534'; ?>; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;"><?= htmlspecialchars($sig['status']); ?></span></td>
                        <td style="font-size: 12px;"><?= htmlspecialchars($sig['resolution'] ?? '-'); ?></td>
                        <td>
                            <?php if ($sig['status'] === 'pending'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="report_id" value="<?= $sig['id'] ?>">
                                    <button type="submit" name="decision" value="client_wins" style="background: #22c55e; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; margin-right: 5px;" onclick="return confirm('Refund client and resolve dispute?');">Refund</button>
                                    <button type="submit" name="decision" value="provider_wins" style="background: #64748b; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer;" onclick="return confirm('Dismiss report and warn provider?');">Dismiss</button>
                                </form>
                            <?php endif; ?>
                            <a href="admin_reports.php?delete=<?= $sig['id']; ?>" style="background: var(--danger); color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; margin-left: 5px;" onclick="return confirm('Delete this report?');">X</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div> <!-- Close main-content -->
</body>
</html>
<?php
$current_page = 'orders';
include 'components/layout_start.php';

$orders = [
    ['id' => 'AFT-4701', 'buyer' => 'Maria Fonye', 'date' => '2024-03-15', 'total' => '45,000 XAF', 'status' => 'Shipped', 'email' => 'maria@example.com', 'address' => 'Douala, Cameroon', 'item' => 'Handmade African Cap'],
    ['id' => 'AFT-1398', 'buyer' => 'John Doe', 'date' => '2024-03-16', 'total' => '54,000 XAF', 'status' => 'Shipped', 'email' => 'john@example.com', 'address' => 'Yaoundé, Cameroon', 'item' => 'Beaded Zulu Necklace'],
    ['id' => 'AFT-8813', 'buyer' => 'Claire Dubois', 'date' => '2024-03-12', 'total' => '32,000 XAF', 'status' => 'Delivered', 'email' => 'claire@example.com', 'address' => 'Paris, France', 'item' => 'Woven Sisal Basket'],
    ['id' => 'AFT-4192', 'buyer' => 'Kwesi Mensah', 'date' => '2024-03-17', 'total' => '28,000 XAF', 'status' => 'Processing', 'email' => 'kwesi@example.com', 'address' => 'Accra, Ghana', 'item' => 'Bronze Ife Head'],
    ['id' => 'AFT-4190', 'buyer' => 'Aisha Bello', 'date' => '2024-03-11', 'total' => '65,000 XAF', 'status' => 'Pending', 'email' => 'aisha@example.com', 'address' => 'Lagos, Nigeria', 'item' => 'Ebony Wood Carving'],
    ['id' => 'AFT-4180', 'buyer' => 'Emeka Obi', 'date' => '2024-03-10', 'total' => '30,000 XAF', 'status' => 'Canceled', 'email' => 'emeka@example.com', 'address' => 'Abuja, Nigeria', 'item' => 'Kente Cloth'],
];
?>

<div class="page-header">
    <div class="header-content">
        <h1>Orders</h1>
        <p class="subtitle">Manage and fulfill your customer orders</p>
    </div>
    <div class="header-actions">
        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search orders...">
        </div>
        <select class="form-select">
            <option>All Orders</option>
            <option>Pending</option>
            <option>Processing</option>
            <option>Shipped</option>
            <option>Delivered</option>
            <option>Canceled</option>
        </select>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Buyer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td class="order-id"><?php echo $order['id']; ?></td>
                <td class="buyer-name"><?php echo $order['buyer']; ?></td>
                <td class="order-date"><?php echo $order['date']; ?></td>
                <td class="order-total"><?php echo $order['total']; ?></td>
                <td>
                    <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </td>
                <td class="actions">
                    <button class="btn-view" onclick='showOrderDetails(<?php echo json_encode($order); ?>)'>
                        <i data-lucide="eye"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Order Detail Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h2 id="modalOrderId">AFT-4208</h2>
                <p id="modalOrderDate" class="text-muted">2024-03-15 • 10:15 AM</p>
            </div>
            <button class="btn-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="info-grid">
                <div class="info-section">
                    <label>Buyer</label>
                    <p id="modalBuyerName" class="font-bold"></p>
                    <p id="modalBuyerEmail" class="text-muted"></p>
                </div>
                <div class="info-section">
                    <label>Shipping Address</label>
                    <p id="modalShippingAddress" class="font-bold"></p>
                </div>
            </div>
            
            <div class="items-list">
                <label>Items</label>
                <div class="item-row">
                    <span id="modalItemName"></span>
                    <span id="modalItemPrice" class="font-bold"></span>
                </div>
                <div class="item-total">
                    <span>Total</span>
                    <span id="modalTotal" class="font-bold text-primary"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-primary-lg">Mark Shipped</button>
            <button class="btn-secondary-lg" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<style>
.card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
}

.overflow-hidden { overflow: hidden; }

.orders-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.orders-table th {
    padding: 1rem 1.5rem;
    background: #FAFAFA;
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 500;
    border-bottom: 1px solid var(--border-color);
}

.orders-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #F5F5F5;
    font-size: 0.9rem;
}

.order-id { font-weight: 600; }
.order-total { font-weight: 500; }

.badge {
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.badge-shipped { background: var(--status-shipped); color: var(--status-shipped-text); }
.badge-delivered { background: var(--status-delivered); color: var(--status-delivered-text); }
.badge-processing { background: var(--status-processing); color: var(--status-processing-text); }
.badge-pending { background: #F5F5F5; color: #666; }
.badge-canceled { background: var(--status-canceled); color: var(--status-canceled-text); }

.btn-view {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 4px;
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}

.modal.show { display: flex; }

.modal-content {
    background-color: white;
    padding: 2.5rem;
    border-radius: 24px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.modal-header h2 { font-family: var(--font-heading); font-size: 1.75rem; }

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
}

.modal-body label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.font-bold { font-weight: 600; }
.text-primary { color: var(--primary-color); }

.items-list {
    background: #FAFAFA;
    padding: 1.5rem;
    border-radius: 12px;
}

.item-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.item-total {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.modal-footer {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 2rem;
}

.btn-primary-lg {
    background: #4F46E5; /* Blue as seen in screenshot */
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary-lg {
    background: #FEE2E2;
    color: #DC2626;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
}

.search-box {
    display: flex;
    align-items: center;
    background: white;
    border: 1px solid var(--border-color);
    padding: 0 1rem;
    border-radius: 100px;
}

.search-box input {
    border: none;
    outline: none;
    padding: 0.6rem;
    width: 200px;
}
</style>

<script>
function showOrderDetails(order) {
    document.getElementById('modalOrderId').textContent = order.id;
    document.getElementById('modalOrderDate').textContent = order.date + ' • 10:15 AM';
    document.getElementById('modalBuyerName').textContent = order.buyer;
    document.getElementById('modalBuyerEmail').textContent = order.email;
    document.getElementById('modalShippingAddress').textContent = order.address;
    document.getElementById('modalItemName').textContent = order.item + ' × 1';
    document.getElementById('modalItemPrice').textContent = order.total;
    document.getElementById('modalTotal').textContent = order.total;
    
    document.getElementById('orderModal').classList.add('show');
}

function closeModal() {
    document.getElementById('orderModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'components/layout_end.php'; ?>

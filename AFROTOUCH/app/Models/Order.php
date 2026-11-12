<?php

namespace App\Models;

class Order {
    public int $id;
    public int $buyer_id;
    public string $order_number;
    public float $total;
    public string $status;
    public ?string $shipping_address;
    public string $payment_status;
    public ?string $payment_method;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $buyer_name;
    public ?string $buyer_email;
    
    protected static string $table = 'orders';
    
    public static function all(): array
    {
        $result = \Database::query("SELECT * FROM " . self::$table);
        return self::convertToObjects($result);
    }
    
    public static function find(int $id): ?self
    {
        $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return self::mapToObject($row);
        }
        return null;
    }
    
    public static function findByBuyer(int $buyer_id): array
    {
        try {
            $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE buyer_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $buyer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return self::convertToObjects($result);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public static function findOrFail(int $id): self
    {
        $order = self::find($id);
        if (!$order) {
            throw new \Exception("Order not found");
        }
        return $order;
    }
    
    public static function create(array $data): self
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($values), '?');
        
        $sql = "INSERT INTO " . self::$table . " (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = \Database::prepare($sql);
        
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        
        return self::find(\Database::getLastInsertId());
    }
    
    public function update(array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "$key = ?";
        }
        
        $sql = "UPDATE " . self::$table . " SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = \Database::prepare($sql);
        
        $values = array_values($data);
        $values[] = $this->id;
        
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    public function buyer(): ?User
    {
        return User::find($this->buyer_id);
    }
    
    public static function recentBySeller(int $seller_id, int $limit = 10): array
    {
        try {
            $stmt = \Database::prepare("
                SELECT o.*, u.name as buyer_name, u.email as buyer_email 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                JOIN products p ON oi.product_id = p.id 
                LEFT JOIN users u ON o.buyer_id = u.id
                WHERE p.seller_id = ? 
                GROUP BY o.id 
                ORDER BY o.created_at DESC 
                LIMIT ?
            ");
            $stmt->bind_param("ii", $seller_id, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $order = new self();
                $order->id = (int)$row['id'];
                $order->buyer_id = (int)$row['buyer_id'];
                $order->order_number = $row['order_number'];
                $order->total = (float)$row['total'];
                $order->status = $row['status'];
                $order->shipping_address = $row['shipping_address'] ?? null;
                $order->payment_status = $row['payment_status'];
                $order->created_at = $row['created_at'] ?? null;
                $order->buyer_name = $row['buyer_name'] ?? 'Unknown';
                $order->buyer_email = $row['buyer_email'] ?? '';
                $orders[] = $order;
            }
            return $orders;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public static function countBySeller(int $seller_id): int
    {
        try {
            $stmt = \Database::prepare("
                SELECT COUNT(DISTINCT o.id) as count 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                JOIN products p ON oi.product_id = p.id 
                WHERE p.seller_id = ?
            ");
            $stmt->bind_param("i", $seller_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? (int)$row['count'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public static function totalRevenueBySeller(int $seller_id): float
    {
        try {
            $stmt = \Database::prepare("
                SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE p.seller_id = ?
            ");
            $stmt->bind_param("i", $seller_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? (float)$row['total'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public static function getStatsBySeller(int $seller_id): array
    {
        return [
            'total_orders' => self::countBySeller($seller_id),
            'total_revenue' => self::totalRevenueBySeller($seller_id),
            'categories' => []
        ];
    }
    
    public static function countAll(): int
    {
        try {
            $result = \Database::query("SELECT COUNT(*) as cnt FROM " . self::$table);
            $row = $result->fetch_assoc();
            return $row ? (int)$row['cnt'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public static function totalRevenue(): float
    {
        try {
            $result = \Database::query("SELECT COALESCE(SUM(total), 0) as total FROM " . self::$table . " WHERE payment_status = 'paid'");
            $row = $result->fetch_assoc();
            return $row ? (float)$row['total'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public function items(): array
    {
        return OrderItem::where('order_id', $this->id);
    }
    
    public static function where(string $column, $value): array
    {
        $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return self::convertToObjects($result);
    }
    
    private static function convertToObjects($result): array
    {
        $objects = [];
        while ($row = $result->fetch_assoc()) {
            $objects[] = self::mapToObject($row);
        }
        return $objects;
    }
    
    private static function mapToObject(array $row): self
    {
        $order = new self();
        $order->id = (int)$row['id'];
        $order->buyer_id = (int)$row['buyer_id'];
        $order->order_number = $row['order_number'];
        $order->total = (float)$row['total'];
        $order->status = $row['status'];
        $order->shipping_address = $row['shipping_address'] ?? null;
        $order->payment_status = $row['payment_status'];
        $order->payment_method = $row['payment_method'] ?? null;
        $order->created_at = $row['created_at'] ?? null;
        $order->updated_at = $row['updated_at'] ?? null;
        return $order;
    }
}

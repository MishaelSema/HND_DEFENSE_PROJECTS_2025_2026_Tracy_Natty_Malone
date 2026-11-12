<?php

namespace App\Models;

class OrderItem {
    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $price;
    public ?string $created_at;
    public ?string $updated_at;
    
    protected static string $table = 'order_items';
    
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
    
    public static function where(string $column, $value): array
    {
        $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return self::convertToObjects($result);
    }
    
    public static function findByOrder(int $order_id): array
    {
        return self::where('order_id', $order_id);
    }
    
    public function product(): ?Product
    {
        return Product::find($this->product_id);
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
        $item = new self();
        $item->id = (int)$row['id'];
        $item->order_id = (int)$row['order_id'];
        $item->product_id = (int)$row['product_id'];
        $item->quantity = (int)$row['quantity'];
        $item->price = (float)$row['price'];
        $item->created_at = $row['created_at'] ?? null;
        $item->updated_at = $row['updated_at'] ?? null;
        return $item;
    }
}

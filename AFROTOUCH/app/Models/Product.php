<?php

namespace App\Models;

class Product {
    public int $id;
    public int $seller_id;
    public string $name;
    public ?string $description;
    public float $price;
    public string $category;
    public int $stock;
    public string $status;
    public ?string $image;
    public ?string $created_at;
    public ?string $updated_at;
    
    protected static string $table = 'products';
    
    public static function all(): array
    {
        try {
            $result = \Database::query("SELECT * FROM " . self::$table);
            if (!$result) {
                return [];
            }
            return self::convertToObjects($result);
        } catch (\Exception $e) {
            error_log("Product::all() error: " . $e->getMessage());
            return [];
        }
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
    
    public static function findOrFail(int $id): self
    {
        $product = self::find($id);
        if (!$product) {
            throw new \Exception("Product not found");
        }
        return $product;
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
    
    public function delete(): bool
    {
        $stmt = \Database::prepare("DELETE FROM " . self::$table . " WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }
    
    public static function findBySeller(int $seller_id): array
    {
        try {
            $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE seller_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $seller_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return self::convertToObjects($result);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public static function findActive(?int $seller_id = null): array
    {
        try {
            if ($seller_id) {
                $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE status = 'active' AND seller_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $seller_id);
            } else {
                $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE status = 'active' ORDER BY created_at DESC");
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return self::convertToObjects($result);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public static function countBySeller(int $seller_id): int
    {
        try {
            $stmt = \Database::prepare("SELECT COUNT(*) as count FROM " . self::$table . " WHERE seller_id = ?");
            $stmt->bind_param("i", $seller_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? (int)$row['count'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
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
    
    public function seller(): ?User
    {
        return User::find($this->seller_id);
    }
    
    private static function convertToObjects($result): array
    {
        $objects = [];
        if (!$result) {
            return $objects;
        }
        try {
            while ($row = $result->fetch_assoc()) {
                $objects[] = self::mapToObject($row);
            }
        } catch (\Exception $e) {
            error_log("convertToObjects error: " . $e->getMessage());
        }
        return $objects;
    }
    
    private static function mapToObject(array $row): self
    {
        $product = new self();
        $product->id = (int)$row['id'];
        $product->seller_id = (int)$row['seller_id'];
        $product->name = $row['name'];
        $product->description = $row['description'] ?? null;
        $product->price = (float)$row['price'];
        $product->category = $row['category'];
        $product->stock = (int)$row['stock'];
        $product->status = $row['status'];
        $product->image = $row['image'] ?? null;
        $product->created_at = $row['created_at'] ?? null;
        $product->updated_at = $row['updated_at'] ?? null;
        return $product;
    }
}

<?php

namespace App\Models;

class User {
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role;
    public ?string $phone;
    public ?string $country;
    public ?string $address;
    public ?string $avatar;
    public ?string $shop_name;
    public ?string $shop_description;
    public ?string $shop_logo;
    public ?string $shop_location;
    public bool $is_verified;
    public ?string $created_at;
    public ?string $updated_at;
    
    protected static string $table = 'users';
    
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
    
    public static function findOrFail(int $id): self
    {
        $user = self::find($id);
        if (!$user) {
            throw new \Exception("User not found");
        }
        return $user;
    }
    
    public static function findByEmail(string $email): ?self
    {
        $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return self::mapToObject($row);
        }
        return null;
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
    
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }
    
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public static function findByRole(string $role): array
    {
        try {
            $stmt = \Database::prepare("SELECT * FROM " . self::$table . " WHERE role = ? ORDER BY created_at DESC");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            return self::convertToObjects($result);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public static function count(): int
    {
        try {
            $result = \Database::query("SELECT COUNT(*) as cnt FROM " . self::$table);
            $row = $result->fetch_assoc();
            return $row ? (int)$row['cnt'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public static function countByRole(string $role): int
    {
        try {
            $stmt = \Database::prepare("SELECT COUNT(*) as cnt FROM " . self::$table . " WHERE role = ?");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? (int)$row['cnt'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
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
        $user = new self();
        $user->id = (int)$row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->role = $row['role'];
        $user->phone = $row['phone'] ?? null;
        $user->country = $row['country'] ?? null;
        $user->address = $row['address'] ?? null;
        $user->avatar = $row['avatar'] ?? null;
        $user->shop_name = $row['shop_name'] ?? null;
        $user->shop_description = $row['shop_description'] ?? null;
        $user->shop_logo = $row['shop_logo'] ?? null;
        $user->shop_location = $row['shop_location'] ?? null;
        $user->is_verified = (bool)$row['is_verified'];
        $user->created_at = $row['created_at'] ?? null;
        $user->updated_at = $row['updated_at'] ?? null;
        return $user;
    }
}

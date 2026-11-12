<?php
// includes/ratelimit.php
// Rate limiting functionality

class RateLimiter {
    private $maxAttempts;
    private $lockoutDuration;
    private $storageFile;
    
    public function __construct($maxAttempts = 5, $lockoutMinutes = 3) {
        $this->maxAttempts = $maxAttempts;
        $this->lockoutDuration = $lockoutMinutes * 60;
        $this->storageFile = sys_get_temp_dir() . '/allo_service_ratelimit.json';
    }
    
    private function loadData() {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?: [];
    }
    
    private function saveData($data) {
        file_put_contents($this->storageFile, json_encode($data));
    }
    
    public function isLockedOut($identifier) {
        $data = $this->loadData();
        
        if (!isset($data[$identifier])) {
            return false;
        }
        
        if ($data[$identifier]['attempts'] >= $this->maxAttempts) {
            $lockoutEnd = $data[$identifier]['last_attempt'] + $this->lockoutDuration;
            if (time() < $lockoutEnd) {
                $remaining = ceil(($lockoutEnd - time()) / 60);
                return $remaining;
            }
            $this->clearAttempts($identifier);
            return false;
        }
        
        return false;
    }
    
    public function recordFailedAttempt($identifier) {
        $data = $this->loadData();
        
        if (!isset($data[$identifier])) {
            $data[$identifier] = [
                'attempts' => 0,
                'last_attempt' => 0
            ];
        }
        
        $data[$identifier]['attempts']++;
        $data[$identifier]['last_attempt'] = time();
        
        $this->saveData($data);
        
        if ($data[$identifier]['attempts'] >= $this->maxAttempts) {
            return $this->lockoutDuration / 60;
        }
        
        return 0;
    }
    
    public function clearAttempts($identifier) {
        $data = $this->loadData();
        unset($data[$identifier]);
        $this->saveData($data);
    }
    
    public function getRemainingAttempts($identifier) {
        $data = $this->loadData();
        
        if (!isset($data[$identifier])) {
            return $this->maxAttempts;
        }
        
        $lockoutEnd = $data[$identifier]['last_attempt'] + $this->lockoutDuration;
        if (time() < $lockoutEnd) {
            return 0;
        }
        
        return max(0, $this->maxAttempts - $data[$identifier]['attempts']);
    }
}

function getRateLimiter() {
    return new RateLimiter(5, 3);
}
?>

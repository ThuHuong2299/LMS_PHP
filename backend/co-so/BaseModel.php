<?php
/**
 * File: BaseModel.php
 * Mục đích: Class model cơ sở cho tất cả models
 */

abstract class BaseModel {
    
    /**
     * Chuyển đổi object thành array
     */
    public function toArray() {
        return get_object_vars($this);
    }
    
    /**
     * Chuyển đổi object thành JSON
     */
    public function toJson() {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Tạo object từ array
     */
    public static function tuArray($data) {
        $instance = new static();
        
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }
        
        return $instance;
    }
    
    /**
     * Làm sạch dữ liệu string
     */
    protected function lamSachChuoi($value) {
        if ($value === null) return null;
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return $value;
    }
    
    /**
     * Chuyển đổi sang số nguyên an toàn
     */
    protected function chuyenSangInt($value) {
        return $value !== null ? (int)$value : null;
    }
    
    /**
     * Chuyển đổi sang số thực an toàn
     */
    protected function chuyenSangFloat($value) {
        return $value !== null ? (float)$value : null;
    }
}

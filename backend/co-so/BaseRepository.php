<?php
/**
 * File: BaseRepository.php
 * Mục đích: Class repository cơ sở cho tất cả repositories
 */

abstract class BaseRepository {
    
    protected $db;
    
    public function __construct() {
        $this->db = Database::layKetNoi();
    }
    
    /**
     * Thực thi câu lệnh SQL và trả về tất cả kết quả
     */
    public function truyVan($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->xuLyLoi($e);
            return [];
        }
    }
    
    /**
     * Thực thi câu lệnh SQL và trả về 1 kết quả
     */
    public function truyVanMot($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->xuLyLoi($e);
            return null;
        }
    }
    
    /**
     * Thực thi câu lệnh INSERT/UPDATE/DELETE
     */
    protected function thucThi($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->xuLyLoi($e);
            return false;
        }
    }
    
    /**
     * Lấy ID vừa insert
     */
    protected function layIdVuaInsert() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Đếm số lượng bản ghi
     */
    protected function dem($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['total']) ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            $this->xuLyLoi($e);
            return 0;
        }
    }
    
    /**
     * Bắt đầu transaction
     */
    protected function batDauTransaction() {
        $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    protected function commit() {
        $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    protected function rollback() {
        $this->db->rollBack();
    }
    
    /**
     * Xử lý lỗi database
     */
    protected function xuLyLoi($e) {
        error_log("Lỗi Database [" . get_class($this) . "]: " . $e->getMessage());
        error_log("SQL Error: " . $e->getTraceAsString());
    }
}

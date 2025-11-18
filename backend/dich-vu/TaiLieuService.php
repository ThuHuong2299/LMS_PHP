<?php
/**
 * TaiLieuService.php
 * 
 * Service xử lý business logic cho tài liệu lớp học
 * Chức năng: format kích thước file, validate quyền truy cập, xử lý upload
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/TaiLieuRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class TaiLieuService extends BaseService {
    
    private $taiLieuRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->taiLieuRepo = new TaiLieuRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách tài liệu của lớp học
     * 
     * @param int $lopHocId ID của lớp học
     * @param int $giangVienId ID giảng viên (để kiểm tra quyền)
     * @return array Danh sách tài liệu đã format
     */
    public function layDanhSachTaiLieu($lopHocId, $giangVienId = null) {
        // Kiểm tra quyền truy cập lớp học (nếu có giangVienId)
        if ($giangVienId !== null) {
            $lopHoc = $this->lopHocRepo->timTheoId($lopHocId);
            if (!$lopHoc || $lopHoc['giang_vien_id'] != $giangVienId) {
                throw new Exception('Không có quyền truy cập lớp học này');
            }
        }
        
        // Lấy danh sách tài liệu
        $danhSachTaiLieu = $this->taiLieuRepo->layTheoLopHoc($lopHocId);
        
        // Format dữ liệu cho frontend
        return array_map(function($taiLieu) {
            return [
                'id' => $taiLieu['id'],
                'lop_hoc_id' => $taiLieu['lop_hoc_id'],
                'ten_tai_lieu' => $taiLieu['ten_tai_lieu'],
                'loai_file' => $taiLieu['loai_file'],
                'duong_dan_file' => $taiLieu['duong_dan_file'],
                'ten_file_goc' => $taiLieu['ten_file_goc'],
                'kich_thuoc_file' => $taiLieu['kich_thuoc_file'],
                'kich_thuoc_dinh_dang' => $this->formatKichThuocFile($taiLieu['kich_thuoc_file']),
                'thoi_gian_upload' => $taiLieu['thoi_gian_upload'],
                'nguoi_upload_id' => $taiLieu['nguoi_upload_id'],
                'nguoi_upload_ten' => $taiLieu['nguoi_upload_ten']
            ];
        }, $danhSachTaiLieu);
    }
    
    /**
     * Lấy thông tin chi tiết một tài liệu
     * 
     * @param int $taiLieuId ID của tài liệu
     * @param int $giangVienId ID giảng viên (để kiểm tra quyền)
     * @return array|null Thông tin tài liệu
     */
    public function layChiTietTaiLieu($taiLieuId, $giangVienId = null) {
        $taiLieu = $this->taiLieuRepo->layTheoId($taiLieuId);
        
        if (!$taiLieu) {
            throw new Exception('Không tìm thấy tài liệu');
        }
        
        // Kiểm tra quyền truy cập
        if ($giangVienId !== null) {
            $lopHoc = $this->lopHocRepo->timTheoId($taiLieu['lop_hoc_id']);
            if (!$lopHoc || $lopHoc['giang_vien_id'] != $giangVienId) {
                throw new Exception('Không có quyền truy cập tài liệu này');
            }
        }
        
        // Format và trả về
        $taiLieu['kich_thuoc_dinh_dang'] = $this->formatKichThuocFile($taiLieu['kich_thuoc_file']);
        return $taiLieu;
    }
    
    /**
     * Thêm tài liệu mới (dành cho upload sau này)
     * 
     * @param array $duLieu Dữ liệu tài liệu
     * @param int $giangVienId ID giảng viên upload
     * @return int ID của tài liệu vừa thêm
     */
    public function themTaiLieu($duLieu, $giangVienId) {
        // Kiểm tra quyền upload vào lớp này
        $lopHoc = $this->lopHocRepo->timTheoId($duLieu['lop_hoc_id']);
        if (!$lopHoc || $lopHoc['giang_vien_id'] != $giangVienId) {
            throw new Exception('Không có quyền upload tài liệu vào lớp này');
        }
        
        // Validate loại file
        $loaiFileHopLe = ['pdf', 'docx', 'pptx', 'xlsx'];
        if (!in_array($duLieu['loai_file'], $loaiFileHopLe)) {
            throw new Exception('Loại file không được hỗ trợ');
        }
        
        // Thêm người upload vào dữ liệu
        $duLieu['nguoi_upload_id'] = $giangVienId;
        
        // Thêm vào database
        return $this->taiLieuRepo->them($duLieu);
    }
    
    /**
     * Xóa tài liệu
     * 
     * @param int $taiLieuId ID của tài liệu cần xóa
     * @param int $giangVienId ID giảng viên (kiểm tra quyền)
     * @return bool Kết quả xóa
     */
    public function xoaTaiLieu($taiLieuId, $giangVienId) {
        // Lấy thông tin tài liệu
        $taiLieu = $this->taiLieuRepo->layTheoId($taiLieuId);
        if (!$taiLieu) {
            throw new Exception('Không tìm thấy tài liệu');
        }
        
        // Kiểm tra quyền xóa
        $lopHoc = $this->lopHocRepo->timTheoId($taiLieu['lop_hoc_id']);
        if (!$lopHoc || $lopHoc['giang_vien_id'] != $giangVienId) {
            throw new Exception('Không có quyền xóa tài liệu này');
        }
        
        // Xóa file vật lý nếu tồn tại
        if (!empty($taiLieu['duong_dan_file'])) {
            $duongDanDayDu = $_SERVER['DOCUMENT_ROOT'] . $taiLieu['duong_dan_file'];
            if (file_exists($duongDanDayDu)) {
                @unlink($duongDanDayDu);
            }
        }
        
        // Xóa khỏi database
        return $this->taiLieuRepo->xoa($taiLieuId);
    }
    
    /**
     * Format kích thước file từ bytes sang KB/MB/GB
     * 
     * @param int $bytes Kích thước tính bằng bytes
     * @return string Kích thước đã format (vd: "240 KB", "1.5 MB")
     */
    private function formatKichThuocFile($bytes) {
        if ($bytes == 0) return '0 B';
        
        $donVi = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        // Format với 0 chữ số thập phân cho KB, 1 chữ số cho MB trở lên
        if ($i <= 1) { // B hoặc KB
            return round($bytes / pow(1024, $i), 0) . ' ' . $donVi[$i];
        } else {
            return round($bytes / pow(1024, $i), 1) . ' ' . $donVi[$i];
        }
    }
    
    /**
     * Lấy thống kê tài liệu của giảng viên
     * 
     * @param int $giangVienId ID giảng viên
     * @return array Thống kê
     */
    public function layThongKeTaiLieu($giangVienId) {
        $danhSachLopHoc = $this->lopHocRepo->timTheoGiangVien($giangVienId);
        $tongSoTaiLieu = 0;
        
        foreach ($danhSachLopHoc as $lopHoc) {
            $tongSoTaiLieu += $this->taiLieuRepo->demTheoLopHoc($lopHoc['id']);
        }
        
        return [
            'tong_so_tai_lieu' => $tongSoTaiLieu,
            'so_lop_hoc' => count($danhSachLopHoc)
        ];
    }
}

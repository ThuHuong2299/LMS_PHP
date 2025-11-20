<?php
/**
 * File: BaiKiemTraService.php
 * Mục đích: Xử lý logic nghiệp vụ cho bài kiểm tra
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/BaiKiemTraRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';
require_once __DIR__ . '/../kho-du-lieu/SinhVienRepository.php';

class BaiKiemTraService extends BaseService {
    
    private $baiKiemTraRepo;
    private $lopHocRepo;
    private $sinhVienRepo;
    
    public function __construct() {
        $this->baiKiemTraRepo = new BaiKiemTraRepository();
        $this->lopHocRepo = new LopHocRepository();
        $this->sinhVienRepo = new SinhVienRepository();
    }
    
    /**
     * Lấy danh sách bài kiểm tra theo lớp học
     */
    public function layBaiKiemTraTheoLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy tổng số sinh viên
        $tongSinhVien = $this->baiKiemTraRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách bài kiểm tra
        $danhSach = $this->baiKiemTraRepo->timTheoLopHoc($lopHocId);
        
        // Format dữ liệu
        return array_map(function($bkt) use ($tongSinhVien) {
            return [
                'id' => (int)$bkt['id'],
                'tieu_de' => $bkt['tieu_de'],
                'mo_ta' => $bkt['mo_ta'],
                'thoi_luong' => (int)$bkt['thoi_luong'],
                'thoi_gian_bat_dau' => $bkt['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $bkt['thoi_gian_ket_thuc'],
                'diem_toi_da' => (float)$bkt['diem_toi_da'],
                'so_cau_hoi' => (int)$bkt['so_cau_hoi'],
                'so_sinh_vien_da_lam' => (int)$bkt['so_sinh_vien_da_lam'],
                'tong_sinh_vien' => $tongSinhVien,
                'chuong' => $bkt['chuong_id'] ? [
                    'so_thu_tu' => (int)$bkt['so_thu_tu_chuong'],
                    'ten_chuong' => $bkt['ten_chuong']
                ] : null,
                'ngay_tao' => $bkt['ngay_tao']
            ];
        }, $danhSach);
    }
    
    /**
     * Lấy danh sách bài kiểm tra cho sinh viên
     */
    public function layDanhSachChoSinhVien($lopHocId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Kiểm tra sinh viên có trong lớp không
        if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $lopHocId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy danh sách
        $danhSach = $this->baiKiemTraRepo->layDanhSachChoSinhVien($lopHocId, $sinhVienId);
        
        // Format dữ liệu
        return array_map(function($bkt) {
            $trangThai = $bkt['trang_thai'] ?? 'chua_lam';
            $coTheLam = $this->kiemTraCoTheLam($bkt);
            
            return [
                'id' => (int)$bkt['id'],
                'tieu_de' => $bkt['tieu_de'],
                'mo_ta' => $bkt['mo_ta'],
                'thoi_luong' => (int)$bkt['thoi_luong'],
                'thoi_gian_bat_dau' => $bkt['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $bkt['thoi_gian_ket_thuc'],
                'diem_toi_da' => (float)$bkt['diem_toi_da'],
                'so_cau_hoi' => (int)$bkt['so_cau_hoi'],
                'chuong' => $bkt['chuong_id'] ? [
                    'so_thu_tu' => (int)$bkt['so_thu_tu_chuong'],
                    'ten_chuong' => $bkt['ten_chuong']
                ] : null,
                'trang_thai_lam_bai' => $trangThai,
                'co_the_lam' => $coTheLam,
                'bai_lam_id' => $bkt['bai_lam_id'] ? (int)$bkt['bai_lam_id'] : null,
                'diem' => $bkt['diem'] ? (float)$bkt['diem'] : null,
                'so_cau_dung' => $bkt['so_cau_dung'] ? (int)$bkt['so_cau_dung'] : null,
                'tong_so_cau' => $bkt['tong_so_cau'] ? (int)$bkt['tong_so_cau'] : null,
                'thoi_gian_nop' => $bkt['thoi_gian_nop']
            ];
        }, $danhSach);
    }
    
    /**
     * Kiểm tra có thể làm bài không
     */
    private function kiemTraCoTheLam($bkt) {
        $now = time();
        $batDau = strtotime($bkt['thoi_gian_bat_dau']);
        $ketThuc = strtotime($bkt['thoi_gian_ket_thuc']);
        
        // Nếu đã làm rồi thì không làm lại được
        if ($bkt['trang_thai'] && in_array($bkt['trang_thai'], ['da_nop', 'da_cham'])) {
            return false;
        }
        
        // Nếu đang làm thì tiếp tục làm được
        if ($bkt['trang_thai'] === 'dang_lam') {
            return $now <= $ketThuc;
        }
        
        // Kiểm tra thời gian
        return $now >= $batDau && $now <= $ketThuc;
    }
    
    /**
     * Lấy chi tiết bài kiểm tra cho sinh viên
     */
    public function layChiTietChoSinhVien($baiKiemTraId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiKiemTraId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy thông tin bài kiểm tra
        $baiKiemTra = $this->baiKiemTraRepo->layChiTiet($baiKiemTraId);
        
        if (!$baiKiemTra) {
            $this->nemLoi('Không tìm thấy bài kiểm tra');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $baiKiemTra['lop_hoc_id'])) {
            $this->nemLoi('Bạn không có quyền truy cập bài kiểm tra này');
        }
        
        // Kiểm tra trạng thái bài làm
        $baiLam = $this->baiKiemTraRepo->kiemTraDaLam($baiKiemTraId, $sinhVienId);
        
        $trangThai = $baiLam ? $baiLam['trang_thai'] : 'chua_lam';
        
        // Lấy câu hỏi (ẩn đáp án nếu chưa nộp bài)
        $hienThiDapAn = ($trangThai === 'chua_lam' || $trangThai === 'dang_lam');
        $cauHoi = $this->baiKiemTraRepo->layCauHoi($baiKiemTraId, $hienThiDapAn);
        
        // Format dữ liệu
        $duLieu = [
            'thong_tin_bai_kiem_tra' => [
                'id' => (int)$baiKiemTra['id'],
                'tieu_de' => $baiKiemTra['tieu_de'],
                'mo_ta' => $baiKiemTra['mo_ta'],
                'thoi_luong' => (int)$baiKiemTra['thoi_luong'],
                'thoi_gian_bat_dau' => $baiKiemTra['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $baiKiemTra['thoi_gian_ket_thuc'],
                'diem_toi_da' => (float)$baiKiemTra['diem_toi_da'],
                'cho_phep_lam_lai' => (int)($baiKiemTra['cho_phep_lam_lai'] ?? 0),
                'ten_mon_hoc' => $baiKiemTra['ten_mon_hoc'],
                'ma_lop_hoc' => $baiKiemTra['ma_lop_hoc']
            ],
            'trang_thai_lam_bai' => $trangThai,
            'bai_lam_id' => $baiLam ? (int)$baiLam['id'] : null,
            'thoi_gian_bat_dau_lam' => $baiLam ? $baiLam['thoi_gian_bat_dau'] : null,
            'cau_hoi' => $cauHoi,
            'co_the_lam' => $this->kiemTraCoTheLam(array_merge($baiKiemTra, ['trang_thai' => $trangThai]))
        ];
        
        return $duLieu;
    }
    
    /**
     * Bắt đầu làm bài kiểm tra
     */
    public function batDauLamBai($baiKiemTraId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiKiemTraId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy thông tin bài kiểm tra
        $baiKiemTra = $this->baiKiemTraRepo->layChiTiet($baiKiemTraId);
        
        if (!$baiKiemTra) {
            $this->nemLoi('Không tìm thấy bài kiểm tra');
        }
        
        // Kiểm tra quyền
        if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $baiKiemTra['lop_hoc_id'])) {
            $this->nemLoi('Bạn không có quyền truy cập bài kiểm tra này');
        }
        
        // Kiểm tra đã làm chưa
        $baiLam = $this->baiKiemTraRepo->kiemTraDaLam($baiKiemTraId, $sinhVienId);
        
        if ($baiLam) {
            if ($baiLam['trang_thai'] === 'dang_lam') {
                // Đang làm dở, trả về bài làm hiện tại
                return [
                    'bai_lam_id' => (int)$baiLam['id'],
                    'thoi_gian_bat_dau' => $baiLam['thoi_gian_bat_dau'],
                    'thong_bao' => 'Tiếp tục làm bài'
                ];
            } else {
                // Đã nộp bài rồi
                $choPhepLamLai = (int)$baiKiemTra['cho_phep_lam_lai'];
                
                if ($choPhepLamLai !== 1) {
                    // Không cho phép làm lại
                    $this->nemLoi('Bạn đã làm bài kiểm tra này rồi. Giảng viên chưa cho phép làm lại.');
                }
                
                // Cho phép làm lại - không throw error, tiếp tục tạo bài làm mới bên dưới
                error_log("Sinh viên $sinhVienId được làm lại bài kiểm tra $baiKiemTraId");
            }
        }
        
        // Kiểm tra thời gian
        $now = time();
        $batDau = strtotime($baiKiemTra['thoi_gian_bat_dau']);
        $ketThuc = strtotime($baiKiemTra['thoi_gian_ket_thuc']);
        
        // Debug thời gian
        error_log("DEBUG batDauLamBai:");
        error_log("  - Now: " . date('Y-m-d H:i:s', $now) . " (timestamp: $now)");
        error_log("  - Bắt đầu: " . $baiKiemTra['thoi_gian_bat_dau'] . " (timestamp: $batDau)");
        error_log("  - Kết thúc: " . $baiKiemTra['thoi_gian_ket_thuc'] . " (timestamp: $ketThuc)");
        error_log("  - Now < Bắt đầu? " . ($now < $batDau ? 'YES' : 'NO'));
        error_log("  - Now > Kết thúc? " . ($now > $ketThuc ? 'YES' : 'NO'));
        
        if ($now < $batDau) {
            $this->nemLoi('Bài kiểm tra chưa mở');
        }
        
        if ($now > $ketThuc) {
            $this->nemLoi('Bài kiểm tra đã hết hạn');
        }
        
        // Tạo bài làm mới
        $baiLamId = $this->baiKiemTraRepo->taoBaiLam($baiKiemTraId, $sinhVienId);
        
        return [
            'bai_lam_id' => $baiLamId,
            'thoi_gian_bat_dau' => date('Y-m-d H:i:s'),
            'thong_bao' => 'Bắt đầu làm bài thành công'
        ];
    }
    
    /**
     * Lưu câu trả lời
     */
    public function luuTraLoi($baiLamId, $cauHoiId, $luaChonId, $sinhVienId) {
        error_log("=== LƯU TRẢ LỜI ===");
        error_log("Bài làm ID: $baiLamId");
        error_log("Câu hỏi ID: $cauHoiId");
        error_log("Lựa chọn ID: $luaChonId");
        error_log("Sinh viên ID: $sinhVienId");
        
        // Validate
        if (!$this->kiemTraSoNguyen($baiLamId) || !$this->kiemTraSoNguyen($cauHoiId) || !$this->kiemTraSoNguyen($luaChonId)) {
            error_log("VALIDATE FAILED: Tham số không hợp lệ");
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Kiểm tra bài làm có thuộc sinh viên không
        $baiLam = $this->baiKiemTraRepo->layKetQua($baiLamId);
        error_log("Bài làm: " . json_encode($baiLam));
        
        if (!$baiLam || $baiLam['sinh_vien_id'] != $sinhVienId) {
            error_log("FAILED: Không tìm thấy bài làm hoặc không thuộc sinh viên");
            $this->nemLoi('Không tìm thấy bài làm');
        }
        
        // Kiểm tra trạng thái
        if ($baiLam['trang_thai'] !== 'dang_lam') {
            error_log("FAILED: Trạng thái không hợp lệ - " . $baiLam['trang_thai']);
            $this->nemLoi('Không thể lưu câu trả lời khi bài làm đã nộp');
        }
        
        // Lưu trả lời
        error_log("Đang lưu vào database...");
        $result = $this->baiKiemTraRepo->luuTraLoi($baiLamId, $cauHoiId, $luaChonId);
        error_log("Kết quả lưu: " . ($result ? "SUCCESS" : "FAILED"));
        
        return ['thong_bao' => 'Lưu câu trả lời thành công'];
    }
    
    /**
     * Nộp bài kiểm tra
     */
    public function nopBai($baiLamId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiLamId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Kiểm tra bài làm
        $baiLam = $this->baiKiemTraRepo->layKetQua($baiLamId);
        
        if (!$baiLam || $baiLam['sinh_vien_id'] != $sinhVienId) {
            $this->nemLoi('Không tìm thấy bài làm');
        }
        
        if ($baiLam['trang_thai'] !== 'dang_lam') {
            $this->nemLoi('Bài làm đã được nộp rồi');
        }
        
        // Chấm điểm
        $ketQua = $this->baiKiemTraRepo->chamDiem($baiLamId);
        
        // Tính thời gian làm bài
        $thoiGianBatDau = strtotime($baiLam['thoi_gian_bat_dau']);
        $thoiGianNop = time();
        $thoiGianLamBai = round(($thoiGianNop - $thoiGianBatDau) / 60); // phút
        
        // Cập nhật bài làm
        $this->baiKiemTraRepo->nopBai(
            $baiLamId, 
            $ketQua['so_cau_dung'], 
            $ketQua['tong_diem'],
            $thoiGianLamBai
        );
        
        return [
            'diem' => (float)$ketQua['tong_diem'],
            'so_cau_dung' => (int)$ketQua['so_cau_dung'],
            'tong_so_cau' => (int)$baiLam['tong_so_cau'],
            'thoi_gian_lam_bai' => $thoiGianLamBai,
            'thong_bao' => 'Nộp bài thành công'
        ];
    }
    
    /**
     * Xem kết quả bài kiểm tra
     */
    public function xemKetQua($baiLamId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiLamId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy kết quả đầy đủ
        $baiLam = $this->baiKiemTraRepo->layBaiLamVoiDapAn($baiLamId);
        
        if (!$baiLam || $baiLam['sinh_vien_id'] != $sinhVienId) {
            $this->nemLoi('Không tìm thấy bài làm');
        }
        
        if ($baiLam['trang_thai'] === 'dang_lam') {
            $this->nemLoi('Bạn chưa nộp bài');
        }
        
        // Tính điểm phần trăm
        $diemPhanTram = 0;
        if ($baiLam['tong_so_cau'] > 0) {
            $diemPhanTram = ($baiLam['so_cau_dung'] / $baiLam['tong_so_cau']) * 100;
        }
        
        // Format dữ liệu
        return [
            'thong_tin_bai_lam' => [
                'id' => (int)$baiLam['id'],
                'tieu_de' => $baiLam['tieu_de'],
                'diem' => (float)$baiLam['diem'],
                'diem_toi_da' => (float)$baiLam['diem_toi_da'],
                'diem_phan_tram' => (float)$diemPhanTram,
                'so_cau_dung' => (int)$baiLam['so_cau_dung'],
                'tong_so_cau' => (int)$baiLam['tong_so_cau'],
                'thoi_luong' => (int)$baiLam['thoi_luong'],
                'thoi_gian_bat_dau' => $baiLam['thoi_gian_bat_dau'],
                'thoi_gian_nop' => $baiLam['thoi_gian_nop'],
                'thoi_gian_lam_bai' => (int)$baiLam['thoi_gian_lam_bai']
            ],
            'cau_hoi_va_dap_an' => array_map(function($ch) {
                return [
                    'cau_hoi_id' => (int)$ch['cau_hoi_id'],
                    'thu_tu' => (int)$ch['thu_tu'],
                    'noi_dung' => $ch['noi_dung_cau_hoi'],
                    'diem' => (float)$ch['diem'],
                    'lua_chon_da_chon' => $ch['lua_chon_da_chon'] ? (int)$ch['lua_chon_da_chon'] : null,
                    'dung_hay_sai' => $ch['dung_hay_sai'] ? true : false,
                    'lua_chon' => array_map(function($lc) use ($ch) {
                        return [
                            'id' => (int)$lc['id'],
                            'thu_tu' => (int)$lc['thu_tu'],
                            'noi_dung' => $lc['noi_dung_lua_chon'],
                            'la_dap_an_dung' => $lc['la_dap_an_dung'] ? true : false,
                            'da_chon' => ($lc['id'] == $ch['lua_chon_da_chon'])
                        ];
                    }, $ch['lua_chon'])
                ];
            }, $baiLam['cau_hoi'])
        ];
    }
    
    /**
     * Tạo bài kiểm tra mới với validation đầy đủ
     */
    public function taoBaiKiemTra($duLieu, $giangVienId) {
        // Validate dữ liệu cơ bản
        if (empty($duLieu['lop_hoc_id'])) {
            $this->nemLoi('Vui lòng chọn lớp học');
        }
        
        if (empty($duLieu['tieu_de'])) {
            $this->nemLoi('Vui lòng nhập tiêu đề bài kiểm tra');
        }
        
        if (empty($duLieu['thoi_luong']) || $duLieu['thoi_luong'] <= 0) {
            $this->nemLoi('Thời lượng phải lớn hơn 0');
        }
        
        if (empty($duLieu['thoi_gian_bat_dau'])) {
            $this->nemLoi('Vui lòng chọn thời gian bắt đầu');
        }
        
        // Validate thời gian bắt đầu phải > hiện tại
        $thoiGianBatDau = strtotime($duLieu['thoi_gian_bat_dau']);
        if ($thoiGianBatDau <= time()) {
            $this->nemLoi('Thời gian bắt đầu phải lớn hơn thời gian hiện tại');
        }
        
        // Validate câu hỏi
        if (empty($duLieu['cau_hoi']) || !is_array($duLieu['cau_hoi'])) {
            $this->nemLoi('Vui lòng thêm ít nhất 1 câu hỏi');
        }
        
        $tongDiem = 0;
        foreach ($duLieu['cau_hoi'] as $idx => $cauHoi) {
            // Validate nội dung câu hỏi
            if (empty($cauHoi['noi_dung_cau_hoi'])) {
                $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Vui lòng nhập nội dung');
            }
            
            // Validate lựa chọn
            if (empty($cauHoi['cac_lua_chon']) || !is_array($cauHoi['cac_lua_chon'])) {
                $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Vui lòng thêm lựa chọn');
            }
            
            if (count($cauHoi['cac_lua_chon']) < 2) {
                $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Phải có ít nhất 2 lựa chọn');
            }
            
            // Validate phải có đúng 1 đáp án đúng
            $soDapAnDung = 0;
            foreach ($cauHoi['cac_lua_chon'] as $luaChon) {
                if (empty($luaChon['noi_dung'])) {
                    $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Lựa chọn không được để trống');
                }
                if (!empty($luaChon['dung'])) {
                    $soDapAnDung++;
                }
            }
            
            if ($soDapAnDung === 0) {
                $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Phải có ít nhất 1 đáp án đúng');
            }
            
            if ($soDapAnDung > 1) {
                $this->nemLoi('Câu hỏi ' . ($idx + 1) . ': Chỉ được có 1 đáp án đúng');
            }
            
            $tongDiem += $cauHoi['diem'] ?? 1.0;
        }
        
        // Kiểm tra quyền giảng viên
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($duLieu['lop_hoc_id'], $giangVienId)) {
            $this->nemLoi('Bạn không có quyền tạo bài kiểm tra cho lớp này');
        }
        
        // Bắt đầu transaction
        $this->baiKiemTraRepo->batDauGiaoDich();
        
        try {
            // Tạo bài kiểm tra
            $duLieuBaiKiemTra = [
                'lop_hoc_id' => $duLieu['lop_hoc_id'],
                'chuong_id' => $duLieu['chuong_id'] ?? null,
                'tieu_de' => trim($duLieu['tieu_de']),
                'thoi_luong' => (int)$duLieu['thoi_luong'],
                'thoi_gian_bat_dau' => $duLieu['thoi_gian_bat_dau'],
                'diem_toi_da' => $tongDiem,
                'cho_phep_lam_lai' => $duLieu['cho_phep_lam_lai'] ?? 0
            ];
            
            $baiKiemTraId = $this->baiKiemTraRepo->taoBaiKiemTra($duLieuBaiKiemTra);
            
            // Thêm câu hỏi và lựa chọn
            foreach ($duLieu['cau_hoi'] as $idx => $cauHoi) {
                $duLieuCauHoi = [
                    'thu_tu' => $idx + 1,
                    'noi_dung_cau_hoi' => trim($cauHoi['noi_dung_cau_hoi']),
                    'diem' => $cauHoi['diem'] ?? 1.0
                ];
                
                $cauHoiId = $this->baiKiemTraRepo->themCauHoi($baiKiemTraId, $duLieuCauHoi);
                
                // Thêm các lựa chọn
                foreach ($cauHoi['cac_lua_chon'] as $thuTu => $luaChon) {
                    $this->baiKiemTraRepo->themLuaChon($cauHoiId, [
                        'thu_tu' => $thuTu + 1,
                        'noi_dung' => trim($luaChon['noi_dung']),
                        'dung' => !empty($luaChon['dung'])
                    ]);
                }
            }
            
            // Commit transaction
            $this->baiKiemTraRepo->xacNhanGiaoDich();
            
            return [
                'id' => $baiKiemTraId,
                'tong_diem' => $tongDiem
            ];
            
        } catch (Exception $e) {
            $this->baiKiemTraRepo->huyGiaoDich();
            throw $e;
        }
    }
    
    /**
     * Lấy chi tiết bài kiểm tra kèm thống kê và danh sách sinh viên
     */
    public function layChiTietBaiKiemTra($baiKiemTraId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiKiemTraId)) {
            $this->nemLoi('ID bài kiểm tra không hợp lệ');
        }
        
        // Lấy thông tin bài kiểm tra
        $baiKiemTra = $this->baiKiemTraRepo->layChiTiet($baiKiemTraId);
        if (!$baiKiemTra) {
            $this->nemLoi('Không tìm thấy bài kiểm tra');
        }
        
        // Kiểm tra quyền truy cập (giảng viên phải là người phụ trách lớp)
        $lopHocId = (int)$baiKiemTra['lop_hoc_id'];
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập bài kiểm tra này');
        }
        
        // Lấy thống kê
        $thongKe = $this->baiKiemTraRepo->layThongKeBaiKiemTra($baiKiemTraId);
        
        // Lấy tổng số sinh viên
        $tongSinhVien = $this->baiKiemTraRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách sinh viên
        $danhSachSinhVien = $this->baiKiemTraRepo->layDanhSachSinhVienLamBaiKiemTra($baiKiemTraId, $lopHocId);
        
        // Format dữ liệu trả về
        return [
            'thong_tin_bai_kiem_tra' => [
                'id' => (int)$baiKiemTra['id'],
                'tieu_de' => $baiKiemTra['tieu_de'],
                'mo_ta' => $baiKiemTra['mo_ta'],
                'thoi_luong' => (int)$baiKiemTra['thoi_luong'],
                'thoi_gian_bat_dau' => $baiKiemTra['thoi_gian_bat_dau'],
                'diem_toi_da' => (float)$baiKiemTra['diem_toi_da'],
                'ngay_tao' => $baiKiemTra['ngay_tao'],
                'ten_lop_hoc' => $baiKiemTra['ten_lop_hoc'],
                'ma_lop_hoc' => $baiKiemTra['ma_lop_hoc']
            ],
            'thong_ke' => [
                'so_bai_chua_cham' => (int)($thongKe['so_bai_chua_cham'] ?? 0),
                'so_bai_da_cham' => (int)($thongKe['so_bai_da_cham'] ?? 0),
                'diem_trung_binh' => $thongKe['diem_trung_binh'] ? round((float)$thongKe['diem_trung_binh'], 2) : null,
                'diem_cao_nhat' => $thongKe['diem_cao_nhat'] ? (float)$thongKe['diem_cao_nhat'] : null,
                'diem_thap_nhat' => $thongKe['diem_thap_nhat'] ? (float)$thongKe['diem_thap_nhat'] : null,
                'so_bai_da_nop' => (int)($thongKe['so_bai_da_nop'] ?? 0),
                'tong_sinh_vien' => $tongSinhVien
            ],
            'danh_sach_sinh_vien' => array_map(function($sv) {
                return [
                    'sinh_vien_id' => (int)$sv['sinh_vien_id'],
                    'ho_ten' => $sv['ho_ten'],
                    'ma_sinh_vien' => $sv['ma_sinh_vien'],
                    'anh_dai_dien' => $sv['anh_dai_dien'],
                    'trang_thai' => $sv['trang_thai'],
                    'diem' => $sv['diem'] !== null ? (float)$sv['diem'] : null,
                    'thoi_gian_nop' => $sv['thoi_gian_nop'],
                    'thoi_gian_bat_dau' => $sv['thoi_gian_bat_dau'],
                    'thoi_gian_lam_bai' => $sv['thoi_gian_lam_bai'],
                    'trang_thai_cham' => $sv['trang_thai_cham']
                ];
            }, $danhSachSinhVien)
        ];
    }
}

<?php
/**
 * File: BaiTapService.php
 * Mục đích: Xử lý logic nghiệp vụ cho bài tập
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/BaiTapRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class BaiTapService extends BaseService {
    
    private $baiTapRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->baiTapRepo = new BaiTapRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách bài tập theo lớp học
     */
    public function layBaiTapTheoLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy tổng số sinh viên
        $tongSinhVien = $this->baiTapRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách bài tập
        $danhSach = $this->baiTapRepo->timTheoLopHoc($lopHocId);
        
        // Format dữ liệu
        return array_map(function($bt) use ($tongSinhVien) {
            return [
                'id' => (int)$bt['id'],
                'tieu_de' => $bt['tieu_de'],
                'mo_ta' => $bt['mo_ta'],
                'han_nop' => $bt['han_nop'],
                'diem_toi_da' => (float)$bt['diem_toi_da'],
                'so_cau_hoi' => (int)$bt['so_cau_hoi'],
                'so_sinh_vien_da_nop' => (int)$bt['so_sinh_vien_da_nop'],
                'tong_sinh_vien' => $tongSinhVien,
                'chuong' => [
                    'so_thu_tu' => (int)$bt['so_thu_tu_chuong'],
                    'ten_chuong' => $bt['ten_chuong']
                ],
                'bai_giang' => $bt['bai_giang_id'] ? [
                    'so_thu_tu_bai' => (int)$bt['so_thu_tu_bai'],
                    'tieu_de' => $bt['ten_bai_giang']
                ] : null,
                'ngay_tao' => $bt['ngay_tao']
            ];
        }, $danhSach);
    }
    
    /**
     * Lấy chi tiết bài tập kèm thống kê và danh sách sinh viên
     */
    public function layChiTietBaiTap($baiTapId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiTapId)) {
            $this->nemLoi('ID bài tập không hợp lệ');
        }
        
        // Lấy thông tin bài tập
        $baiTap = $this->baiTapRepo->timTheoId($baiTapId);
        if (!$baiTap) {
            $this->nemLoi('Không tìm thấy bài tập');
        }
        
        // Kiểm tra quyền truy cập
        if ((int)$baiTap['giang_vien_id'] !== (int)$giangVienId) {
            $this->nemLoi('Bạn không có quyền truy cập bài tập này');
        }
        
        $lopHocId = (int)$baiTap['lop_hoc_id'];
        
        // Lấy thống kê
        $thongKe = $this->baiTapRepo->layThongKeBaiTap($baiTapId);
        
        // Lấy tổng số sinh viên
        $tongSinhVien = $this->baiTapRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách sinh viên
        $danhSachSinhVien = $this->baiTapRepo->layDanhSachSinhVienLamBai($baiTapId, $lopHocId);
        
        // Format dữ liệu trả về
        return [
            'thong_tin_bai_tap' => [
                'id' => (int)$baiTap['id'],
                'tieu_de' => $baiTap['tieu_de'],
                'mo_ta' => $baiTap['mo_ta'],
                'han_nop' => $baiTap['han_nop'],
                'diem_toi_da' => (float)$baiTap['diem_toi_da'],
                'ngay_tao' => $baiTap['ngay_tao'],
                'ten_lop_hoc' => $baiTap['ten_lop_hoc']
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
                    'trang_thai_cham' => $sv['trang_thai_cham']
                ];
            }, $danhSachSinhVien)
        ];
    }
    
    /**
     * Lấy chi tiết bài làm của sinh viên
     */
    public function layChiTietBaiLamSinhVien($baiTapId, $sinhVienId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy thông tin bài tập để kiểm tra quyền
        $baiTap = $this->baiTapRepo->timTheoId($baiTapId);
        if (!$baiTap) {
            $this->nemLoi('Không tìm thấy bài tập');
        }
        
        // Kiểm tra quyền truy cập
        if ((int)$baiTap['giang_vien_id'] !== (int)$giangVienId) {
            $this->nemLoi('Bạn không có quyền truy cập bài tập này');
        }
        
        // Lấy chi tiết bài làm
        $baiLam = $this->baiTapRepo->layChiTietBaiLam($baiTapId, $sinhVienId);
        
        // Nếu sinh viên chưa làm bài
        if (!$baiLam) {
            // Lấy thông tin sinh viên
            $sv = $this->baiTapRepo->layThongTinSinhVien($sinhVienId);
            
            if (!$sv) {
                $this->nemLoi('Không tìm thấy sinh viên');
            }
            
            return [
                'bai_lam' => null,
                'sinh_vien' => [
                    'id' => (int)$sv['id'],
                    'ho_ten' => $sv['ho_ten'],
                    'ma_sinh_vien' => $sv['ma_nguoi_dung'],
                    'anh_dai_dien' => $sv['anh_dai_dien']
                ],
                'bai_tap' => [
                    'id' => (int)$baiTap['id'],
                    'tieu_de' => $baiTap['tieu_de'],
                    'mo_ta' => $baiTap['mo_ta'],
                    'han_nop' => $baiTap['han_nop'],
                    'diem_toi_da' => (float)$baiTap['diem_toi_da']
                ],
                'cau_hoi' => [],
                'binh_luan' => []
            ];
        }
        
        $baiLamId = (int)$baiLam['bai_lam_id'];
        
        // Lấy câu hỏi và trả lời
        $cauHoiTraLoi = $this->baiTapRepo->layCauHoiVaTraLoi($baiTapId, $baiLamId);
        
        // Lấy bình luận
        $binhLuan = $this->baiTapRepo->layBinhLuan($baiLamId);
        
        // Format dữ liệu trả về
        return [
            'bai_lam' => [
                'id' => $baiLamId,
                'trang_thai' => $baiLam['trang_thai'],
                'tong_diem' => $baiLam['tong_diem'] !== null ? (float)$baiLam['tong_diem'] : null,
                'thoi_gian_bat_dau' => $baiLam['thoi_gian_bat_dau'],
                'thoi_gian_nop' => $baiLam['thoi_gian_nop'],
                'thoi_gian_cham' => $baiLam['thoi_gian_cham']
            ],
            'sinh_vien' => [
                'id' => (int)$baiLam['sinh_vien_id'],
                'ho_ten' => $baiLam['sinh_vien_ho_ten'],
                'ma_sinh_vien' => $baiLam['sinh_vien_ma'],
                'anh_dai_dien' => $baiLam['sinh_vien_anh']
            ],
            'bai_tap' => [
                'id' => (int)$baiLam['bai_tap_id'],
                'tieu_de' => $baiLam['bai_tap_tieu_de'],
                'mo_ta' => $baiLam['bai_tap_mo_ta'],
                'han_nop' => $baiLam['han_nop'],
                'diem_toi_da' => (float)$baiLam['diem_toi_da']
            ],
            'cau_hoi' => array_map(function($ch) {
                return [
                    'cau_hoi_id' => (int)$ch['cau_hoi_id'],
                    'noi_dung' => $ch['noi_dung_cau_hoi'],
                    'mo_ta' => $ch['cau_hoi_mo_ta'],
                    'diem_toi_da' => (float)$ch['diem_toi_da'],
                    'tra_loi' => $ch['tra_loi_id'] ? [
                        'id' => (int)$ch['tra_loi_id'],
                        'noi_dung' => $ch['noi_dung_tra_loi'],
                        'diem' => $ch['diem_dat_duoc'] !== null ? (float)$ch['diem_dat_duoc'] : null,
                        'thoi_gian' => $ch['thoi_gian_tra_loi']
                    ] : null
                ];
            }, $cauHoiTraLoi),
            'binh_luan' => array_map(function($bl) {
                return [
                    'id' => (int)$bl['id'],
                    'noi_dung' => $bl['noi_dung'],
                    'thoi_gian_gui' => $bl['thoi_gian_gui'],
                    'nguoi_gui' => [
                        'id' => (int)$bl['nguoi_gui_id'],
                        'ho_ten' => $bl['nguoi_gui_ten'],
                        'anh_dai_dien' => $bl['nguoi_gui_anh'],
                        'vai_tro' => $bl['nguoi_gui_vai_tro']
                    ]
                ];
            }, $binhLuan)
        ];
    }
}

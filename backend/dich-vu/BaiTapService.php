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
            
            // Lấy danh sách câu hỏi của bài tập (dù sinh viên chưa làm)
            $cauHoiBaiTap = $this->baiTapRepo->layCauHoiBaiTap($baiTapId);
            
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
                'cau_hoi' => array_map(function($ch) {
                    return [
                        'cau_hoi_id' => (int)$ch['id'],
                        'noi_dung' => $ch['noi_dung_cau_hoi'],
                        'mo_ta' => $ch['mo_ta'],
                        'diem_toi_da' => (float)$ch['diem'],
                        'tra_loi' => null // Sinh viên chưa trả lời
                    ];
                }, $cauHoiBaiTap),
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
    
    // ========================================
    // METHODS CHO SINH VIÊN LÀM BÀI TẬP
    // ========================================
    
    /**
     * Lấy chi tiết bài tập + câu hỏi + bài làm của sinh viên
     */
    public function layChiTietBaiTapChoSinhVien($baiTapId, $sinhVienId) {
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy thông tin bài tập
        $baiTap = $this->baiTapRepo->layChiTietBaiTap($baiTapId);
        if (!$baiTap) {
            $this->nemLoi('Không tìm thấy bài tập');
        }
        
        // Lấy danh sách câu hỏi
        $cauHoi = $this->baiTapRepo->layCauHoiBaiTap($baiTapId);
        
        // Lấy hoặc tạo bài làm
        $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        
        if (!$baiLam) {
            // Tạo bài làm mới
            $baiLamId = $this->baiTapRepo->taoBaiLam($baiTapId, $sinhVienId);
            $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        }
        
        // Lấy các câu trả lời đã lưu
        $traLoi = [];
        if ($baiLam) {
            $traLoiRaw = $this->baiTapRepo->layTraLoiDaLuu($baiLam['id']);
            foreach ($traLoiRaw as $tl) {
                $traLoi[$tl['cau_hoi_id']] = [
                    'noi_dung_tra_loi' => $tl['noi_dung_tra_loi'],
                    'thoi_gian_tra_loi' => $tl['thoi_gian_tra_loi'],
                    'diem' => $tl['diem']
                ];
            }
        }
        
        return [
            'bai_tap' => [
                'id' => (int)$baiTap['id'],
                'tieu_de' => $baiTap['tieu_de'],
                'mo_ta' => $baiTap['mo_ta'],
                'han_nop' => $baiTap['han_nop'],
                'diem_toi_da' => (float)$baiTap['diem_toi_da']
            ],
            'cau_hoi' => array_map(function($ch) use ($traLoi) {
                $traLoiData = isset($traLoi[$ch['id']]) ? $traLoi[$ch['id']] : null;
                return [
                    'id' => (int)$ch['id'],
                    'noi_dung_cau_hoi' => $ch['noi_dung_cau_hoi'],
                    'mo_ta' => $ch['mo_ta'],
                    'diem' => (float)$ch['diem'],
                    'tra_loi_da_luu' => $traLoiData ? $traLoiData['noi_dung_tra_loi'] : '',
                    'diem_dat_duoc' => $traLoiData && isset($traLoiData['diem']) ? (float)$traLoiData['diem'] : null
                ];
            }, $cauHoi),
            'bai_lam' => [
                'id' => (int)$baiLam['id'],
                'trang_thai' => $baiLam['trang_thai'],
                'thoi_gian_bat_dau' => $baiLam['thoi_gian_bat_dau'],
                'thoi_gian_nop' => $baiLam['thoi_gian_nop'],
                'diem' => $baiLam['diem']
            ]
        ];
    }
    
    /**
     * Lưu câu trả lời của sinh viên
     */
    public function luuTraLoiBaiTap($baiTapId, $sinhVienId, $traLoiList) {
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        if (!is_array($traLoiList) || empty($traLoiList)) {
            $this->nemLoi('Dữ liệu trả lời không hợp lệ');
        }
        
        // Lấy hoặc tạo bài làm
        $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        
        if (!$baiLam) {
            $baiLamId = $this->baiTapRepo->taoBaiLam($baiTapId, $sinhVienId);
        } else {
            $baiLamId = $baiLam['id'];
            
            // Kiểm tra đã nộp chưa
            if ($baiLam['trang_thai'] === 'da_nop' || $baiLam['trang_thai'] === 'da_cham') {
                $this->nemLoi('Bài tập đã được nộp, không thể chỉnh sửa');
            }
        }
        
        // Lưu từng câu trả lời
        foreach ($traLoiList as $traLoi) {
            if (!isset($traLoi['cau_hoi_id']) || !isset($traLoi['noi_dung_tra_loi'])) {
                continue;
            }
            
            $cauHoiId = (int)$traLoi['cau_hoi_id'];
            $noiDungTraLoi = trim($traLoi['noi_dung_tra_loi']);
            
            // Lưu hoặc cập nhật
            $this->baiTapRepo->luuTraLoi($baiLamId, $cauHoiId, $noiDungTraLoi);
        }
        
        return true;
    }
    
    /**
     * Nộp bài tập
     */
    public function nopBaiTap($baiTapId, $sinhVienId) {
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Lấy bài làm
        $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        
        if (!$baiLam) {
            $this->nemLoi('Chưa có bài làm');
        }
        
        if ($baiLam['trang_thai'] === 'da_nop' || $baiLam['trang_thai'] === 'da_cham') {
            $this->nemLoi('Bài tập đã được nộp trước đó');
        }
        
        // Nộp bài
        $result = $this->baiTapRepo->nopBaiTap($baiLam['id']);
        
        if (!$result) {
            $this->nemLoi('Không thể nộp bài');
        }
        
        // Đếm số câu đã trả lời
        $thongKe = $this->baiTapRepo->demSoCauTraLoi($baiLam['id'], $baiTapId);
        
        return [
            'thoi_gian_nop' => date('Y-m-d H:i:s'),
            'so_cau_da_tra_loi' => (int)$thongKe['so_cau_da_tra_loi'],
            'tong_so_cau' => (int)$thongKe['tong_so_cau']
        ];
    }
    
    /**
     * Lấy bình luận của câu hỏi
     */
    public function layBinhLuanCauHoi($baiTapId, $cauHoiId, $sinhVienId) {
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($cauHoiId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập (sinh viên phải có bài làm)
        $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        
        if (!$baiLam) {
            $this->nemLoi('Bạn chưa có bài làm cho bài tập này');
        }
        
        // Lấy bình luận
        $binhLuan = $this->baiTapRepo->layBinhLuan($baiLam['id'], $cauHoiId);
        
        return array_map(function($bl) {
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
        }, $binhLuan);
    }
    
    /**
     * Thêm bình luận cho câu hỏi
     */
    public function themBinhLuanCauHoi($baiTapId, $cauHoiId, $sinhVienId, $noiDung) {
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($cauHoiId) || !$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        if (empty(trim($noiDung))) {
            $this->nemLoi('Nội dung bình luận không được để trống');
        }
        
        // Kiểm tra quyền truy cập
        $baiLam = $this->baiTapRepo->layBaiLamSinhVien($baiTapId, $sinhVienId);
        
        if (!$baiLam) {
            $this->nemLoi('Bạn chưa có bài làm cho bài tập này');
        }
        
        // Thêm bình luận
        $binhLuanId = $this->baiTapRepo->themBinhLuan($baiLam['id'], $sinhVienId, $noiDung, $cauHoiId);
        
        if (!$binhLuanId) {
            $this->nemLoi('Không thể thêm bình luận');
        }
        
        return [
            'id' => $binhLuanId,
            'thoi_gian_gui' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Tạo bài tập mới với danh sách câu hỏi
     */
    public function taoBaiTap($lopHocId, $giangVienId, $chuongId, $tieuDe, $hanNop, $cauHoiList) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($chuongId)) {
            $this->nemLoi('ID chương không hợp lệ');
        }
        
        if (empty(trim($tieuDe))) {
            $this->nemLoi('Tiêu đề không được để trống');
        }
        
        if (empty($hanNop)) {
            $this->nemLoi('Hạn nộp không được để trống');
        }
        
        // Validate thời gian
        $hanNopTime = strtotime($hanNop);
        if ($hanNopTime === false || $hanNopTime <= time()) {
            $this->nemLoi('Hạn nộp phải là thời điểm trong tương lai');
        }
        
        if (empty($cauHoiList) || !is_array($cauHoiList)) {
            $this->nemLoi('Bài tập phải có ít nhất 1 câu hỏi');
        }
        
        // Kiểm tra quyền
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền tạo bài tập cho lớp này');
        }
        
        // Tính tổng điểm
        $tongDiem = 0;
        foreach ($cauHoiList as $index => $cauHoi) {
            if (empty(trim($cauHoi['noi_dung'] ?? ''))) {
                $this->nemLoi("Câu hỏi " . ($index + 1) . " không được để trống");
            }
            $tongDiem += floatval($cauHoi['diem'] ?? 0);
        }
        
        if ($tongDiem <= 0) {
            $tongDiem = 10; // Mặc định 10 điểm
        }
        
        // 1. Tạo bài tập
        $duLieuBaiTap = [
            'lop_hoc_id' => $lopHocId,
            'chuong_id' => $chuongId,
            'tieu_de' => trim($tieuDe),
            'mo_ta' => null,
            'han_nop' => $hanNop,
            'diem_toi_da' => $tongDiem
        ];
        
        $ketQua = $this->baiTapRepo->taoBaiTap($duLieuBaiTap);
        
        if (!$ketQua) {
            $this->nemLoi('Không thể tạo bài tập');
        }
        
        $baiTapId = $this->baiTapRepo->layIdVuaThem();
        
        // 2. Thêm các câu hỏi
        foreach ($cauHoiList as $index => $cauHoi) {
            $duLieuCauHoi = [
                'noi_dung' => trim($cauHoi['noi_dung']),
                'mo_ta' => isset($cauHoi['mo_ta']) ? trim($cauHoi['mo_ta']) : null,
                'diem' => floatval($cauHoi['diem'] ?? ($tongDiem / count($cauHoiList)))
            ];
            
            $ketQuaCauHoi = $this->baiTapRepo->themCauHoi($baiTapId, $duLieuCauHoi);
            
            if (!$ketQuaCauHoi) {
                $this->nemLoi('Không thể thêm câu hỏi ' . ($index + 1));
            }
        }
        
        return [
            'id' => $baiTapId,
            'thong_bao' => 'Đã tạo bài tập thành công'
        ];
    }
    
    /**
     * Chấm điểm câu hỏi
     */
    public function chamDiemCauHoi($traLoiId, $diem, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($traLoiId)) {
            $this->nemLoi('ID trả lời không hợp lệ');
        }
        
        if (!is_numeric($diem) || $diem < 0) {
            $this->nemLoi('Điểm không hợp lệ');
        }
        
        // Lấy thông tin trả lời
        $traLoi = $this->baiTapRepo->layThongTinTraLoi($traLoiId);
        if (!$traLoi) {
            $this->nemLoi('Không tìm thấy trả lời');
        }
        
        // Kiểm tra quyền: giảng viên phải là chủ lớp
        $baiTapId = $traLoi['bai_tap_id'];
        $lopHocId = $this->baiTapRepo->layLopHocIdTheoBaiTap($baiTapId);
        
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền chấm bài tập này');
        }
        
        // Kiểm tra điểm không vượt quá điểm tối đa
        $cauHoi = $this->baiTapRepo->layCauHoi($traLoi['cau_hoi_id']);
        if ($diem > $cauHoi['diem']) {
            $this->nemLoi('Điểm không được vượt quá ' . $cauHoi['diem']);
        }
        
        // Cập nhật điểm
        $ketQua = $this->baiTapRepo->capNhatDiemTraLoi($traLoiId, $diem);
        
        if (!$ketQua) {
            $this->nemLoi('Không thể cập nhật điểm');
        }
        
        // Tính lại tổng điểm bài làm
        $baiLamId = $traLoi['bai_lam_id'];
        $this->baiTapRepo->capNhatTongDiemBaiLam($baiLamId);
        
        return [
            'tra_loi_id' => $traLoiId,
            'diem' => $diem,
            'bai_lam_id' => $baiLamId
        ];
    }
    
    /**
     * Lấy danh sách bình luận theo câu hỏi - Giảng viên
     */
    public function layBinhLuanCauHoiGiangVien($baiTapId, $sinhVienId, $cauHoiId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId) || !$this->kiemTraSoNguyen($cauHoiId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        // Kiểm tra quyền: GV phải là chủ lớp
        $lopHocId = $this->baiTapRepo->layLopHocIdTheoBaiTap($baiTapId);
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập bài tập này');
        }
        
        // Lấy bài làm
        $baiLam = $this->baiTapRepo->layChiTietBaiLam($baiTapId, $sinhVienId);
        if (!$baiLam) {
            $this->nemLoi('Sinh viên chưa làm bài tập này');
        }
        
        $baiLamId = $baiLam['bai_lam_id'];
        
        // Lấy bình luận theo câu hỏi
        $binhLuan = $this->baiTapRepo->layBinhLuan($baiLamId, $cauHoiId);
        
        // Format dữ liệu
        return array_map(function($bl) {
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
        }, $binhLuan);
    }
    
    /**
     * Gửi bình luận mới - Giảng viên
     */
    public function guiBinhLuanCauHoi($baiTapId, $sinhVienId, $cauHoiId, $noiDung, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiTapId) || !$this->kiemTraSoNguyen($sinhVienId) || !$this->kiemTraSoNguyen($cauHoiId)) {
            $this->nemLoi('Tham số không hợp lệ');
        }
        
        if (empty(trim($noiDung))) {
            $this->nemLoi('Nội dung bình luận không được để trống');
        }
        
        // Kiểm tra quyền
        $lopHocId = $this->baiTapRepo->layLopHocIdTheoBaiTap($baiTapId);
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập bài tập này');
        }
        
        // Lấy bài làm
        $baiLam = $this->baiTapRepo->layChiTietBaiLam($baiTapId, $sinhVienId);
        if (!$baiLam) {
            $this->nemLoi('Sinh viên chưa làm bài tập này');
        }
        
        $baiLamId = $baiLam['bai_lam_id'];
        
        // Thêm bình luận
        $ketQua = $this->baiTapRepo->themBinhLuan($baiLamId, $giangVienId, $noiDung, $cauHoiId);
        
        if (!$ketQua) {
            $this->nemLoi('Không thể gửi bình luận');
        }
        
        return [
            'id' => $this->baiTapRepo->layIdVuaThem(),
            'bai_lam_id' => $baiLamId,
            'cau_hoi_id' => $cauHoiId
        ];
    }
}

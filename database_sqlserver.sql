-- ============================================================================
-- SQL SERVER DDL & SEED DATA - IFPRO RISK COMMAND CENTER (MENRISK)
-- Database: AIS_2_Mar2026v3
-- Menggunakan tabel eksisting:
--   - User      : dbo.Login (UserID, UserName, PasswordCI, Active)
--   - Department: dbo.organisation_d_3s (id, d_3_name, d_3_desc)
-- Tabel spesifik aplikasi diawali prefix MNRSK_:
--   - dbo.MNRSK_user_departments
--   - dbo.MNRSK_risks
--   - dbo.MNRSK_kri
-- ============================================================================

-- 1. Drop existing MNRSK tables if exists (Urutan: child -> parent)
IF OBJECT_ID('dbo.MNRSK_user_departments', 'U') IS NOT NULL DROP TABLE dbo.MNRSK_user_departments;
IF OBJECT_ID('dbo.MNRSK_risks', 'U') IS NOT NULL DROP TABLE dbo.MNRSK_risks;
IF OBJECT_ID('dbo.MNRSK_kri', 'U') IS NOT NULL DROP TABLE dbo.MNRSK_kri;
GO

-- 2. Create Table: MNRSK_user_departments (Relasi UserID [dbo.Login] -> id [dbo.organisation_d_3s])
CREATE TABLE dbo.MNRSK_user_departments (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id NVARCHAR(30) NOT NULL,
    department_id BIGINT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_MNRSK_user_dept_user FOREIGN KEY (user_id) REFERENCES dbo.Login(UserID) ON DELETE CASCADE,
    CONSTRAINT FK_MNRSK_user_dept_dept FOREIGN KEY (department_id) REFERENCES dbo.organisation_d_3s(id) ON DELETE CASCADE,
    CONSTRAINT UQ_MNRSK_user_dept UNIQUE (user_id, department_id)
);
GO

-- 3. Create Table: MNRSK_risks (Merelasi ke dbo.organisation_d_3s via department_id)
CREATE TABLE dbo.MNRSK_risks (
    id VARCHAR(20) NOT NULL PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,
    owner NVARCHAR(100) NOT NULL,
    department_id BIGINT NULL, -- Foreign Key ke dbo.organisation_d_3s(id)
    dir NVARCHAR(100) NULL, -- Fallback display nama divisi
    cat NVARCHAR(100) NOT NULL,
    inherent INT NOT NULL DEFAULT 0,
    residual INT NOT NULL DEFAULT 0,
    appetite NVARCHAR(50) NOT NULL,
    progress INT NOT NULL DEFAULT 0,
    due NVARCHAR(50) NULL,
    decision NVARCHAR(MAX) NULL,
    mitigation NVARCHAR(MAX) NULL,
    period NVARCHAR(50) NOT NULL DEFAULT 'September 2026',
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    updated_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_MNRSK_risks_department FOREIGN KEY (department_id) REFERENCES dbo.organisation_d_3s(id) ON DELETE SET NULL
);
GO

-- 4. Create Table: MNRSK_kri (Key Risk Indicators - Merelasi ke dbo.organisation_d_3s via department_id)
CREATE TABLE dbo.MNRSK_kri (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    indicator NVARCHAR(150) NOT NULL,
    value NVARCHAR(50) NOT NULL,
    threshold NVARCHAR(100) NOT NULL,
    department_id BIGINT NULL, -- Foreign Key ke dbo.organisation_d_3s(id)
    directorate NVARCHAR(100) NULL, -- Fallback display nama divisi
    status NVARCHAR(50) NOT NULL, -- 'Kritis', 'Waspada', 'Normal'
    period NVARCHAR(50) NOT NULL DEFAULT 'September 2026',
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_MNRSK_kri_department FOREIGN KEY (department_id) REFERENCES dbo.organisation_d_3s(id) ON DELETE SET NULL
);
GO

-- ============================================================================
-- SEED INITIAL DATA (Merujuk ke id dbo.organisation_d_3s & UserID dbo.Login)
-- ============================================================================

-- A. Seed Relasi User - Department (Many-to-Many)
INSERT INTO dbo.MNRSK_user_departments (user_id, department_id)
SELECT 'admin', id FROM dbo.organisation_d_3s;
GO

INSERT INTO dbo.MNRSK_user_departments (user_id, department_id)
SELECT u.UserID, d.id
FROM dbo.Login u
CROSS JOIN dbo.organisation_d_3s d
WHERE u.UserID IN ('a.fikri', 'abrori', 'admin1')
  AND d.id IN (4, 14, 16, 20)
  AND NOT EXISTS (
      SELECT 1 FROM dbo.MNRSK_user_departments ud
      WHERE ud.user_id = u.UserID AND ud.department_id = d.id
  );
GO

-- B. Seed MNRSK_risks (Merelasi ke dbo.organisation_d_3s: 16=IT, 4=Marketing & Leasing, 20=Finance & Treasury, 14=HSE & Facility, 17=Corporate Secretary, 13=Construction & Fit Out, 15=HR & GA)
INSERT INTO dbo.MNRSK_risks (id, title, owner, department_id, dir, cat, inherent, residual, appetite, progress, due, decision, mitigation, period)
VALUES 
('R-001', N'Gangguan layanan sistem kritikal dan serangan siber', N'Manager IT', 16, N'IT', N'Teknologi', 25, 20, N'Melebihi', 42, N'30 Sep 2026', N'Persetujuan percepatan implementasi DR site dan managed SOC.', N'Implementasi DR site, EDR endpoint, SOC 24/7 dan simulasi incident response.', N'September 2026'),
('R-002', N'Target okupansi Sosoro Mall tidak tercapai', N'VP Komersial', 4, N'Marketing & Leasing', N'Strategis', 20, 16, N'Melebihi', 55, N'15 Okt 2026', N'Persetujuan skema commercial incentive untuk anchor tenant.', N'Repricing tenant mix, akuisisi anchor tenant, dan digital campaign berbasis conversion.', N'September 2026'),
('R-003', N'Piutang usaha melewati jatuh tempo', N'VP Keuangan', 20, N'Finance & Treasury', N'Keuangan', 20, 15, N'Melebihi', 68, N'25 Sep 2026', N'Mandat eskalasi collection dan restrukturisasi selektif.', N'Aging control mingguan, payment reminder otomatis, dan collection task force.', N'September 2026'),
('R-004', N'Ketergantungan pendapatan pada pelanggan utama', N'Direktur Komersial', 4, N'Marketing & Leasing', N'Strategis', 16, 12, N'Mendekati', 35, N'30 Nov 2026', N'', N'Diversifikasi portofolio dan target kontribusi revenue eksternal.', N'September 2026'),
('R-005', N'Kegagalan SLA layanan kebersihan dan keamanan', N'VP Operasional', 14, N'HSE & Facility', N'Operasional', 16, 12, N'Mendekati', 76, N'10 Okt 2026', N'', N'Digital SLA monitoring, vendor scorecard, dan corrective action.', N'September 2026'),
('R-006', N'Ketidakpatuhan perizinan dan regulasi properti', N'Corporate Secretary', 17, N'Corporate Secretary', N'Kepatuhan', 15, 10, N'Dalam', 82, N'20 Okt 2026', N'', N'Compliance calendar dan legal document repository.', N'September 2026'),
('R-007', N'Keterlambatan proyek revitalisasi aset', N'VP Property', 13, N'Construction & Fit Out', N'Operasional', 15, 9, N'Dalam', 64, N'15 Des 2026', N'', N'Stage gate proyek dan weekly project control.', N'September 2026'),
('R-008', N'Kesenjangan kompetensi digital tenaga kerja', N'VP Human Capital', 15, N'HR & GA', N'Strategis', 12, 8, N'Dalam', 60, N'31 Des 2026', N'', N'Digital capability mapping dan targeted upskilling.', N'September 2026');
GO

-- C. Seed MNRSK_kri (Merelasi ke dbo.organisation_d_3s)
INSERT INTO dbo.MNRSK_kri (indicator, value, threshold, department_id, directorate, status, period)
VALUES
(N'System availability', N'98,2%', N'≥ 99,5%', 16, N'IT', N'Kritis', N'September 2026'),
(N'Occupancy rate', N'61%', N'≥ 75%', 4, N'Marketing & Leasing', N'Waspada', N'September 2026'),
(N'Overdue receivables', N'Rp8,7 M', N'≤ Rp5 M', 20, N'Finance & Treasury', N'Kritis', N'September 2026'),
(N'SLA vendor achievement', N'91%', N'≥ 95%', 14, N'HSE & Facility', N'Waspada', N'September 2026'),
(N'Employee turnover', N'4,1%', N'≤ 8%', 15, N'HR & GA', N'Normal', N'September 2026'),
(N'Project delay index', N'7,5%', N'≤ 10%', 13, N'Construction & Fit Out', N'Normal', N'September 2026');
GO

-- 5. Verification Query Join
SELECT r.id, r.title, r.department_id, d.d_3_name AS department_name, d.d_3_desc AS department_desc, r.residual
FROM dbo.MNRSK_risks r
INNER JOIN dbo.organisation_d_3s d ON r.department_id = d.id;

SELECT k.id, k.indicator, k.department_id, d.d_3_name AS department_name, d.d_3_desc AS department_desc, k.status
FROM dbo.MNRSK_kri k
INNER JOIN dbo.organisation_d_3s d ON k.department_id = d.id;
GO

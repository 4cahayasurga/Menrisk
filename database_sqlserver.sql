-- ============================================================================
-- SQL SERVER DDL & SEED DATA - IFPRO RISK COMMAND CENTER (MENRISK)
-- Database Engine: Microsoft SQL Server 2012 / 2016 / 2019 / 2022 / Azure SQL
-- Seluruh data risks & kri merelasi ke tabel dbo.departments via department_id
-- ============================================================================

-- 1. Create Database (Opsional, jika belum ada)
-- CREATE DATABASE menrisk_db;
-- GO
-- USE menrisk_db;
-- GO

-- 2. Drop existing tables if exists (Urutan: child -> parent)
IF OBJECT_ID('dbo.user_departments', 'U') IS NOT NULL DROP TABLE dbo.user_departments;
IF OBJECT_ID('dbo.risks', 'U') IS NOT NULL DROP TABLE dbo.risks;
IF OBJECT_ID('dbo.kri', 'U') IS NOT NULL DROP TABLE dbo.kri;
IF OBJECT_ID('dbo.users', 'U') IS NOT NULL DROP TABLE dbo.users;
IF OBJECT_ID('dbo.departments', 'U') IS NOT NULL DROP TABLE dbo.departments;
GO

-- 3. Create Table: departments (Master Department / Divisi)
CREATE TABLE dbo.departments (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    code VARCHAR(50) NULL,
    description NVARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT GETDATE()
);
GO

-- 4. Create Table: users
CREATE TABLE dbo.users (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama NVARCHAR(100) NOT NULL,
    level VARCHAR(20) NOT NULL DEFAULT 'user', -- 'admin' atau 'user'
    is_active TINYINT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    updated_at DATETIME NOT NULL DEFAULT GETDATE()
);
GO

-- 5. Create Table: user_departments (Many-to-Many Pivot)
CREATE TABLE dbo.user_departments (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_user_dept_user FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_dept_dept FOREIGN KEY (department_id) REFERENCES dbo.departments(id) ON DELETE CASCADE,
    CONSTRAINT UQ_user_dept UNIQUE (user_id, department_id)
);
GO

-- 6. Create Table: risks (Merelasi ke dbo.departments via department_id)
CREATE TABLE dbo.risks (
    id VARCHAR(20) NOT NULL PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,
    owner NVARCHAR(100) NOT NULL,
    department_id INT NULL, -- Foreign Key ke dbo.departments
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
    CONSTRAINT FK_risks_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id) ON DELETE SET NULL
);
GO

-- 7. Create Table: kri (Key Risk Indicators - Merelasi ke dbo.departments via department_id)
CREATE TABLE dbo.kri (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    indicator NVARCHAR(150) NOT NULL,
    value NVARCHAR(50) NOT NULL,
    threshold NVARCHAR(100) NOT NULL,
    department_id INT NULL, -- Foreign Key ke dbo.departments
    directorate NVARCHAR(100) NULL, -- Fallback display nama divisi
    status NVARCHAR(50) NOT NULL, -- 'Kritis', 'Waspada', 'Normal'
    period NVARCHAR(50) NOT NULL DEFAULT 'September 2026',
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_kri_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id) ON DELETE SET NULL
);
GO

-- ============================================================================
-- SEED INITIAL DATA
-- ============================================================================

-- A. Seed Departments (ID: 1=IT, 2=OPS, 3=COM, 4=FIN, 5=HC, 6=IA)
INSERT INTO dbo.departments (name, code, description)
VALUES
(N'Teknologi Informasi', 'IT', N'Divisi TI, Infrastruktur, Cybersecurity dan Sistem Informasi'),
(N'Operasional', 'OPS', N'Divisi Manajemen Operasional dan Pelayanan'),
(N'Komersial', 'COM', N'Divisi Pemasaran, Tenant Mix, dan Penjualan Komersial'),
(N'Keuangan', 'FIN', N'Divisi Keuangan, Akuntansi, Penagihan, dan Perpajakan'),
(N'Human Capital', 'HC', N'Divisi Sumber Daya Manusia dan Pengembangan Talenta'),
(N'Audit Internal', 'IA', N'Divisi Audit dan Risk Assurance');
GO

-- B. Seed Users (Default: admin / admin123, user / user123)
INSERT INTO dbo.users (username, password, nama, level, is_active)
VALUES
('admin', '$2y$10$rnfvhgYbJ5ngexXtrD11Me7BIspnWyS1Mgfk2eS9ShqWfY9xKAsz6', N'Administrator Enterprise', 'admin', 1),
('user', '$2y$10$BPuT51BXP08cHq9jfOlb0uvUbZQHmii03qi3J0.3CWF4.6WjVvCO6', N'Risk Officer Operasional', 'user', 1),
('it_risk', '$2y$10$BPuT51BXP08cHq9jfOlb0uvUbZQHmii03qi3J0.3CWF4.6WjVvCO6', N'Staff IT Risk & Compliance', 'user', 1);
GO

-- C. Seed Relasi User - Department (Many-to-Many)
INSERT INTO dbo.user_departments (user_id, department_id)
VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), -- Admin punya akses ke semua divisi
(2, 2), (2, 4),                                 -- User punya akses ke Operasional & Keuangan
(3, 1);                                         -- IT Risk punya akses ke IT
GO

-- D. Seed Risks (Merelasi ke department_id)
INSERT INTO dbo.risks (id, title, owner, department_id, dir, cat, inherent, residual, appetite, progress, due, decision, mitigation, period)
VALUES 
('R-001', N'Gangguan layanan sistem kritikal dan serangan siber', N'Manager IT', 1, N'Teknologi Informasi', N'Teknologi', 25, 20, N'Melebihi', 42, N'30 Sep 2026', N'Persetujuan percepatan implementasi DR site dan managed SOC.', N'Implementasi DR site, EDR endpoint, SOC 24/7 dan simulasi incident response.', N'September 2026'),
('R-002', N'Target okupansi Sosoro Mall tidak tercapai', N'VP Komersial', 3, N'Komersial', N'Strategis', 20, 16, N'Melebihi', 55, N'15 Okt 2026', N'Persetujuan skema commercial incentive untuk anchor tenant.', N'Repricing tenant mix, akuisisi anchor tenant, dan digital campaign berbasis conversion.', N'September 2026'),
('R-003', N'Piutang usaha melewati jatuh tempo', N'VP Keuangan', 4, N'Keuangan', N'Keuangan', 20, 15, N'Melebihi', 68, N'25 Sep 2026', N'Mandat eskalasi collection dan restrukturisasi selektif.', N'Aging control mingguan, payment reminder otomatis, dan collection task force.', N'September 2026'),
('R-004', N'Ketergantungan pendapatan pada pelanggan utama', N'Direktur Komersial', 3, N'Komersial', N'Strategis', 16, 12, N'Mendekati', 35, N'30 Nov 2026', N'', N'Diversifikasi portofolio dan target kontribusi revenue eksternal.', N'September 2026'),
('R-005', N'Kegagalan SLA layanan kebersihan dan keamanan', N'VP Operasional', 2, N'Operasional', N'Operasional', 16, 12, N'Mendekati', 76, N'10 Okt 2026', N'', N'Digital SLA monitoring, vendor scorecard, dan corrective action.', N'September 2026'),
('R-006', N'Ketidakpatuhan perizinan dan regulasi properti', N'Corporate Secretary', 2, N'Operasional', N'Kepatuhan', 15, 10, N'Dalam', 82, N'20 Okt 2026', N'', N'Compliance calendar dan legal document repository.', N'September 2026'),
('R-007', N'Keterlambatan proyek revitalisasi aset', N'VP Property', 2, N'Operasional', N'Operasional', 15, 9, N'Dalam', 64, N'15 Des 2026', N'', N'Stage gate proyek dan weekly project control.', N'September 2026'),
('R-008', N'Kesenjangan kompetensi digital tenaga kerja', N'VP Human Capital', 5, N'Human Capital', N'Strategis', 12, 8, N'Dalam', 60, N'31 Des 2026', N'', N'Digital capability mapping dan targeted upskilling.', N'September 2026');
GO

-- E. Seed KRI (Merelasi ke department_id)
INSERT INTO dbo.kri (indicator, value, threshold, department_id, directorate, status, period)
VALUES
(N'System availability', N'98,2%', N'≥ 99,5%', 1, N'Teknologi Informasi', N'Kritis', N'September 2026'),
(N'Occupancy rate', N'61%', N'≥ 75%', 3, N'Komersial', N'Waspada', N'September 2026'),
(N'Overdue receivables', N'Rp8,7 M', N'≤ Rp5 M', 4, N'Keuangan', N'Kritis', N'September 2026'),
(N'SLA vendor achievement', N'91%', N'≥ 95%', 2, N'Operasional', N'Waspada', N'September 2026'),
(N'Employee turnover', N'4,1%', N'≤ 8%', 5, N'Human Capital', N'Normal', N'September 2026'),
(N'Project delay index', N'7,5%', N'≤ 10%', 2, N'Operasional', N'Normal', N'September 2026');
GO

-- 8. Verification Query Join
SELECT r.id, r.title, r.department_id, d.name AS department_name, d.code AS department_code, r.residual
FROM dbo.risks r
INNER JOIN dbo.departments d ON r.department_id = d.id;

SELECT k.id, k.indicator, k.department_id, d.name AS department_name, d.code AS department_code, k.status
FROM dbo.kri k
INNER JOIN dbo.departments d ON k.department_id = d.id;
GO

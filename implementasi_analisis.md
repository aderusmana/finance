# 📘 Analisis Alur Lengkap Sistem Bank Garansi (End-to-End Workflow)

Dokumen ini menjelaskan secara menyeluruh seluruh siklus hidup (*lifecycle*), alur kerja (*business workflow*), peran aktor, serta mekanisme notifikasi dan approval pada modul **Bank Garansi (BG)** di sistem Finance.

---

## 🗺️ Peta Alur Utama (High-Level Overview)

```mermaid
graph TD
    %% Tahap 1
    subgraph TAHAP 1: Early Warning & Rekomendasi BG
        Cron[Cron Scheduler H-60<br/>Tgl 1 Awal Bulan] -->|Peringatan BG Expired| AdminRTM[Admin RTM]
        AdminRTM -->|Buka Menu Recommendations| RecForm[Input Penjualan Bulanan & Hitung Rata-rata]
        RecForm -->|Submit Rekomendasi BG| EmailCust[Kirim Email CustomerFillFormNotification ke Customer]
    end

    %% Tahap 2
    subgraph TAHAP 2: Customer Portal (Input Bank & Download Form)
        EmailCust -->|Buka Link Portal| CustInput[Customer Isi Data Bank: Single / Multiple Bank]
        CustInput -->|Submit Data Bank| GenPDF[Sistem Generate Formulir BG PDF]
        GenPDF -->|Email & Download Link| CustSign[Customer Download & TTD Basah + Cap]
    end

    %% Tahap 3
    subgraph TAHAP 3: Upload Dokumen & Verifikasi Submission
        CustSign -->|Upload Signed PDF| PortalUpload[Customer Portal Upload]
        PortalUpload -->|Notif Upload Selesai| AdminReview[Admin RTM Review Dokumen di Submissions]
        AdminReview -->|Pilihan A: Direct Approve| DirectOk[Status: Completed / Approved]
        AdminReview -->|Pilihan B: Koreksi Data & Forward| ForwardFin[Forward ke Finance Approval]
    end

    %% Tahap 4
    subgraph TAHAP 4: Lampiran D Management & Approval Workflow
        AdminRTM2[Admin RTM di Menu Lampiran D] -->|Edit & Buat Versi Baru v+1| SubmitLampD[Status: waiting_approval<br/>Kirim Approval ke Manager Finance]
        SubmitLampD --> DecFin{Keputusan Manager Finance}
        DecFin -->|REJECT + Wajib Isi Reason| NotifRevisi[Status: rejected_by_finance<br/>Notifikasi Alasan Revisi ke Admin-RTM]
        NotifRevisi -->|Admin-RTM Perbaiki Data| AdminRTM2
        DecFin -->|APPROVE| OkLampD[Status: Completed / Approved<br/>Versi Lampiran D Aktif]
    end

    %% Tahap 5
    subgraph TAHAP 5: Finalisasi & Distribusi Dokumen
        OkLampD --> FinalNotif[Notifikasi ke Admin-RTM & Secretary-Finance]
        DirectOk --> FinalNotif
        FinalNotif --> FinalEmail[Email Lampiran_D.pdf ke Sales & Finance]
        FinalEmail --> HistoryBG[Tercatat di BG Histories & Siap Download di Reports]
    end
```

---

## 📌 Rincian 5 Tahapan Alur Kerja Sistem

---

### 1️⃣ Tahap 1: Peringatan Jatuh Tempo (H-60) & Pembuatan Rekomendasi BG

1. **Pemicu Otomatis (Scheduler Cron):**
   * Command `php artisan bg:check-expired` berjalan otomatis setiap **tanggal 1 awal bulan**.
   * Menarik data Bank Garansi berstatus `approved` yang akan jatuh tempo (*expiring*) dalam 60–90 hari ke depan (Batch A: tgl 1–15 bulan ke-2, Batch B: tgl 16–31 bulan ke-3).
   * **Notifikasi Email:** Peringatan email H-60 dikirimkan khusus ke **`admin-rtm`** (role `super-admin` tidak menerima email).
   * Sistem otomatis membuat draft baru di tabel `bg_recommendations` berstatus **`pending`**.

2. **Proses di Menu Recommendations (`/bg-recommendations`):**
   * **Aktor:** **Admin-RTM**
   * Admin-RTM membuka tab *Expiring BG*, lalu mengklik **"Process Recommendation"**.
   * Admin-RTM menginput rincian penjualan bulanan customer (misal 3 bulan terakhir). Sistem otomatis menjumlahkan dan menghitung **nilai rata-rata penjualan**.
   * Sistem otomatis menghitung batas kredit yang direkomendasikan (*Recommended Credit Limit*) dan Nilai BG Ditetapkan (*Set BG*) berdasarkan aturan persentase masa loyalitas customer (`BgLimitRule`) serta PPN (`Tax`).
   * Admin-RTM dapat menyesuaikan nilai final *Credit Limit Updated* dan *Set BG*.

3. **Submit Rekomendasi:**
   * Admin-RTM mengklik **Submit / Save**.
   * Status rekomendasi berubah menjadi **`process`**.
   * Sistem men-generate token unik dan otomatis mengirimkan email **`CustomerFillFormNotification`** ke alamat email customer bersangkutan.

---

### 2️⃣ Tahap 2: Customer Portal (Pengisian Data Bank & Download Formulir)

1. **Akses Portal oleh Customer:**
   * Customer menerima email berisi informasi rata-rata penjualan dan nominal BG yang ditetapkan.
   * Customer mengklik tautan aman: `/customer-portal/form/{token}`.

2. **Pengisian Data Bank Garansi:**
   * Customer mengisi rincian Bank Garansi penjamin:
     * **Nama Bank** (misal: BCA, Mandiri, BRI, BNI, Bank Danamon, dll.)
     * **Nama Cabang Bank**
     * **Nominal BG**
   * Mendukung **Multiple Bank** (jika nilai BG dipecah ke beberapa bank penjamin).

3. **Generate Formulir Konfirmasi:**
   * Setelah customer submit, sistem membuat record pengajuan di tabel `bg_submissions` (status: `awaiting_upload`).
   * Sistem otomatis men-generate file PDF formulir resmi **`Formulir_BG_{form_code}.pdf`**.
   * Sistem mengirimkan email kedua (**`BgSubmissionDocumentMail`**) ke customer berisi tautan unduh formulir tersebut.

4. **Penandatanganan Dokumen:**
   * Customer mengunduh formulir PDF tersebut.
   * Customer mencetak dan melakukan **tanda tangan basah + stempel/cap perusahaan**.

---

### 3️⃣ Tahap 3: Upload Dokumen & Verifikasi Submission

1. **Upload Dokumen oleh Customer:**
   * Customer mengakses portal upload: `/customer-portal/upload/{token}`.
   * Customer mengunggah hasil scan dokumen PDF bertanda tangan basah (maksimal 5MB).
   * Status submission berubah menjadi **`uploaded`** dan token akses customer ditutup (*null*).
   * Activity log mencatat: *"Customer Uploaded Signed Document"*.

2. **Verifikasi oleh Admin-RTM (`/bg-submissions`):**
   * **Aktor:** **Admin-RTM**
   * Admin-RTM menerima notifikasi bahwa customer telah mengunggah dokumen.
   * Admin-RTM membuka menu **BG Submissions** dan mengklik **"View File"** untuk memeriksa dokumen scan customer.

3. **Keputusan Verifikasi oleh Admin-RTM:**
   * **Opsi A: Direct Approve (Bypass Workflow):**
     * Jika dokumen dan data sudah valid tanpa koreksi.
     * Status submission langsung menjadi **`completed`** dan status Bank Garansi menjadi **`approved`**.
     * Riwayat tercatat otomatis di `BgHistory`.
     * Menjalankan notifikasi final completion.
   * **Opsi B: Edit & Forward ke Finance:**
     * Jika ada perubahan nominal / koreksi data customer.
     * Sistem membuat versi baru `LampiranDVersion` (`v+1`), mengubah status menjadi `waiting_approval`, dan meneruskannya ke alur **Finance Approval**.

---

### 4️⃣ Tahap 4: Menu Lampiran D Management & Approval Workflow

1. **Koreksi / Revisi di Menu Lampiran D (`/lampiran-d`):**
   * **Aktor:** **Admin-RTM**
   * Admin-RTM membuka menu **Lampiran D Management** dan memilih data customer yang ingin direvisi lalu klik tombol **"Edit"**.
   * Admin-RTM mengubah data (Average, TOP, Lead Time, Limit Kredit, Nilai Set BG, Nominal BG, serta mengisi catatan revisi).
   * Saat form disimpan:
     * Sistem membuat snapshot versi baru di tabel `lampiran_d_versions` (misal dari `v1` menjadi `v2`).
     * Status submission terkait berubah menjadi **`waiting_approval`**.
     * Sistem memicu **Lampiran D Approval** (`category: 'BG'`, `sub_category: 'Lampiran D'`).
     * Mengirimkan email approval (**`FinanceApprovalMail`**) serta notifikasi lonceng ke **Manager Finance**.

2. **Proses Review oleh Manager Finance:**
   * Manager Finance dapat memproses approval melalui **Email Link** (`/approval/form/{token}/{action}`) atau melalui **Inbox Dashboard** (`/approvals/inbox`).

3. **Hasil Keputusan Manager Finance:**
   * ❌ **Jika DITOLAK (REJECT):**
     * Manager Finance **WAJIB mengisi alasan penolakan (*Reason/Notes*)**.
     * Status submission menjadi **`rejected_by_finance`**.
     * Sistem mengirimkan **Notifikasi Lonceng (`SystemNotification`)** ke **`admin-rtm`** & `super-admin`:
       > *"Perubahan Lampiran D untuk **{Customer}** ditolak oleh Manager Finance. Alasan: \"{Alasan Penolakan}\". Silakan perbaiki di Lampiran D Management dan submit kembali."*
     * Admin-RTM membuka kembali menu Lampiran D Management, memperbaiki data sesuai catatan, dan men-submit ulang.
   * ✅ **Jika DISETUJUI (APPROVE):**
     * Status submission menjadi **`completed`** dan versi Lampiran D tersebut aktif digunakan.
     * Status data Bank Garansi berubah menjadi **`approved`** (masa berlaku aktif 1 tahun ke depan).

---

### 5️⃣ Tahap 5: Notifikasi Akhir, Distribusi Dokumen & Histories

Setelah Lampiran D disetujui (*Approved*):

1. **🔔 Notifikasi Lonceng Aplikasi (`SystemNotification`):**
   * **Penerima:**
     * **`admin-rtm`**
     * **`secretary-finance`**
     * **`super-admin`**
   * **Pesan:**
     > *"Perubahan Lampiran D pada **{Nama Customer}** telah di-approved oleh Manager Finance dan siap di-download atau digunakan."*
   * **Link Redirect:** Langsung mengarah ke menu [Lampiran D Management](file:///c:/laragon/www/htdocs/finance/app/Http/Controllers/BG/LampiranDController.php).

2. **📧 Email Dokumen Final Lampiran D ([CustomerBgReadyMail](file:///c:/laragon/www/htdocs/finance/app/Mail/CustomerBgReadyMail.php)):**
   * Mengirimkan email berlampirkan dokumen resmi **`Lampiran_D.pdf`** khusus untuk tim internal:
     * **Sales & Marketing:** Role **`head-SNM`** dan **`admin-rtm`**
     * **Finance:** Role **`manager-finance`**, **`head-finance`**, dan **`secretary-finance`**
     * *(Catatan: Dokumen Lampiran D adalah dokumen analisa internal sehingga **tidak dikirimkan ke Customer**).*

3. **📊 Pencatatan Riwayat & Laporan:**
   * Riwayat perubahan nominal, tanggal expired, dan catatan revisi tersimpan permanen di menu **BG Histories** (`/bg-histories`).
   * Seluruh dokumen (Lampiran D PDF, Surat Pengantar Bank, dll.) siap diunduh kapan saja melalui menu **Reports** (`/bg-reports`) maupun menu **Lampiran D**.

---

## 👥 Matriks Hak Akses & Tanggung Jawab Role

| Role | Tanggung Jawab Utama dalam Alur BG |
| :--- | :--- |
| **`admin-rtm`** | • Menerima notifikasi H-60 expiring BG.<br/>• Memproses rekomendasi BG & input penjualan bulanan.<br/>• Memverifikasi upload dokumen customer di menu Submissions.<br/>• Melakukan revisi/versioning di menu Lampiran D Management.<br/>• Menerima notifikasi revisi (jika reject) & notifikasi persetujuan (jika approve). |
| **`manager-finance`** | • Menerima notifikasi & email permohonan approval Lampiran D.<br/>• Melakukan review, memberikan alasan penolakan (jika reject), atau menyetujui dokumen (approve).<br/>• Menerima email berkas final Lampiran D. |
| **`head-finance`** | • Memonitoring seluruh pengajuan dan menerima notifikasi / tembusan email final Lampiran D. |
| **`secretary-finance`** | • Menerima notifikasi saat Lampiran D selesai di-approve oleh Manager Finance.<br/>• Menerima email berkas final `Lampiran_D.pdf` yang siap digunakan/diarsipkan. |
| **`head-SNM`** | • Menerima tembusan email dokumen final `Lampiran_D.pdf` untuk koordinasi penjualan. |
| **`Customer / Distributor`** | • Menerima email link portal untuk pengisian data bank.<br/>• Mengunduh formulir konfirmasi BG dan mengunggah kembali dokumen bertanda tangan basah. |

---

## 🗂️ Struktur File & Referensi Controller

* **Rekomendasi BG:** `App\Http\Controllers\BG\BgRecommendationController.php`
* **Portal Customer:** `App\Http\Controllers\BG\CustomerBgPortalController.php`
* **Verifikasi Submissions:** `App\Http\Controllers\BG\BgSubmissionController.php`
* **Manajemen Versi Lampiran D:** `App\Http\Controllers\BG\LampiranDController.php`
* **Approval via Email Link:** `App\Http\Controllers\BG\ApprovalProcessController.php`
* **Approval via Inbox Dashboard:** `App\Http\Controllers\BG\BgApprovalInboxController.php`
* **Scheduler H-60 Expiring BG:** `App\Console\Commands\CheckExpiringBg.php`
* **Riwayat & Laporan:** `BgHistoryController.php` & `BgReportController.php`

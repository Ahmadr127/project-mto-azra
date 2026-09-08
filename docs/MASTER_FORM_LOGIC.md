# Master MCU & Logic Form Pemeriksaan

> Menjawab: _“di setiap master selain master paket ada masing-masing nama layanan, bagaimana logic untuk form tindakan, lab, non lab, anamnesis, dan fisik, apakah setiap inputan dinamis atau hardcode, dan apa yg dinamis dan apa yg hardcode di setiap inputan”_

---

## 1. Ringkasan Master

| Master | Tabel | Field dinamis (diinput admin) | Menu | Dipakai di form mana |
|---|---|---|---|---|
| **Paket MCU** | `mcu_packages` | `code, name, base_price, description, display_order, status` | `Master Paket MCU` (`mcu-packages.*`) | Template pendaftaran — `McuRegistrationController@store` baca `McuPackageItem` |
| **Tindakan** | `mcu_medical_actions` | `code, name, category, price, description` | `Master Tindakan` | `doctor/form` |
| **Lab** | `mcu_labs` | `code, name, category, normal_value, price` | `Master Lab` | `lab/form` |
| **Non-Lab (Radiologi)** | `mcu_radiologies` | `code, name, category, price` | `Master Non-Lab` | `radiology/form` |
| **Anamnesis** | `mcu_anamneses` | `code, name, category` | `Master Anamnesis` | `anamnesis/form` |
| **Fisik** | `mcu_physical_exams` | `code, name, category` (cuma 1 row `FIS-MASTER-001` sebagai flag) | `Master Pemeriksaan Fisik` | `physical/form` |

**Relasi paket:** `mcu_package_items` polimorfik `item_type (McuLab::class|McuMedicalAction::class|...)` + `item_id` + `mcu_package_id`. Saat `POST mcu-registrations.store` (`McuRegistrationController.php:48-99`) `DB::transaction` → `McuPackage::with('items')` → loop `class_basename(item_type)` → `examLabs()->create(['mcu_lab_id','normal_value','pending'])` / `examMedicalActions` / `examRadiologies` / `examAnamneses` / `physicalExamResult`. Jadi **apakah form muncul ditentukan paket** (kalau paket tidak punya item Lab, `examLabs->isEmpty()` → banner `Belum Dikonfigurasi`).

---

## 2. Logic per Form Pemeriksaan

### 2.1 Tindakan / Konsultasi Dokter — `doctor/form.blade.php` + `McuExaminationController:doctorForm/store`

```php
// Controller:156-178
$mcuRegistration->load('examMedicalActions.medicalAction'); // dinamis dari paket
// store: foreach $request->results as $examId => $value
$exam->update(['result'=>$value,'doctor_id'=>auth()->id(),'status'=>'completed']);
```

| Aspek | Dinamis | Hardcode |
|---|---|---|
| **Daftar pemeriksaan** | `foreach examMedicalActions` → `medicalAction->name`, `code` (dari master) | Header `Daftar Pemeriksaan & Konsultasi Dokter ({{ count }} Tindakan)` + layout card `md:w-4/12` label + `md:w-8/12` textarea |
| **Input** | `value` = `old('results.'.$exam->id, $exam->result)` ; `name="results[{{ $exam->id }}]"` | `textarea rows=2` placeholder `Masukkan hasil... atau resep...`, default `Dalam Batas Normal` |

---

### 2.2 Laboratorium — `lab/form.blade.php` + `labForm/store`

```php
$groupedExams = $mcuRegistration->examLabs->groupBy(fn($e)=> $e->lab->category ?? 'Lainnya');
```

| Aspek | Dinamis | Hardcode |
|---|---|---|
| **Kategori** | `lab->category, name, code, normal_value, display_order` dari `mcu_labs` | Array `$categoryOrder` 9 entri (`Hematologi Lengkap:{icon:fa-droplet,color:border-rose...}`, `Urine`, `Urine Sedimen`, `Fungsi Liver`, `Lipid Profil`, `Fungsi Ginjal`, `Glukosa Darah`, `Imunologi`, `Lainnya`) — urutan + icon + deskripsi hardcode |
| **Grid** | `foreach $normalExams->sortBy('lab.display_order')` → card per lab | `grid lg:grid-cols-4`, filter `!str_ends_with(code,'-KES')` untuk pisah normal vs rekap, `input type=text name="results[exam.id]"` + `Rujukan: {{ normal_value }}` hardcode template |
| **Rekap** | `summaryExam` = pertama `code` berakhiran `-KES` (dinamis cari) | `textarea` rekap dengan placeholder `Tuliskan temuan abnormal...` + label `REKAPITULASI SUMMARY` hardcode |

---

### 2.3 Non-Lab (Radiologi / Penunjang) — `radiology/form.blade.php`

| Aspek | Dinamis | Hardcode |
|---|---|---|
| **Daftar** | `foreach examRadiologies->sortBy('radiology.display_order')` → `radiology->name` | Header `B. Penunjang Medis Non-Laboratorium` + warning `*Merupakan kesimpulan... Dalam Batas Normal` hardcode |
| **Input** | `value` = `old('results.'.$exam->id, $exam->findings)` (hasil sebelumnya) | `$defaultFindings` hardcode map `EKG=>Dalam Batas Normal`, `Foto Thorax=>Cor tak membesar...` dll; `textarea name="results[exam.id]"` + toggle 2 tombol `Normal/Abnormal` (`is_normal[exam.id]` hidden + JS `setStatus()`) hardcode; kolom `Jenis Pemeriksaan | Status Temuan | Hasil Temuan` hardcode |

---

### 2.4 Anamnesis — `anamnesis/form.blade.php` (paling hardcode)

```php
$examsByCode = $mcuRegistration->examAnamneses->keyBy(fn($e)=> trim($e->anamnesis->code));
$getInputHtml('ANM-KD-001') // helper cari by code
```

| Aspek | Dinamis | Hardcode |
|---|---|---|
| **Struktur form** | `exam->result` diisi user, diambil via `$examsByCode->get('ANM-xx-xxx')`; kalau code tidak ada di paket → `<span>Komponen tidak aktif</span>` | **38+ kode spesifik hardcode** di blade: `ANM-KD-001` (Koreksi Dokter), `ANM-KP-001/002` (Keluhan Kerja), `ANM-RK-001..012` (Riwayat Keluarga), `ANM-RP-001..014` (Riwayat Pribadi), `ANM-RI-001..003` (Rawat Inap), `ANM-KB-001..003` (Kebiasaan), `ANM-OP-001` (Operasi), `ANM-OB-001` (Obat) + layout 6 seksi (`Koreksi Dokter`, `Riwayat Kesehatan`, `Riwayat Penyakit Keluarga` (tabel 12 baris), `Riwayat Penyakit Pribadi` (10 baris), `Rawat Inap 3 Tahun`, `Kebiasaan/Operasi/Obat` 3 kolom) + tabel HTML hardcode |
| **Input** | `value` dinamis, `exam_ids[]` hidden dinamis | `select` options hardcode: keluarga `Tidak/Tidak Tahu/Ya (Ayah)/Ya (Ibu)/Ya (Ayah&Ibu)`, pribadi `Tidak/Ya`, kematian `Tidak/Ya (Ayah)/...` ; placeholder `Ketik hasil...` hardcode |

> Walau master `mcu_anamneses` punya `name`, **form tidak `foreach` master** — melainkan `get('ANM-RK-002')` hardcode per baris. Jika paket tidak include kode itu, baris jadi `Komponen tidak aktif`.

---

### 2.5 Pemeriksaan Fisik — `physical/form.blade.php` (full hardcode)

**Satu-satunya bagian dinamis:** `if (!$mcuRegistration->physicalExamResult)` (ada/tidaknya form ditentukan paket include `McuPhysicalExam`). Isi form **100% hardcode** 8 seksi:

| Seksi | Field hardcode (`name="..."`) | Dinamis |
|---|---|---|
| **1. Antropometri & Vital Signs** | `vital_signs[waist/height/weight/bmi/bmi_conclusion/tensi_1/2/3/tensi_avg/pulse/temp/respiration/conclusion]` + Alpine `antropometriCalc()` hardcode hitung `bmi = weight/(height/100)^2` & `tensi_avg` dari 2-3 | `value="{{ $vs['waist'] ?? '' }}"` dari `mcu_physical_exam_results.vital_signs` JSON (dinamis isi, tapi key hardcode) |
| **2. Kepala & Muka** | `head_and_neck[head/face]` | value dinamis |
| **3. Mata & Visus** | `head_and_neck[sclera/conjunctiva/color_blind/visus_od_sph/cyl/axis/add + os_* + visus_notes]` + tabel `Spheris/Silindris/Axis/Add` hardcode | - |
| **4. THT Mulut Gigi** | `head_and_neck[nose_konka/septum/polip/ear_shape/drum/cerumen/throat_tonsil/...+dental_caries/decay/missing/calculus + mouth_halitosis/.../neck_*]` | - |
| **5. Thorax Abdomen** | `thorax[lung_auscultation/sternum/heart_rhythm/murmur/gallop/breast_status]`, `abdomen[hepar/lien/peristaltik]` | - |
| **6. Urogenital** | `urogenital[haemorrhoid/cva_pain/hernia]` | - |
| **7. Ekstremitas** | `extremities[up_right_str/physio/patho + up_left_* + low_* + varices]` tabel Kanan/Kiri hardcode | - |
| **8. Lain-lain** | `others[skin/romberg/visual_field]` | - |

Semua `placeholder` (`Central`, `Tidak Ada`, `Dalam Batas Normal`) & default value hardcode; `physicalExamResult` JSON kolom `vital_signs/head_and_neck/thorax/abdomen/urogenital/extremities/others` cuma wadah.

---

## 3. Kesimpulan Dinamis vs Hardcode

| Form | Apa yang dinamis (dari DB/master) | Apa yang hardcode (di blade/controller) |
|---|---|---|
| **Lab** | `lab->name/code/normal_value/category`, jumlah card per kategori | `categoryOrder` 9 kategori + icon/color, grid 4 kolom, filter `-KES`, placeholder, banner empty |
| **Tindakan** | `medicalAction->name/code`, jumlah baris | Header, textarea, count badge |
| **Non-Lab** | `radiology->name` | `defaultFindings` EKG/Thorax, toggle Normal/Abnormal + JS, tabel 3 kolom |
| **Anamnesis** | `exam->result` per kode + apakah kode ada di paket | 38 kode `ANM-*`, 6 seksi layout, tabel, dropdown options |
| **Fisik** | Hanya `ada/tidak` form + isi JSON sebelumnya | 8 seksi, ~50 field name, Alpine BMI/tensi, tabel Visus/Ekstremitas |

**Inti:** `master` (`name` layanan) selalu dinamis (admin CRUD di `Master MCU`), tapi **form pemeriksaan Fisik & Anamnesis 80-100% hardcode struktur** (kode & field name ditulis manual di blade), sedangkan **Lab/Tindakan/Non-Lab** hybrid (loop dinamis dari `exam*` tapi grouping/header/toggle hardcode).

---

## 4. File Terkait (untuk trace)

- Master CRUD: `app/Http/Controllers/Mcu{Lab,MedicalAction,Radiology,Anamnesis,PhysicalExam}Controller.php` + `McuPackageController.php`
- Pemeriksaan: `app/Http/Controllers/McuExaminationController.php:12-178` + `resources/views/mcu/examinations/{lab,doctor,radiology,anamnesis,physical}/form.blade.php`
- Registrasi: `app/Models/McuRegistration.php` (`examLabs, examRadiologies, examAnamneses, examMedicalActions, physicalExamResult`) + `McuPackageItem.php` (polimorfik)
- Seed: `database/seeders/MasterMcuSeeder.php` (1 row per master) + `DemoSeeder.php` (DEMO-RM-001..007, DEMO-PKT-*)

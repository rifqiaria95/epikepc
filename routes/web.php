<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Mono\TagController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Mono\ItemController;
use App\Http\Controllers\Mono\NewsController;
use App\Http\Controllers\Mono\UnitController;
use App\Http\Controllers\Mono\UserController;
use App\Http\Controllers\Mono\AboutController;
use App\Http\Controllers\Mono\AdminController;
use App\Http\Controllers\Mono\CabangController;
use App\Http\Controllers\Mono\DivisiController;
use App\Http\Controllers\Mono\GaleriController;
use App\Http\Controllers\Mono\GudangController;
use App\Http\Controllers\Mono\VisiMisiController;
use App\Http\Controllers\Mono\VendorController;
use App\Http\Controllers\Mono\ChatLogController;
use App\Http\Controllers\Mono\JabatanController;
use App\Http\Controllers\Mono\PegawaiController;
use App\Http\Controllers\Mono\ProgramController;
use App\Http\Controllers\Mono\KategoriController;
use App\Http\Controllers\Mono\DashboardController;
use App\Http\Controllers\Mono\EducationController;
use App\Http\Controllers\Mono\KnowledgeController;
use App\Http\Controllers\Mono\MenuGroupController;
use App\Http\Controllers\Mono\PelangganController;
use App\Http\Controllers\Mono\DepartemenController;
use App\Http\Controllers\Mono\ExperienceController;
use App\Http\Controllers\Mono\MenuDetailController;
use App\Http\Controllers\Mono\OrganisasiController;
use App\Http\Controllers\Mono\PermissionController;
use App\Http\Controllers\Mono\PerusahaanController;
use App\Http\Controllers\Mono\ProgramReqController;
use App\Http\Controllers\Ext\RegistrationController;
use App\Http\Controllers\Mono\JenisProgramController;
use App\Http\Controllers\Mono\ProgramRegistController;
use App\Http\Controllers\Mono\SubMenuDetailController;
use App\Http\Controllers\Mono\KategoriGaleriController;
use App\Http\Controllers\Mono\RolePermissionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Ext\ProgramRegistController as ExtProgramRegistController;
use App\Http\Controllers\Mono\TestimoniController;

Route::get('/', function () {
    return view('auth.login');
});

// Route untuk External
Route::prefix('registration')->name('registration.')->group(function () {
    Route::get('/', [RegistrationController::class, 'index'])->name('index');
    Route::post('/store', [RegistrationController::class, 'store'])->name('store');
    Route::get('/show/{id}', [RegistrationController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [RegistrationController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [RegistrationController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [RegistrationController::class, 'destroy'])->name('destroy');
});

// API Routes untuk dropdown wilayah
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/provinsi', [RegistrationController::class, 'getProvinsi'])->name('provinsi');
    Route::get('/kota/{id_provinsi}', [RegistrationController::class, 'getKota'])->name('kota');
    Route::get('/kecamatan/{id_kota}', [RegistrationController::class, 'getKecamatan'])->name('kecamatan');
    Route::get('/kelurahan/{id_kecamatan}', [RegistrationController::class, 'getKelurahan'])->name('kelurahan');
});

// Route untuk Semua Role
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ext-dashboard', [DashboardController::class, 'extDashboard'])->name('ext-dashboard');

    // Route User
    Route::prefix('inventory/unit')->name('unit.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::post('/store', [UnitController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UnitController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('destroy');
    });

    // Route Pegawai
    Route::prefix('hrd/pegawai')->name('pegawai.')->group(function () {
        Route::get('/', [PegawaiController::class, 'index'])->name('index');
        Route::post('/store', [PegawaiController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PegawaiController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PegawaiController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PegawaiController::class, 'destroy'])->name('destroy');
        Route::get('/profile/{id}', [PegawaiController::class, 'profile'])->name('profile');
        Route::get('/get-kota/{id_provinsi}', [PegawaiController::class, 'getKotaByProvinsi'])->name('pegawai.get-kota');
        Route::get('/get-cabang/{id_perusahaan}', [PegawaiController::class, 'getCabangByPerusahaan'])->name('pegawai.get-cabang');
        Route::get('/get-departemen/{id_divisi}', [PegawaiController::class, 'getDepartemenByDivisi'])->name('pegawai.get-departemen');
        Route::get('/form-example', [PegawaiController::class, 'formExample'])->name('pegawai.form-example');
    });

    // Route Jabatan
    Route::prefix('hrd/jabatan')->name('jabatan.')->group(function () {
        Route::get('/', [JabatanController::class, 'index'])->name('index');
        Route::post('/store', [JabatanController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [JabatanController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [JabatanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [JabatanController::class, 'destroy'])->name('destroy');
    });

    // Route Divisi
    Route::prefix('hrd/divisi')->name('divisi.')->group(function () {
        Route::get('/', [DivisiController::class, 'index'])->name('index');
        Route::post('/store', [DivisiController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DivisiController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DivisiController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DivisiController::class, 'destroy'])->name('destroy');
    });

    // Route Departemen
    Route::prefix('hrd/departemen')->name('departemen.')->group(function () {
        Route::get('/', [DepartemenController::class, 'index'])->name('index');
        Route::post('/store', [DepartemenController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DepartemenController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DepartemenController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DepartemenController::class, 'destroy'])->name('destroy');
        Route::get('/get-by-divisi/{id_divisi}', [DepartemenController::class, 'getByDivisi'])->name('get.by.divisi');
    });

    // Route Perusahaan
    Route::prefix('company/perusahaan')->name('perusahaan.')->group(function () {
        Route::get('/', [PerusahaanController::class, 'index'])->name('index');
        Route::post('/store', [PerusahaanController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PerusahaanController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PerusahaanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PerusahaanController::class, 'destroy'])->name('destroy');
    });

    // Route Cabang
    Route::prefix('company/cabang')->name('cabang.')->group(function () {
        Route::get('/', [CabangController::class, 'index'])->name('index');
        Route::post('/store', [CabangController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CabangController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CabangController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CabangController::class, 'destroy'])->name('destroy');
        Route::get('/get-perusahaan', [CabangController::class, 'getPerusahaan'])->name('get.perusahaan');
        Route::get('/get-by-perusahaan/{id_perusahaan}', [CabangController::class, 'getByPerusahaan'])->name('get.by.perusahaan');
    });

    // Route Item
    Route::prefix('inventory/item')->name('item.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::post('/store', [ItemController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ItemController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ItemController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ItemController::class, 'destroy'])->name('destroy');
        Route::get('/profile/{id}', [ItemController::class, 'profile'])->name('profile');
    });

    // Route Gudang
    Route::prefix('inventory/gudang')->name('gudang.')->group(function () {
        Route::get('/', [GudangController::class, 'index'])->name('index');
        Route::post('/store', [GudangController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [GudangController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [GudangController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [GudangController::class, 'destroy'])->name('destroy');
    });

    // Route Kategori
    Route::prefix('portfolio/news/kategori')->name('kategori.')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::post('/store', [KategoriController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });

    // Route Jenis Program
    Route::prefix('program/jenis-program')->name('jenis-program.')->group(function () {
        Route::get('/', [JenisProgramController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_jenis_program');
        Route::post('/store', [JenisProgramController::class, 'store'])
            ->name('store')
            ->middleware('permission:approve_jenis_program');
        Route::get('/edit/{id}', [JenisProgramController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_jenis_program');
        Route::put('/update/{id}', [JenisProgramController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_jenis_program');
        Route::delete('/delete/{id}', [JenisProgramController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_jenis_program');
        Route::get('/show/{id}', [JenisProgramController::class, 'show'])
            ->name('show')
            ->middleware('permission:show_jenis_program');
        Route::post('/approve/{id}', [JenisProgramController::class, 'approve'])
            ->name('approve')
            ->middleware('permission:approve_jenis_program');
        Route::post('/reject/{id}', [JenisProgramController::class, 'reject'])
            ->name('reject')
            ->middleware('permission:reject_jenis_program');
    });

    // Route Program
    Route::prefix('program/daftar-program')->name('daftar-program.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_daftar_program');
        Route::post('/store', [ProgramController::class, 'store'])
            ->name('store')
            ->middleware('permission:approve_daftar_program');
        Route::get('/edit/{id}', [ProgramController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_daftar_program');
        Route::put('/update/{id}', [ProgramController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_daftar_program');
        Route::delete('/delete/{id}', [ProgramController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_daftar_program');
        Route::get('/show/{id}', [ProgramController::class, 'show'])
            ->name('show')
            ->middleware('permission:show_daftar_program');
        Route::post('/approve/{id}', [ProgramController::class, 'approve'])
            ->name('approve')
            ->middleware('permission:approve_daftar_program');
        Route::post('/reject/{id}', [ProgramController::class, 'reject'])
            ->name('reject')
            ->middleware('permission:reject_daftar_program');
    });


    // Route untuk mendapatkan data program berdasarkan ID
    Route::get('/program/get-program/{id}', [ProgramController::class, 'getProgram'])
        ->name('program.get-program')
        ->middleware('auth');

    // Route Program Requirement
    Route::prefix('program/program-requirement')->name('program-requirement.')->group(function () {
        Route::get('/', [ProgramReqController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_program_requirement');
        Route::post('/store', [ProgramReqController::class, 'store'])
            ->name('store')
            ->middleware('permission:approve_program_requirement');
        Route::get('/edit/{id}', [ProgramReqController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_program_requirement');
        Route::put('/update/{id}', [ProgramReqController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_program_requirement');
        Route::delete('/delete/{id}', [ProgramReqController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_program_requirement');
    });

    // Route Program Registration
    Route::prefix('program/program-registration')->name('program-registration.')->group(function () {
        Route::get('/', [ProgramRegistController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_program_registration');
        Route::post('/store', [ProgramRegistController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_program_registration');
        Route::get('/edit/{id}', [ProgramRegistController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_program_registration');
        Route::put('/update/{id}', [ProgramRegistController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_program_registration');
        Route::delete('/delete/{id}', [ProgramRegistController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_program_registration');
    });

    // Route Program Registration External
    Route::prefix('registration-program')->name('registration-program.')->middleware('guest_role')->group(function () {
        Route::get('/', [ExtProgramRegistController::class, 'index'])
            ->name('index');
        Route::post('/store', [ExtProgramRegistController::class, 'store'])
            ->name('store');
        Route::get('/edit/{id}', [ExtProgramRegistController::class, 'edit'])
            ->name('edit');
        Route::put('/update/{id}', [ExtProgramRegistController::class, 'update'])
            ->name('update');
    });

    // Route Tag
    Route::prefix('portfolio/news/tag')->name('tag.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::post('/store', [TagController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TagController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TagController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TagController::class, 'destroy'])->name('destroy');
    });

    // Route Pelanggan
    Route::prefix('sales/customer')->name('sales/customer.')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('index');
        Route::post('/store', [PelangganController::class, 'store'])->name('store');
        Route::get('/edit/{id:uuid}', [PelangganController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PelangganController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PelangganController::class, 'destroy'])->name('destroy');
        Route::get('/get-kota/{id_provinsi}', [PelangganController::class, 'getKotaByProvinsi'])->name('pelanggan.get-kota');
        Route::get('/profile/{id}', [PelangganController::class, 'profile'])->name('profile');
    });

    // Route Pelanggan
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::post('/store', [VendorController::class, 'store'])->name('store');
        Route::get('/edit/{id:uuid}', [VendorController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [VendorController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('destroy');
        Route::get('/get-kota/{id_provinsi}', [VendorController::class, 'getKotaByProvinsi'])->name('vendor.get-kota');
        Route::get('/profile/{id}', [VendorController::class, 'profile'])->name('profile');
    });

    // Route News
    Route::prefix('portfolio/news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_news');
        Route::post('/store', [NewsController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_news');
        Route::get('/edit/{id:uuid}', [NewsController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_news');
        Route::put('/update/{id}', [NewsController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_news');
        Route::delete('/delete/{id}', [NewsController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_news');
    });

    // Route About
    Route::prefix('portfolio/profile/about')->name('about.')->group(function () {
        Route::get('/', [AboutController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_profile');
        Route::post('/store', [AboutController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_profile');
        Route::get('/edit/{id}', [AboutController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_profile');
        Route::put('/update/{id}', [AboutController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_profile');
        Route::delete('/delete/{id}', [AboutController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_profile');
    });

    // Route Visi Misi
    Route::prefix('portfolio/profile/visi-misi')->name('visi-misi.')->group(function () {
        Route::get('/', [VisiMisiController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_profile');
        Route::post('/store', [VisiMisiController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_profile');
        Route::get('/edit/{id}', [VisiMisiController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_profile');
        Route::put('/update/{id}', [VisiMisiController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_profile');
        Route::delete('/delete/{id}', [VisiMisiController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_profile');
    });

    // Route Education
    Route::prefix('portfolio/education')->name('education.')->group(function () {
        Route::get('/', [EducationController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_education');
        Route::post('/store', [EducationController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_education');
        Route::get('/edit/{id}', [EducationController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_education');
        Route::put('/update/{id}', [EducationController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_education');
        Route::delete('/delete/{id}', [EducationController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_education');
    });

    // Route Galeri
    Route::prefix('portfolio/galeri/list-galeri')->name('list-galeri.')->group(function () {
        Route::get('/', [GaleriController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_galeri');
        Route::post('/store', [GaleriController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_galeri');
        Route::get('/edit/{id}', [GaleriController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_galeri');
        Route::put('/update/{id}', [GaleriController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_galeri');
        Route::delete('/delete/{id}', [GaleriController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_galeri');
    });

    // Route Kategori Galeri
    Route::prefix('portfolio/galeri/kategori-galeri')->name('kategori-galeri.')->group(function () {
        Route::get('/', [KategoriGaleriController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_galeri');
        Route::post('/store', [KategoriGaleriController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_galeri');
        Route::get('/edit/{id}', [KategoriGaleriController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:show_galeri');
        Route::put('/update/{id}', [KategoriGaleriController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_galeri');
        Route::delete('/delete/{id}', [KategoriGaleriController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_galeri');
    });

    // Route Organisasi
    Route::prefix('portfolio/organisasi')->name('organisasi.')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_organisasi');
        Route::post('/store', [OrganisasiController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_organisasi');
        Route::get('/edit/{id}', [OrganisasiController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_organisasi');
        Route::put('/update/{id}', [OrganisasiController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_organisasi');
        Route::delete('/delete/{id}', [OrganisasiController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_organisasi');
    });

    // Route Experience
    Route::prefix('portfolio/experience')->name('experience.')->group(function () {
        Route::get('/', [ExperienceController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_experience');
        Route::post('/store', [ExperienceController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_experience');
        Route::get('/edit/{id}', [ExperienceController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_experience');
        Route::put('/update/{id}', [ExperienceController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_experience');
        Route::delete('/delete/{id}', [ExperienceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_experience');
    });

    // Route Testimoni
    Route::prefix('portfolio/testimoni')->name('testimoni.')->group(function () {
        Route::get('/', [TestimoniController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_testimoni');
        Route::post('/store', [TestimoniController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_testimoni');
        Route::get('/edit/{id}', [TestimoniController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_testimoni');
        Route::put('/update/{id}', [TestimoniController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_testimoni');
        Route::delete('/delete/{id}', [TestimoniController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_testimoni');
    });

    // Route User
    Route::prefix('/admin/users')->name('user.')->group(function () {
        Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
    });

    // Route Chat
    Route::prefix('admin/chat')->name('chat.')->group(function () {
        Route::get('/chat', [ChatLogController::class, 'index'])->name('index');
    });

    // Route Knowledge
    Route::prefix('admin/knowledge')->name('knowledge.')->group(function () {
        Route::get('/', [KnowledgeController::class, 'index'])->name('index');
        Route::post('/store', [KnowledgeController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KnowledgeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KnowledgeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [KnowledgeController::class, 'destroy'])->name('destroy');
    });
});

// Route untuk Superadmin
Route::middleware(['auth', 'role:superadmin'])->group(function () {

    // Route User
    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::get('/getPermission/{id}', [UserController::class, 'getPermission'])->name('getPermission');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Route Role
    Route::prefix('admin/role')->name('role.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::post('/store', [RolePermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RolePermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RolePermissionController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
        Route::get('/profile/{id}', [RolePermissionController::class, 'profile'])->name('profile');
        Route::get('/permissions', [RolePermissionController::class, 'getPermissions']);
    });

    // Route Permission
    Route::prefix('admin/permission')->name('permission.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/store', [PermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-batch', [PermissionController::class, 'destroyBatch'])->name('destroy.batch');
        Route::get('/profile/{id}', [PermissionController::class, 'profile'])->name('profile');
        Route::get('/permissions', [PermissionController::class, 'getPermissions']);
        Route::get('/get-menu-groups', [PermissionController::class, 'getMenuGroups'])->name('get.menu.groups');
        Route::get('/get-menu-details', [PermissionController::class, 'getMenuDetails'])->name('get.menu.details');
        Route::get('/get-menu-details-by-group', [PermissionController::class, 'getMenuDetailsByGroup'])->name('get.menu.details.by.group');

    });

    // Route Menu Group
    Route::prefix('admin/menu-group')->name('menu-group.')->group(function () {
        Route::get('/', [MenuGroupController::class, 'index'])->name('index');
        Route::post('/store', [MenuGroupController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuGroupController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MenuGroupController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuGroupController::class, 'destroy'])->name('destroy');
    });

    // Route Menu Details
    Route::prefix('admin/menu-detail')->name('menu-detail.')->group(function () {
        Route::get('/', [MenuDetailController::class, 'index'])->name('index');
        Route::post('/store', [MenuDetailController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuDetailController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [MenuDetailController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuDetailController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-batch', [MenuDetailController::class, 'destroyBatch'])->name('destroy.batch');
    });

    // Route Sub Menu Details
    Route::prefix('admin/sub-menu-detail')->name('sub-menu-detail.')->group(function () {
        Route::get('/', [SubMenuDetailController::class, 'index'])->name('index');
        Route::post('/store', [SubMenuDetailController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SubMenuDetailController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [SubMenuDetailController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SubMenuDetailController::class, 'destroy'])->name('destroy');
    });

    // Route Trash
    Route::get('/deleted/data', [AdminController::class, 'getDeletedRecords'])->name('deleted.data');
    Route::post('/deleted/restore', [AdminController::class, 'restoreRecord'])->name('deleted.restore');
    Route::post('/deleted/delete', [AdminController::class, 'deleteRecord'])->name('deleted.delete');
});

require __DIR__.'/auth.php';

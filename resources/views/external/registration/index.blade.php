@extends('layouts.auth.regist')

@section('content')

<div class="authentication-wrapper authentication-cover authentication-bg">
    <!-- Logo -->
    <a href="index.html" class="app-brand auth-cover-brand">
        <img src="{{ asset('assets/img/branding/logo.png') }}" alt="logo" class="img-fluid" width="100">
        <span class="app-brand-text demo text-heading fw-bold">Kainnova</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row">
        <!-- Left Text -->
        <div
            class="d-none d-lg-flex col-lg-4 align-items-center justify-content-center p-5 auth-cover-bg-color position-relative auth-multisteps-bg-height">
            <img src="../../assets/img/illustrations/auth-register-multisteps-illustration.png"
                alt="auth-register-multisteps" class="img-fluid" width="280" />

            <img src="../../assets/img/illustrations/auth-register-multisteps-shape-light.png"
                alt="auth-register-multisteps" class="platform-bg"
                data-app-light-img="illustrations/auth-register-multisteps-shape-light.png"
                data-app-dark-img="illustrations/auth-register-multisteps-shape-dark.png" />
        </div>
        <!-- /Left Text -->

        <!--  Multi Steps Registration -->
        <div class="d-flex col-lg-8 align-items-center justify-content-center authentication-bg p-5">
            <div class="w-px-700">
                <div id="multiStepsValidation" class="bs-stepper border-none shadow-none mt-5">
                    <div class="bs-stepper-header border-none pt-12 px-0">
                        <div class="step" data-target="#accountDetailsValidation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ti ti-user ti-md"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Akun</span>
                                    <span class="bs-stepper-subtitle">Detail Akun</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#personalInfoValidation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ti ti-file-analytics ti-md"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Identitas</span>
                                    <span class="bs-stepper-subtitle">Data Pribadi</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#addressInfoValidation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ti ti-map-pin ti-md"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Alamat</span>
                                    <span class="bs-stepper-subtitle">Data Alamat</span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content px-0">
                        <form id="multiStepsForm" enctype="multipart/form-data">
                            @csrf
                            <!-- Account Details -->
                            <div id="accountDetailsValidation" class="content">
                                <div class="content-header mb-6">
                                    <h4 class="mb-0">Informasi Akun</h4>
                                    <p class="mb-0">Masukkan Detail Akun Anda</p>
                                </div>
                                <div class="row g-6">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="name">Nama Lengkap</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Masukkan Nama Lengkap" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="Masukkan Email Anda" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6 form-password-toggle">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="password2" required />
                                            <span class="input-group-text cursor-pointer" id="password2"><i
                                                    class="ti ti-eye-off"></i></span>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="password_confirmation2" required />
                                            <span class="input-group-text cursor-pointer" id="password_confirmation2"><i
                                                    class="ti ti-eye-off"></i></span>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" type="button" disabled>
                                            <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Sebelumnya</span>
                                        </button>
                                        <button class="btn btn-primary btn-next" type="button">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Selanjutnya</span>
                                            <i class="ti ti-arrow-right ti-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Personal Info -->
                            <div id="personalInfoValidation" class="content">
                                <div class="content-header mb-6">
                                    <h4 class="mb-0">Informasi Identitas</h4>
                                    <p class="mb-0">Masukkan Data Identitas Anda</p>
                                </div>
                                <div class="row g-6">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select select2" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="kode_pos">Kode Pos</label>
                                        <input type="text" id="kode_pos" name="kode_pos" class="form-control" 
                                               placeholder="Masukkan Kode Pos" maxlength="5" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="nik">NIK</label>
                                        <input type="text" id="nik" name="nik" class="form-control" 
                                               placeholder="Masukkan NIK" maxlength="16" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="kk">No. Kartu Keluarga</label>
                                        <input type="text" id="kk" name="kk" class="form-control" 
                                               placeholder="Masukkan No. KK" maxlength="16" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                        <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" 
                                               placeholder="Masukkan Tempat Lahir" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="no_hp">No. HP</label>
                                        <input type="text" id="no_hp" name="no_hp" class="form-control" 
                                               placeholder="Masukkan No. HP" />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="pekerjaan">Pekerjaan</label>
                                        <input type="text" id="pekerjaan" name="pekerjaan" class="form-control" 
                                               placeholder="Masukkan Pekerjaan" />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="penghasilan">Penghasilan (Rp)</label>
                                        <input type="number" id="penghasilan" name="penghasilan" class="form-control" 
                                               placeholder="Masukkan Penghasilan" />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="foto_ktp">Foto KTP</label>
                                        <input type="file" name="foto_ktp" id="foto_ktp" class="form-control"
                                               accept="image/*" />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="foto_kk">Foto Kartu Keluarga</label>
                                        <input type="file" name="foto_kk" id="foto_kk" class="form-control"
                                               accept="image/*" />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" type="button">
                                            <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Sebelumnya</span>
                                        </button>
                                        <button class="btn btn-primary btn-next" type="button">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Selanjutnya</span>
                                            <i class="ti ti-arrow-right ti-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Address Info -->
                            <div id="addressInfoValidation" class="content">
                                <div class="content-header mb-6">
                                    <h4 class="mb-0">Informasi Alamat</h4>
                                    <p class="mb-0">Masukkan Data Alamat Anda</p>
                                </div>
                                <div class="row g-6">
                                    <div class="col-12">
                                        <label class="form-label" for="alamat">Alamat Lengkap</label>
                                        <textarea id="alamat" name="alamat" class="form-control" rows="3"
                                                  placeholder="Masukkan Alamat Lengkap" required></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="rt">RT</label>
                                        <input type="text" id="rt" name="rt" class="form-control" 
                                               placeholder="Masukkan RT" maxlength="3" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="rw">RW</label>
                                        <input type="text" id="rw" name="rw" class="form-control" 
                                               placeholder="Masukkan RW" maxlength="3" required />
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="provinsi">Provinsi</label>
                                        <select id="provinsi" name="id_provinsi" class="form-select select2" required>
                                            <option value="">Pilih Provinsi</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="kota">Kota/Kabupaten</label>
                                        <select id="kota" name="id_kota" class="form-select select2" required disabled>
                                            <option value="">Pilih Kota/Kabupaten</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="kecamatan">Kecamatan</label>
                                        <select id="kecamatan" name="id_kecamatan" class="form-select select2" required disabled>
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="kelurahan">Kelurahan/Desa</label>
                                        <select id="kelurahan" name="id_kelurahan" class="form-select select2" required disabled>
                                            <option value="">Pilih Kelurahan/Desa</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" type="button">
                                            <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Sebelumnya</span>
                                        </button>
                                        <button class="btn btn-success" type="submit" id="btn-submit">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Daftar</span>
                                            <i class="ti ti-check ti-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Multi Steps Registration -->
    </div>
</div>
@endsection

@section('script')
<script>
    // Check selected custom option
    window.Helpers.initCustomOptionCheck();
</script>
<script src="{{ asset('assets/ajax/registration.js') }}"></script>
@endsection

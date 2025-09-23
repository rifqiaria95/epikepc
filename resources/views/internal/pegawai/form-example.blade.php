@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Form Pegawai dengan Hierarki Dropdown</h5>
        </div>
        <div class="card-body">
            <form id="formPegawai" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nm_pegawai" class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" id="nm_pegawai" name="nm_pegawai" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_perusahaan" class="form-label">Perusahaan</label>
                            <select id="id_perusahaan" name="id_perusahaan" class="form-select" required>
                                <option value="" selected disabled>Pilih Perusahaan</option>
                                @foreach ($perusahaan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_cabang" class="form-label">Cabang</label>
                            <select id="id_cabang" name="id_cabang" class="form-select" required>
                                <option value="" selected disabled>Pilih Cabang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_divisi" class="form-label">Divisi</label>
                            <select id="id_divisi" name="id_divisi" class="form-select" required>
                                <option value="" selected disabled>Pilih Divisi</option>
                                @foreach ($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_departemen" class="form-label">Departemen</label>
                            <select id="id_departemen" name="id_departemen" class="form-select" required>
                                <option value="" selected disabled>Pilih Departemen</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_jabatan" class="form-label">Jabatan</label>
                            <select id="id_jabatan" name="id_jabatan" class="form-select" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                @foreach ($jabatan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="foto_pegawai" class="form-label">Foto Pegawai</label>
                            <input type="file" class="form-control" id="foto_pegawai" name="foto_pegawai" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_provinsi" class="form-label">Provinsi</label>
                            <select id="id_provinsi" name="id_provinsi" class="form-select">
                                <option value="" selected disabled>Pilih Provinsi</option>
                                @foreach ($provinsi as $p)
                                    <option value="{{ $p->id_provinsi }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_kota" class="form-label">Kota</label>
                            <select id="id_kota" name="id_kota" class="form-select">
                                <option value="" selected disabled>Pilih Kota</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-secondary btn-reset">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ url('assets/js/hierarchy-dropdown.js') }}"></script>
<script>
$(document).ready(function() {
    // Form submission
    $('#formPegawai').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '/pegawai/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message);
                    $('#formPegawai')[0].reset();
                    // Reset dropdowns
                    $('#id_cabang, #id_departemen, #id_kota').empty().append('<option value="" selected disabled>Pilih...</option>');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('Terjadi kesalahan saat menyimpan data');
                }
            }
        });
    });

    // Reset form
    $('.btn-reset').on('click', function() {
        $('#formPegawai')[0].reset();
        $('#id_cabang, #id_departemen, #id_kota').empty().append('<option value="" selected disabled>Pilih...</option>');
    });
});
</script>
@endsection 
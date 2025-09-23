// Hierarki Dropdown Functions
$(document).ready(function() {
    
    // Hierarki Perusahaan -> Cabang (untuk form pegawai)
    $('#pegawai-perusahaan').on('change', function() {
        let perusahaanId = $(this).val();
        let cabangSelect = $('#pegawai-cabang');
        
        // Reset cabang dropdown
        cabangSelect.empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        if (perusahaanId) {
            $.ajax({
                url: `/hrd/pegawai/get-cabang/${perusahaanId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(cabang) {
                            cabangSelect.append(`<option value="${cabang.id}">${cabang.nama_cabang}</option>`);
                        });
                    } else {
                        cabangSelect.append('<option value="" disabled>Tidak ada cabang untuk perusahaan ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data cabang!');
                }
            });
        }
    });

    // Hierarki Divisi -> Departemen (untuk form pegawai)
    $('#pegawai-divisi').on('change', function() {
        let divisiId = $(this).val();
        let departemenSelect = $('#pegawai-departemen');
        
        // Reset departemen dropdown
        departemenSelect.empty().append('<option value="" selected disabled>Pilih Departemen</option>');
        
        if (divisiId) {
            $.ajax({
                url: `/hrd/pegawai/get-departemen/${divisiId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(departemen) {
                            departemenSelect.append(`<option value="${departemen.id}">${departemen.nama_departemen}</option>`);
                        });
                    } else {
                        departemenSelect.append('<option value="" disabled>Tidak ada departemen untuk divisi ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data departemen!');
                }
            });
        }
    });

    // Hierarki Perusahaan -> Cabang (untuk form example)
    $('#id_perusahaan').on('change', function() {
        let perusahaanId = $(this).val();
        let cabangSelect = $('#id_cabang');
        
        // Reset cabang dropdown
        cabangSelect.empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        if (perusahaanId) {
            $.ajax({
                url: `/hrd/pegawai/get-cabang/${perusahaanId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(cabang) {
                            cabangSelect.append(`<option value="${cabang.id}">${cabang.nama_cabang}</option>`);
                        });
                    } else {
                        cabangSelect.append('<option value="" disabled>Tidak ada cabang untuk perusahaan ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data cabang!');
                }
            });
        }
    });

    // Hierarki Divisi -> Departemen (untuk form example)
    $('#id_divisi').on('change', function() {
        let divisiId = $(this).val();
        let departemenSelect = $('#id_departemen');
        
        // Reset departemen dropdown
        departemenSelect.empty().append('<option value="" selected disabled>Pilih Departemen</option>');
        
        if (divisiId) {
            $.ajax({
                url: `/hrd/pegawai/get-departemen/${divisiId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(departemen) {
                            departemenSelect.append(`<option value="${departemen.id}">${departemen.nama_departemen}</option>`);
                        });
                    } else {
                        departemenSelect.append('<option value="" disabled>Tidak ada departemen untuk divisi ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data departemen!');
                }
            });
        }
    });

    // Hierarki untuk form cabang (Perusahaan -> Cabang)
    $('#form_perusahaan').on('change', function() {
        let perusahaanId = $(this).val();
        let cabangSelect = $('#form_cabang');
        
        // Reset cabang dropdown
        cabangSelect.empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        if (perusahaanId) {
            $.ajax({
                url: `/company/cabang/get-by-perusahaan/${perusahaanId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(cabang) {
                            cabangSelect.append(`<option value="${cabang.id}">${cabang.nama_cabang}</option>`);
                        });
                    } else {
                        cabangSelect.append('<option value="" disabled>Tidak ada cabang untuk perusahaan ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data cabang!');
                }
            });
        }
    });

    // Hierarki untuk form departemen (Divisi -> Departemen)
    $('#form_divisi').on('change', function() {
        let divisiId = $(this).val();
        let departemenSelect = $('#form_departemen');
        
        // Reset departemen dropdown
        departemenSelect.empty().append('<option value="" selected disabled>Pilih Departemen</option>');
        
        if (divisiId) {
            $.ajax({
                url: `/hrd/departemen/get-by-divisi/${divisiId}`,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(departemen) {
                            departemenSelect.append(`<option value="${departemen.id}">${departemen.nama_departemen}</option>`);
                        });
                    } else {
                        departemenSelect.append('<option value="" disabled>Tidak ada departemen untuk divisi ini</option>');
                    }
                },
                error: function() {
                    toastr.error('Gagal mengambil data departemen!');
                }
            });
        }
    });

    // Reset hierarki dropdowns when form is reset
    $('.btn-reset').on('click', function() {
        // Reset cabang dropdown
        $('#id_cabang, #form_cabang, #pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        // Reset departemen dropdown
        $('#id_departemen, #form_departemen, #pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
    });

    // Reset hierarki dropdowns when modal is closed
    $('.offcanvas').on('hidden.bs.offcanvas', function() {
        // Reset cabang dropdown
        $('#id_cabang, #form_cabang, #pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        // Reset departemen dropdown
        $('#id_departemen, #form_departemen, #pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
    });

    // Reset hierarki dropdowns when modal is closed (untuk modal pegawai)
    $('#tambahModal').on('hidden.bs.modal', function() {
        // Reset cabang dropdown
        $('#pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');
        
        // Reset departemen dropdown
        $('#pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
    });
}); 
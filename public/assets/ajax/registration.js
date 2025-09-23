$(document).ready(function () {
    // Initialize Select2 after page loads
    setTimeout(function() {
        $('.select2').select2({
            dropdownParent: $('#multiStepsForm'),
            width: '100%'
        });
    }, 100);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Form validation config - dipindah ke scope global
    const validationConfig = {
        step1: {
            'name'                 : 'required',
            'email'                : 'required|email',
            'password'             : 'required|min:8',
            'password_confirmation': 'required|same:password'
        },
        step2: {
            'nik'          : 'required|numeric|min:16',
            'kk'           : 'required|numeric|min:16',
            'tempat_lahir' : 'required',
            'tanggal_lahir': 'required',
            'jenis_kelamin': 'required',
            'kode_pos'     : 'required',
            'no_hp'        : 'required',
            'pekerjaan'    : 'required',
            'penghasilan'  : 'required'
        },
        step3: {
            'alamat'      : 'required',
            'rt'          : 'required',
            'rw'          : 'required',
            'id_provinsi' : 'required',
            'id_kota'     : 'required',
            'id_kecamatan': 'required',
            'id_kelurahan': 'required'
        }
    };

    // Initialize BS Stepper
    let wizardMultiSteps = document.querySelector('#multiStepsValidation');
    let multiStepsForm = document.querySelector('#multiStepsForm');
    let stepperMultiSteps;

    if (wizardMultiSteps) {
        stepperMultiSteps = new Stepper(wizardMultiSteps, {
            linear: true
        });

        // Handle next button
        $(multiStepsForm).on('click', '.btn-next', function (e) {
            e.preventDefault();
            let currentStep = stepperMultiSteps._currentIndex + 1;
            
            if (validateStep(currentStep)) {
                stepperMultiSteps.next();
            }
        });

        // Handle previous button
        $(multiStepsForm).on('click', '.btn-prev', function (e) {
            e.preventDefault();
            stepperMultiSteps.previous();
        });

        // Validate step function
        function validateStep(step) {
            let valid = true;
            let stepConfig;
            let stepFields;

            switch(step) {
                case 1:
                    stepConfig = validationConfig.step1;
                    stepFields = $('#accountDetailsValidation input');
                    break;
                case 2:
                    stepConfig = validationConfig.step2;
                    stepFields = $('#personalInfoValidation input, #personalInfoValidation select, #personalInfoValidation textarea');
                    break;
                case 3:
                    stepConfig = validationConfig.step3;
                    stepFields = $('#addressInfoValidation input, #addressInfoValidation select, #addressInfoValidation textarea');
                    break;
            }

            // Clear previous errors
            stepFields.removeClass('is-invalid');
            stepFields.siblings('.invalid-feedback').text('');

            stepFields.each(function() {
                let fieldName = $(this).attr('name');
                let fieldValue = $(this).val();
                let rules = stepConfig[fieldName];

                if (rules) {
                    let ruleArray = rules.split('|');
                    
                    for (let rule of ruleArray) {
                        if (rule === 'required' && (!fieldValue || fieldValue.trim() === '')) {
                            showFieldError($(this), 'Field ini wajib diisi');
                            valid = false;
                            break;
                        } else if (rule === 'email' && fieldValue && !isValidEmail(fieldValue)) {
                            showFieldError($(this), 'Format email tidak valid');
                            valid = false;
                            break;
                        } else if (rule.startsWith('min:') && fieldValue) {
                            let minLength = parseInt(rule.split(':')[1]);
                            if (fieldValue.length < minLength) {
                                showFieldError($(this), `Minimal ${minLength} karakter`);
                                valid = false;
                                break;
                            }
                        } else if (rule.startsWith('same:') && fieldValue) {
                            let compareField = rule.split(':')[1];
                            let compareValue = $(`[name="${compareField}"]`).val();
                            if (fieldValue !== compareValue) {
                                showFieldError($(this), 'Password konfirmasi tidak cocok');
                                valid = false;
                                break;
                            }
                        } else if (rule === 'numeric' && fieldValue && !/^\d+$/.test(fieldValue)) {
                            showFieldError($(this), 'Hanya boleh berisi angka');
                            valid = false;
                            break;
                        }
                    }
                }
            });

            return valid;
        }

        function showFieldError(field, message) {
            field.addClass('is-invalid');
            field.siblings('.invalid-feedback').text(message);
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    } else {
        console.error('Element #multiStepsValidation tidak ditemukan');
    }

    // Load provinsi data on page load
    loadProvinsi();

    // Load provinsi
    function loadProvinsi() {
        $.ajax({
            url: '/api/provinsi',
            method: 'GET',
            success: function(response) {
                console.log('Provinsi response:', response);
                let provinsiSelect = $('#provinsi');
                provinsiSelect.empty();
                provinsiSelect.append('<option value="">Pilih Provinsi</option>');
                
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        provinsiSelect.append('<option value="' + item.id_provinsi + '">' + item.name + '</option>');
                    });
                } else {
                    console.warn('Tidak ada data provinsi');
                }
                
                // Refresh select2 setelah menambahkan option
                provinsiSelect.trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.error('Error loading provinsi:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    // Handle provinsi change
    $('#provinsi').on('change', function() {
        let id_provinsi = $(this).val();
        console.log('Provinsi selected:', id_provinsi);
        
        // Reset dependent dropdowns first
        resetDropdown(['kota', 'kecamatan', 'kelurahan']);
        
        if (id_provinsi && id_provinsi !== '' && id_provinsi !== 'undefined' && !isNaN(id_provinsi)) {
            loadKota(id_provinsi);
            $('#kota').prop('disabled', false);
        } else {
            console.log('Invalid provinsi ID:', id_provinsi);
        }
    });

    // Load kota/kabupaten
    function loadKota(id_provinsi) {
        // Double check the parameter
        if (!id_provinsi || id_provinsi === 'undefined' || isNaN(id_provinsi)) {
            console.error('Invalid id_provinsi:', id_provinsi);
            return;
        }
        
        $.ajax({
            url: '/api/kota/' + id_provinsi,
            method: 'GET',
            success: function(response) {
                console.log('Kota response:', response);
                let kotaSelect = $('#kota');
                kotaSelect.empty();
                kotaSelect.append('<option value="">Pilih Kota/Kabupaten</option>');
                
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        kotaSelect.append('<option value="' + item.id_kota + '">' + item.name + '</option>');
                    });
                    kotaSelect.prop('disabled', false);
                } else {
                    console.warn('Tidak ada data kota untuk provinsi:', id_provinsi);
                    kotaSelect.prop('disabled', true);
                }
                kotaSelect.trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.error('Error loading kota:', error);
                console.error('Response:', xhr.responseText);
                $('#kota').prop('disabled', true);
            }
        });
    }

    // Handle kota change
    $('#kota').on('change', function() {
        let id_kota = $(this).val();
        console.log('Kota selected:', id_kota);
        
        // Reset dependent dropdowns first
        resetDropdown(['kecamatan', 'kelurahan']);
        
        if (id_kota && id_kota !== '' && id_kota !== 'undefined' && !isNaN(id_kota)) {
            loadKecamatan(id_kota);
            $('#kecamatan').prop('disabled', false);
        } else {
            console.log('Invalid kota ID:', id_kota);
        }
    });

    // Load kecamatan
    function loadKecamatan(id_kota) {
        // Double check the parameter
        if (!id_kota || id_kota === 'undefined' || isNaN(id_kota)) {
            console.error('Invalid id_kota:', id_kota);
            return;
        }
        
        $.ajax({
            url: '/api/kecamatan/' + id_kota,
            method: 'GET',
            success: function(response) {
                console.log('Kecamatan response:', response);
                let kecamatanSelect = $('#kecamatan');
                kecamatanSelect.empty();
                kecamatanSelect.append('<option value="">Pilih Kecamatan</option>');
                
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        kecamatanSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    kecamatanSelect.prop('disabled', false);
                } else {
                    console.warn('Tidak ada data kecamatan untuk kota:', id_kota);
                    kecamatanSelect.prop('disabled', true);
                }
                kecamatanSelect.trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.error('Error loading kecamatan:', error);
                console.error('Response:', xhr.responseText);
                $('#kecamatan').prop('disabled', true);
            }
        });
    }

    // Handle kecamatan change
    $('#kecamatan').on('change', function() {
        let id_kecamatan = $(this).val();
        console.log('Kecamatan selected:', id_kecamatan);
        
        // Reset dependent dropdowns first
        resetDropdown(['kelurahan']);
        
        if (id_kecamatan && id_kecamatan !== '' && id_kecamatan !== 'undefined' && !isNaN(id_kecamatan)) {
            loadKelurahan(id_kecamatan);
            $('#kelurahan').prop('disabled', false);
        } else {
            console.log('Invalid kecamatan ID:', id_kecamatan);
        }
    });

    // Load kelurahan
    function loadKelurahan(id_kecamatan) {
        // Double check the parameter
        if (!id_kecamatan || id_kecamatan === 'undefined' || isNaN(id_kecamatan)) {
            console.error('Invalid id_kecamatan:', id_kecamatan);
            return;
        }
        
        $.ajax({
            url: '/api/kelurahan/' + id_kecamatan,
            method: 'GET',
            success: function(response) {
                console.log('Kelurahan response:', response);
                let kelurahanSelect = $('#kelurahan');
                kelurahanSelect.empty();
                kelurahanSelect.append('<option value="">Pilih Kelurahan/Desa</option>');
                
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        kelurahanSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    kelurahanSelect.prop('disabled', false);
                } else {
                    console.warn('Tidak ada data kelurahan untuk kecamatan:', id_kecamatan);
                    kelurahanSelect.prop('disabled', true);
                }
                kelurahanSelect.trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.error('Error loading kelurahan:', error);
                console.error('Response:', xhr.responseText);
                $('#kelurahan').prop('disabled', true);
            }
        });
    }

    // Reset dropdown function
    function resetDropdown(dropdowns) {
        dropdowns.forEach(function(dropdown) {
            $('#' + dropdown).empty()
                .append('<option value="">Pilih ' + getDropdownLabel(dropdown) + '</option>')
                .prop('disabled', true)
                .trigger('change.select2');
        });
    }

    function getDropdownLabel(dropdown) {
        switch(dropdown) {
            case 'kota': return 'Kota/Kabupaten';
            case 'kecamatan': return 'Kecamatan';
            case 'kelurahan': return 'Kelurahan/Desa';
            default: return '';
        }
    }

    // Handle form submission
    $('#multiStepsForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submitted, starting validation...');
        
        // Debug: log semua form data
        let formData = new FormData(this);
        console.log('Form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Cek validasi step 3 dengan lebih detail
        if (!validateStepDetailed(3)) {
            console.log('Step 3 validation failed');
            return;
        }
        
        console.log('Step 3 validation passed, sending AJAX...');

        let submitBtn = $('#btn-submit');
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mendaftar...');

        $.ajax({
            url: '/registration/store',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pendaftaran berhasil! Silakan tunggu verifikasi admin.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/login';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat mendaftar'
                    });
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    
                    $.each(errors, function(field, messages) {
                        let fieldElement = $('[name="' + field + '"]');
                        if (fieldElement.length) {
                            fieldElement.addClass('is-invalid');
                            fieldElement.siblings('.invalid-feedback').text(messages[0]);
                        }
                        errorMessages.push(messages[0]);
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Error!',
                        text: 'Harap periksa kembali data yang dimasukkan'
                    });
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server'
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Daftar</span><i class="ti ti-check ti-xs"></i>');
            }
        });
    });

    // Password visibility toggle
    $('.form-password-toggle .input-group-text').on('click', function() {
        let input = $(this).siblings('input');
        let icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('ti-eye-off').addClass('ti-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('ti-eye').addClass('ti-eye-off');
        }
    });

    // Format NIK and KK input (only numbers)
    $('#nik, #kk').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Format phone number
    $('#no_hp').on('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, '');
    });

    // Format RT/RW input (only numbers)
    $('#rt, #rw').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validasi step dengan detail logging - dipindah ke scope global
    function validateStepDetailed(step) {
        console.log('Validating step:', step);
        let valid = true;
        let stepConfig;
        let stepFields;

        switch(step) {
            case 1:
                stepConfig = validationConfig.step1;
                stepFields = $('#accountDetailsValidation input');
                break;
            case 2:
                stepConfig = validationConfig.step2;
                stepFields = $('#personalInfoValidation input, #personalInfoValidation select, #personalInfoValidation textarea');
                break;
            case 3:
                stepConfig = validationConfig.step3;
                stepFields = $('#addressInfoValidation input, #addressInfoValidation select, #addressInfoValidation textarea');
                break;
        }

        console.log('Step config:', stepConfig);
        console.log('Step fields count:', stepFields.length);

        // Clear previous errors
        stepFields.removeClass('is-invalid');
        stepFields.siblings('.invalid-feedback').text('');

        stepFields.each(function() {
            let fieldName = $(this).attr('name');
            let fieldValue = $(this).val();
            let rules = stepConfig[fieldName];

            console.log('Checking field:', fieldName, 'value:', fieldValue, 'rules:', rules);

            if (rules) {
                let ruleArray = rules.split('|');
                
                for (let rule of ruleArray) {
                    if (rule === 'required' && (!fieldValue || fieldValue.trim() === '')) {
                        console.log('Field failed required validation:', fieldName);
                        showFieldErrorGlobal($(this), 'Field ini wajib diisi');
                        valid = false;
                        break;
                    } else if (rule === 'email' && fieldValue && !isValidEmailGlobal(fieldValue)) {
                        console.log('Field failed email validation:', fieldName);
                        showFieldErrorGlobal($(this), 'Format email tidak valid');
                        valid = false;
                        break;
                    } else if (rule.startsWith('min:') && fieldValue) {
                        let minLength = parseInt(rule.split(':')[1]);
                        if (fieldValue.length < minLength) {
                            console.log('Field failed min length validation:', fieldName);
                            showFieldErrorGlobal($(this), `Minimal ${minLength} karakter`);
                            valid = false;
                            break;
                        }
                    } else if (rule.startsWith('same:') && fieldValue) {
                        let compareField = rule.split(':')[1];
                        let compareValue = $(`[name="${compareField}"]`).val();
                        if (fieldValue !== compareValue) {
                            console.log('Field failed same validation:', fieldName);
                            showFieldErrorGlobal($(this), 'Password konfirmasi tidak cocok');
                            valid = false;
                            break;
                        }
                    } else if (rule === 'numeric' && fieldValue && !/^\d+$/.test(fieldValue)) {
                        console.log('Field failed numeric validation:', fieldName);
                        showFieldErrorGlobal($(this), 'Hanya boleh berisi angka');
                        valid = false;
                        break;
                    }
                }
            }
        });

        console.log('Step validation result:', valid);
        return valid;
    }

    function showFieldErrorGlobal(field, message) {
        field.addClass('is-invalid');
        field.siblings('.invalid-feedback').text(message);
    }

    function isValidEmailGlobal(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var forms = document.querySelectorAll('#homepage-consultation-form, #contact-consultation-form');

                forms.forEach(function (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        var submitButton = form.querySelector('button[type="submit"]');
                        var alertBox = form.querySelector('.consultation-form-alert');
                        var originalButtonText = submitButton ? submitButton.innerHTML : '';

                        form.querySelectorAll('.consultation-error').forEach(function (item) {
                            item.textContent = '';
                        });

                        if (alertBox) {
                            alertBox.className = 'consultation-form-alert d-none';
                            alertBox.textContent = '';
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="btn-wrap"><span class="btn-text1">Mengirim...</span><span class="btn-text2">Mengirim...</span></span>';
                        }

                        try {
                            var response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                },
                                body: new FormData(form),
                            });

                            var payload = await response.json();

                            if (!response.ok) {
                                if (response.status === 422 && payload.errors) {
                                    Object.keys(payload.errors).forEach(function (field) {
                                        var errorNode = form.querySelector('.consultation-error[data-field="' + field + '"]');

                                        if (errorNode) {
                                            errorNode.textContent = payload.errors[field][0];
                                        }
                                    });
                                } else if (alertBox) {
                                    alertBox.className = 'consultation-form-alert alert alert-danger';
                                    alertBox.textContent = payload.message || 'An error occurred while sending the consultation request.';
                                }

                                return;
                            }

                            form.reset();

                            if (alertBox) {
                                alertBox.className = 'consultation-form-alert alert alert-success';
                                alertBox.textContent = payload.message;
                            }
                        } catch (error) {
                            if (alertBox) {
                                alertBox.className = 'consultation-form-alert alert alert-danger';
                                alertBox.textContent = 'Unable to connect to the server. Please try again.';
                            }
                        } finally {
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce

/* ============================================
   Aurex - Main JavaScript
   ============================================ */

$(document).ready(function () {

    // ---------- Loading Screen ----------
    setTimeout(function () {
        $('#loading-screen').addClass('hidden');
    }, 1200);

    // ---------- Navbar Scroll Effect ----------
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 50) {
            $('#mainNav').addClass('scrolled');
        } else {
            $('#mainNav').removeClass('scrolled');
        }
    });

    // ---------- Smooth Scrolling ----------
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // ---------- Flash Message Auto-dismiss ----------
    setTimeout(function () {
        $('.flash-message').fadeOut(400, function () {
            $(this).remove();
        });
    }, 4000);

    // ---------- Product Card Hover ----------
    $(document).on('mouseenter', '.product-card', function () {
        $(this).find('.product-hover-overlay').stop().fadeIn(200);
    }).on('mouseleave', '.product-card', function () {
        $(this).find('.product-hover-overlay').stop().fadeOut(200);
    });

    // ---------- Size Selection ----------
    $(document).on('click', '.size-btn', function () {
        $('.size-btn').removeClass('active');
        $(this).addClass('active');
    });

    // ---------- Quantity Selector ----------
    $(document).on('click', '.qty-minus', function () {
        var $input = $(this).siblings('.qty-input');
        var val = parseInt($input.val());
        if (val > 1) {
            $input.val(val - 1);
        }
    });

    $(document).on('click', '.qty-plus', function () {
        var $input = $(this).siblings('.qty-input');
        var val = parseInt($input.val());
        if (val < 10) {
            $input.val(val + 1);
        }
    });

    // ---------- Product Thumbnails ----------
    $(document).on('click', '.thumbnail', function () {
        var imgSrc = $(this).find('img').attr('src');
        if (imgSrc) {
            $('.main-image img').attr('src', imgSrc);
        }
        $('.thumbnail').removeClass('active');
        $(this).addClass('active');
    });

    // ---------- Description Tabs ----------
    $(document).on('click', '.desc-tab', function () {
        var target = $(this).data('tab');
        $('.desc-tab').removeClass('active');
        $(this).addClass('active');
        $('.desc-pane').removeClass('active');
        $('#' + target).addClass('active');
    });

    // ---------- OTP Input Auto-focus ----------
    $(document).on('input', '.otp-input', function () {
        if (this.value.length === this.maxLength) {
            $(this).next('.otp-input').focus();
        }
    });

    $(document).on('keydown', '.otp-input', function (e) {
        if (e.key === 'Backspace' && this.value === '') {
            $(this).prev('.otp-input').focus();
        }
    });

    // ---------- OTP Send ----------
    $(document).on('click', '#sendOtpBtn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var identifier = $('#identifier').val();

        if (!identifier) {
            showError('identifier', 'Please enter your email or phone');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');

        $.ajax({
            url: 'send-otp.php',
            method: 'POST',
            data: { identifier: identifier },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('.otp-section').addClass('show');
                    $btn.html('OTP Sent <i class="fas fa-check ms-1"></i>');
                    if (res.otp) {
                        $('#devOtpDisplay').text('Your OTP: ' + res.otp).show();
                    }
                    startOtpTimer();
                } else {
                    showError('identifier', res.error);
                    $btn.prop('disabled', false).html('Send OTP');
                }
            },
            error: function () {
                showError('identifier', 'Something went wrong. Try again.');
                $btn.prop('disabled', false).html('Send OTP');
            }
        });
    });

    // ---------- OTP Verify ----------
    $(document).on('click', '#verifyOtpBtn', function (e) {
        e.preventDefault();
        var otp = '';
        $('.otp-input').each(function () {
            otp += $(this).val();
        });

        if (otp.length < 6) {
            alert('Please enter the complete 6-digit OTP');
            return;
        }

        var identifier = $('#identifier').val();

        $.ajax({
            url: 'verify-otp.php',
            method: 'POST',
            data: { identifier: identifier, otp: otp },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    window.location.href = res.redirect || 'index.php';
                } else {
                    alert(res.error || 'Invalid OTP');
                }
            },
            error: function () {
                alert('Something went wrong. Try again.');
            }
        });
    });

    // ---------- OTP Timer ----------
    function startOtpTimer() {
        var seconds = 60;
        var $timer = $('#otpTimer');
        var interval = setInterval(function () {
            seconds--;
            $timer.text('Resend in ' + seconds + 's');
            if (seconds <= 0) {
                clearInterval(interval);
                $timer.html('<a href="#" id="resendOtp">Resend OTP</a>');
            }
        }, 1000);
    }

    // ---------- Resend OTP ----------
    $(document).on('click', '#resendOtp', function (e) {
        e.preventDefault();
        var identifier = $('#identifier').val();
        $.ajax({
            url: 'send-otp.php',
            method: 'POST',
            data: { identifier: identifier },
            dataType: 'json',
            success: function (res) {
                if (res.success && res.otp) {
                    $('#devOtpDisplay').text('Your OTP: ' + res.otp).show();
                    startOtpTimer();
                }
            }
        });
    });

    // ---------- Add to Cart (AJAX) ----------
    $(document).on('click', '.btn-add-cart', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var size = $('.size-btn.active').data('size') || 'M';
        var quantity = parseInt($('.qty-input').val()) || 1;

        $.ajax({
            url: 'add-to-cart.php',
            method: 'POST',
            data: { product_id: productId, size: size, quantity: quantity },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    showFlash('success', res.message);
                    updateCartBadge(res.cart_count);
                } else {
                    showFlash('error', res.error);
                }
            },
            error: function () {
                showFlash('error', 'Something went wrong');
            }
        });
    });

    // ---------- Cart Quantity Update ----------
    $(document).on('click', '.cart-qty-btn', function () {
        var cartKey = $(this).data('key');
        var action = $(this).data('action');

        $.ajax({
            url: 'update-cart.php',
            method: 'POST',
            data: { key: cartKey, action: action },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    location.reload();
                }
            }
        });
    });

    // ---------- Remove from Cart ----------
    $(document).on('click', '.cart-item-remove', function () {
        var cartKey = $(this).data('key');

        if (confirm('Remove this item?')) {
            $.ajax({
                url: 'remove-from-cart.php',
                method: 'POST',
                data: { key: cartKey },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        location.reload();
                    }
                }
            });
        }
    });

    // ---------- Login Form ----------
    $(document).on('submit', '#loginForm', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Signing In...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    window.location.href = res.redirect || 'index.php';
                } else {
                    showFlash('error', res.error);
                    $btn.prop('disabled', false).html('Sign In');
                }
            },
            error: function () {
                showFlash('error', 'Something went wrong');
                $btn.prop('disabled', false).html('Sign In');
            }
        });
    });

    // ---------- Register Form ----------
    $(document).on('submit', '#registerForm', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var password = $form.find('[name="password"]').val();
        var confirm = $form.find('[name="confirm_password"]').val();

        if (password !== confirm) {
            showError('confirm_password', 'Passwords do not match');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (res.require_otp) {
                        $('.otp-section').addClass('show');
                        $btn.prop('disabled', false).html('Create Account');
                        if (res.otp) {
                            $('#devOtpDisplay').text('Your OTP: ' + res.otp).show();
                        }
                        startOtpTimer();
                    } else {
                        window.location.href = res.redirect || 'index.php';
                    }
                } else {
                    showFlash('error', res.error);
                    $btn.prop('disabled', false).html('Create Account');
                }
            },
            error: function () {
                showFlash('error', 'Something went wrong');
                $btn.prop('disabled', false).html('Create Account');
            }
        });
    });

    // ---------- Image Preview (Admin) ----------
    $(document).on('change', '#productImage', function () {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).addClass('show');
            };
            reader.readAsDataURL(file);
        }
    });

    // ---------- Admin Sidebar Toggle ----------
    $(document).on('click', '#adminToggle', function () {
        $('.admin-sidebar').toggleClass('show');
    });

    // ---------- Helper Functions ----------
    function showError(field, message) {
        var $field = $('[name="' + field + '"], #' + field);
        $field.addClass('error');
        if ($field.next('.error-text').length === 0) {
            $field.after('<div class="error-text">' + message + '</div>');
        } else {
            $field.next('.error-text').text(message);
        }
    }

    function showFlash(type, message) {
        var className = 'flash-' + type;
        var html = '<div class="flash-message ' + className + '">' + message + '</div>';
        $('body').append(html);
        setTimeout(function () {
            $('.flash-message').fadeOut(400, function () {
                $(this).remove();
            });
        }, 4000);
    }

    function updateCartBadge(count) {
        var $badge = $('.cart-badge');
        if (count > 0) {
            if ($badge.length) {
                $badge.text(count);
            } else {
                $('.nav-icon:has(.fa-shopping-bag)').append('<span class="cart-badge">' + count + '</span>');
            }
        } else {
            $badge.remove();
        }
    }

    // ---------- Scroll Animations ----------
    var observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                $(entry.target).addClass('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    $('.feature-card, .category-card, .product-card').each(function () {
        observer.observe(this);
    });

});

@once
<style>
    .payment-loading-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.8); z-index: 99999;
        justify-content: center; align-items: center;
    }
    .payment-loading-overlay.active { display: flex; }
    .payment-loading-content {
        text-align: center; color: white;
        background: rgba(255,255,255,0.1);
        padding: 40px; border-radius: 15px;
        backdrop-filter: blur(10px);
    }
    .payment-loading-spinner {
        border: 6px solid #f3f3f3; border-top: 6px solid #3498db;
        border-radius: 50%; width: 70px; height: 70px;
        animation: payment-spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    @keyframes payment-spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .payment-loading-text    { font-size: 18px; font-weight: 600; margin-top: 10px; }
    .payment-loading-subtext { font-size: 14px; margin-top: 8px; opacity: 0.8; }

    /* Camera Warning Modal */
    .camera-modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.85); z-index: 100000;
        justify-content: center; align-items: center;
        backdrop-filter: blur(8px);
    }
    .camera-modal-overlay.active { display: flex; }
    .camera-modal-card {
        background: #fff; border-radius: 24px;
        padding: 40px; max-width: 480px; width: 90%;
        text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        animation: modalFadeIn 0.3s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .camera-modal-icon {
        width: 80px; height: 80px; background: #fff1f2;
        border-radius: 50%; display: flex; align-items: center;
        justify-content: center; margin: 0 auto 24px;
    }
    .camera-modal-icon i { color: #e11d48; font-size: 36px; }
    .camera-modal-card h3 {
        color: #0f172a; font-size: 22px; font-weight: 700;
        margin-bottom: 12px;
    }
    .camera-modal-card p {
        color: #64748b; font-size: 15px; line-height: 1.6;
        margin-bottom: 24px;
    }
    .camera-modal-warning {
        background: #fffbeb; border: 1px solid #fef3c7;
        border-radius: 12px; padding: 16px; margin-bottom: 32px;
        text-align: left; display: flex; gap: 12px;
    }
    .camera-modal-warning i { color: #d97706; margin-top: 2px; }
    .camera-modal-warning span { color: #92400e; font-size: 13px; font-weight: 500; }
    .camera-modal-actions { display: flex; flex-direction: column; gap: 12px; }
    .btn-camera-continue {
        background: #0f172a; color: #fff; border: none;
        padding: 14px; border-radius: 12px; font-weight: 600;
        font-size: 15px; cursor: pointer; transition: all 0.2s;
    }
    .btn-camera-continue:hover { background: #1e293b; transform: translateY(-1px); }
    .btn-camera-exit {
        background: transparent; color: #64748b; border: 1px solid #e2e8f0;
        padding: 14px; border-radius: 12px; font-weight: 600;
        font-size: 15px; cursor: pointer; transition: all 0.2s;
    }
    .btn-camera-exit:hover { background: #f8fafc; color: #0f172a; }
</style>

<div class="payment-loading-overlay" id="paymentLoadingOverlay">
    <div class="payment-loading-content">
        <div class="payment-loading-spinner"></div>
        <div class="payment-loading-text">Processing Payment...</div>
        <div class="payment-loading-subtext">Please wait, do not close this window</div>
    </div>
</div>

<div class="camera-modal-overlay" id="cameraWarningModal">
    <div class="camera-modal-card">
        <div class="camera-modal-icon">
            <i class="fas fa-video-slash"></i>
        </div>
        <h3>Camera Access Required</h3>
        <p>A functional camera is mandatory for this qualification test. We were unable to detect an active camera device on your system.</p>
        
        <div class="camera-modal-warning">
            <i class="fas fa-info-circle"></i>
            <span>Important: Continuing without a camera may lead to your test attempt being rejected by the admin.</span>
        </div>

        <div class="camera-modal-actions">
            <button class="btn-camera-continue" id="cameraBtnContinue">Continue Anyway</button>
            <button class="btn-camera-exit" id="cameraBtnExit">Exit & Check Camera</button>
        </div>
    </div>
</div>
@endonce

<button class="btn btn-primary pay-btn" id="{{ $buttonId }}">
    {{ $buttonText }} <i class="fas fa-arrow-right"></i>
</button>

@once
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endonce

<script>
(function() {
    const buttonId     = '{{ $buttonId }}';
    const initiateRoute = '{{ $initiateRoute }}';
    const paymentName  = '{{ $paymentName }}';
    const requestBody  = @json($requestBody ?? []);
    const csrfToken    = '{{ csrf_token() }}';
    const checkCamera  = {{ ($checkCamera ?? false) ? 'true' : 'false' }};
    const buttonText   = '{{ $buttonText }}';

    // After successful payment, redirect to the test attempt route (not detail page)
    // The test_id comes back in the initiate response
    const fallbackRedirect = '{{ $redirectUrl }}';

    function showPaymentLoading() {
        document.getElementById('paymentLoadingOverlay').classList.add('active');
    }
    function hidePaymentLoading() {
        document.getElementById('paymentLoadingOverlay').classList.remove('active');
    }

    const cameraModal = document.getElementById('cameraWarningModal');
    const cameraBtnContinue = document.getElementById('cameraBtnContinue');
    const cameraBtnExit = document.getElementById('cameraBtnExit');

    document.getElementById(buttonId).addEventListener('click', async function() {
        const payBtn = this;

        // Camera Check
        if (checkCamera) {
            let cameraWorking = true;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                stream.getTracks().forEach(track => track.stop());
            } catch (err) {
                cameraWorking = false;
            }

            if (!cameraWorking) {
                cameraModal.classList.add('active');
                
                // Wait for user choice
                const userChoice = await new Promise((resolve) => {
                    cameraBtnContinue.onclick = () => {
                        cameraModal.classList.remove('active');
                        resolve('continue');
                    };
                    cameraBtnExit.onclick = () => {
                        cameraModal.classList.remove('active');
                        resolve('exit');
                    };
                });

                if (userChoice === 'exit') {
                    return;
                }
            }
        }

        payBtn.disabled = true;
        payBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Processing...`;

        fetch(initiateRoute, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: Object.keys(requestBody).length ? JSON.stringify(requestBody) : null
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                payBtn.disabled = false;
                payBtn.innerHTML = buttonText + ' <i class="fas fa-arrow-right"></i>';
                alert(data.message || 'Payment initiation failed');
                return;
            }

            const rzp = new Razorpay({
                key:      data.key,
                amount:   data.amount * 100,
                currency: 'INR',
                name:     paymentName,
                order_id: data.orderId || data.order_id,

                handler: function(response) {
                    showPaymentLoading();

                    // Verify payment signature on server
                    fetch('{{ route("supervisor.payment.verify") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            razorpay_order_id:   response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature:  response.razorpay_signature,
                        })
                    })
                    .then(r => r.json())
                    .then(verifyData => {
                       
                        if (verifyData.success) {
                            // Redirect directly to test attempt (not detail page)
                            // Use test_id returned from initiate response
                            const testId = data.test_id;
                            if (testId) {
                               hidePaymentLoading();
                                const attemptUrl = window.location.origin
                                    + '/supervisor/tests/' + testId + '/attempt';
                                window.location.href = attemptUrl;
                            } else {
                                window.location.href = fallbackRedirect;
                            }
                        } else {
                            alert('Payment verification failed. Please contact support.');
                            window.location.href = fallbackRedirect;
                        }
                    })
                    .catch(() => {
                        hidePaymentLoading();
                        window.location.href = fallbackRedirect;
                    });
                },

                modal: {
                    ondismiss: () => {
                        payBtn.disabled = false;
                        payBtn.innerHTML = buttonText + ' <i class="fas fa-arrow-right"></i>';
                    }
                },

                prefill: {
                    name:  data.name  || '',
                    email: data.email || ''
                }
            });

            rzp.open();
        })
        .catch(error => {
            console.error('Payment error:', error);
            payBtn.disabled = false;
            payBtn.innerHTML = buttonText + ' <i class="fas fa-arrow-right"></i>';
            alert('Something went wrong. Please try again.');
        });
    });
})();
</script>
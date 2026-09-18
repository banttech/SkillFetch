// public/js/camera-proctoring.js

window.CameraProctor = (function () {

    let cameraStream = null;

    // ✅ Check camera before payment
    async function checkCameraBeforePayment() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            stream.getTracks().forEach(track => track.stop());
            return true;
        } catch (error) {
            console.log('Camera Error:', error);

            return new Promise((resolve) => {
                const allow = confirm(
                    "Camera issue detected.\n\nCamera is required for this test.\n\nIf camera is not available, your test may be rejected.\n\nDo you want to continue?"
                );
                resolve(allow);
            });
        }
    }

    // ✅ Start camera
    async function startCamera(videoElementId, testId) {
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });

            const video = document.getElementById(videoElementId);
            if (video) {
                video.srcObject = cameraStream;
            }

            log(testId, 'CAMERA_STARTED', 'Camera started successfully');

            monitorCamera(testId);

        } catch (error) {
            log(testId, 'CAMERA_FAILED', error.message);
            alert("Camera is required for this test.");
        }
    }

    // ✅ Monitor camera
    function monitorCamera(testId) {

        if (!cameraStream) return;

        const track = cameraStream.getVideoTracks()[0];

        // Camera stopped
        track.onended = function () {
            log(testId, 'CAMERA_STOPPED', 'User stopped camera');
        };

        // Tab switch
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                log(testId, 'TAB_SWITCH', 'User switched tab');
            }
        });

        // Permission revoke check
        setInterval(async () => {
            try {
                await navigator.mediaDevices.getUserMedia({ video: true });
            } catch (e) {
                log(testId, 'CAMERA_PERMISSION_REVOKED', e.message);
            }
        }, 10000);
    }

    // ✅ Log activity
    function log(testId, type, message) {

        fetch('/camera/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                test_id: testId,
                activity_type: type,
                message: message
            })
        });
    }

    return {
        checkCameraBeforePayment,
        startCamera
    };

})();
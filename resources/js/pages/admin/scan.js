import { CameraScanner } from '../../components/scan/CameraScanner';
import { FileUploader } from '../../components/scan/FileUploader';
import { Html5Qrcode } from "html5-qrcode";

export default function registerQrScanner(Alpine) {
    Alpine.data('qrScanner', () => ({
        scanner: null,
        uploader: null,
        isScanning: false, // UI State
        isLoading: false, // Loading State
        manualInput: '',
        showModal: false,
        isValid: false,
        statusMessage: '',
        scanData: {},
        recentScans: [],
        hasCameraPermission: false,
        cameraError: null,

        async init() {
            console.log("QrScanner Component Initializing...");

            if (!window.isSecureContext) {
                console.warn("Insecure Context Detected");
                this.cameraError = "Koneksi tidak aman (HTTP). Kamera dinonaktifkan. Gunakan HTTPS atau localhost.";
            }

            try {
                this.scanner = new CameraScanner('reader',
                    this.onScanSuccess.bind(this),
                    this.onScanFailure.bind(this)
                );

                this.uploader = new FileUploader(null);

                this.$nextTick(async () => {
                    if (!this.cameraError) {
                        await this.startScanner();
                    }
                });
            } catch (e) {
                console.error("Component Init Error:", e);
                this.cameraError = "Gagal inisialisasi komponen: " + e.message;
            }
        },

        async startScanner() {
            console.log("Starting Scanner...");
            this.isLoading = true;
            this.cameraError = null;

            try {
                this.isScanning = true;
                await this.scanner.start();
                this.hasCameraPermission = true;

                if (this.scanner.html5QrCode) {
                    this.uploader.html5QrCode = this.scanner.html5QrCode;
                }
            } catch (err) {
                console.error("Scanner Start Error:", err);
                this.isScanning = false;
                this.hasCameraPermission = false;

                if (err.name === 'NotAllowedError') {
                    this.cameraError = "Izin kamera ditolak. Silakan izinkan akses kamera di browser.";
                } else if (err.toString().includes('secure context')) {
                    this.cameraError = err.message;
                } else {
                    this.cameraError = "Gagal memulai kamera: " + err.message;
                }
            } finally {
                this.isLoading = false;
            }
        },

        async stopScanner() {
            if (this.scanner) {
                await this.scanner.stop();
                this.isScanning = false;
            }
        },

        async toggleScanner() {
            if (this.isScanning) {
                await this.stopScanner();
            } else {
                await this.startScanner();
            }
        },

        onScanSuccess(decodedText, decodedResult) {
            if (this.showModal) return;
            console.log("Scan Success:", decodedText);
            this.scanner.pause();
            this.isScanning = false;
            this.validateQr(decodedText);
        },

        onScanFailure(errorMessage) {
            // console.warn("Scan Failure:", errorMessage);
        },

        async handleFileUpload(event) {
            console.log("File Upload Initiated");
            const file = event.target.files[0];
            if (!file) return;

            const label = document.querySelector('label[for="qr-input-file"]');
            if (label) {
                if (!label.dataset.originalText) label.dataset.originalText = label.innerHTML;
                label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            }

            try {
                // FORCE STOP: Try to stop 2 times to be sure
                if (this.scanner) {
                    try {
                        const state = this.scanner.getState();
                        // 2=Scanning, 3=Paused. 
                        if (state === 2 || state === 3) {
                            await this.scanner.stop();
                            this.isScanning = false;
                        }
                    } catch (stopErr) {
                        console.warn("Stop Warning:", stopErr);
                    }
                }

                // Wait a bit for the camera to release
                await new Promise(r => setTimeout(r, 300));

                if (!this.uploader.html5QrCode) {
                    // Force new instance to be safe
                    this.uploader.html5QrCode = new Html5Qrcode("reader");
                }

                // Attempt Scan with Retry Logic
                let result;
                try {
                    result = await this.uploader.scanFile(file);
                } catch (scanErr) {
                    // If conflicting, it might be "Cannot start file scan - ongoing camera scan"
                    if (scanErr.toString().toLowerCase().includes('ongoing')) {
                        console.log("Conflict detected, forcing stop and retrying...");

                        // Try to force stop the uploader's instance too if it's different
                        if (this.uploader.html5QrCode) {
                            await this.uploader.html5QrCode.stop().catch(e => { });
                            await this.uploader.html5QrCode.clear().catch(e => { });
                        }

                        await new Promise(r => setTimeout(r, 500));
                        // Re-init for upload
                        this.uploader.html5QrCode = new Html5Qrcode("reader");
                        result = await this.uploader.scanFile(file);
                    } else {
                        throw scanErr;
                    }
                }

                let validText = result;
                if (typeof result === 'object' && result !== null) {
                    validText = result.decodedText || JSON.stringify(result);
                }

                console.log("File Scan Result:", validText);
                this.validateQr(validText);

            } catch (err) {
                console.error("File Scan Failed:", err);
                alert("Gagal membaca QR Code.\nPastikan gambar jelas dan memiliki QR Code yang valid.\nDetail: " + (err.message || err));

                // Only restart if not showing success modal
                if (!this.showModal && !this.cameraError) this.startScanner();
            } finally {
                if (label && label.dataset.originalText) {
                    label.innerHTML = label.dataset.originalText;
                }
                event.target.value = '';

                // Auto restart check
                if (!this.showModal && !this.cameraError) {
                    setTimeout(() => {
                        if (!this.showModal) this.startScanner();
                    }, 500);
                }
            }
        },

        async validateQr(qrData) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const response = await fetch('/admin/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ qr_data: qrData })
                });

                const result = await response.json();

                if (response.ok) {
                    this.handleResult(true, result.message, result.data);
                } else {
                    const errorData = result.data || {};
                    // Backward compat
                    if (!errorData.order_number && result.detail?.order_number) {
                        errorData.order_number = result.detail.order_number;
                    }
                    this.handleResult(false, result.message, errorData);
                }

            } catch (error) {
                console.error("Validation Error:", error);
                this.handleResult(false, "System Error: " + error.message, { order_number: qrData });
            }
        },

        handleManualInput() {
            const input = this.manualInput.trim();
            if (!input) return;

            let dataToSend = input;
            if (!input.startsWith('{')) {
                dataToSend = JSON.stringify({ order_number: input });
            }
            this.validateQr(dataToSend);
        },

        handleResult(valid, message, data) {
            this.isValid = valid;
            this.statusMessage = message;
            this.scanData = data || {};
            this.showModal = true;
            this.manualInput = '';

            const audio = document.getElementById(valid ? 'scan-success' : 'scan-error');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(e => console.log('Audio play failed', e));
            }

            this.recentScans.unshift({
                valid: valid,
                ticketName: data.ticket_name || 'N/A',
                orderNumber: data.order_number || 'Unknown',
                time: new Date().toLocaleTimeString(),
                timestamp: Date.now()
            });
            if (this.recentScans.length > 5) this.recentScans.pop();
        },

        closeModal() {
            this.showModal = false;
            // Always try to start/resume
            if (!this.cameraError) {
                this.startScanner();
            }
        },

        resumeScanner() {
            if (this.cameraError) return;

            // Smart resume: 
            if (this.scanner && this.scanner.html5QrCode) {
                const state = this.scanner.getState();
                console.log("Resuming Scanner... Current State:", state);

                if (state === 3) { // PAUSED
                    this.scanner.resume();
                    this.isScanning = true;
                } else if (state === 1 || state === 0) { // NOT_STARTED or UNKNOWN
                    this.startScanner();
                }
            } else {
                this.startScanner();
            }
        }
    }));
}

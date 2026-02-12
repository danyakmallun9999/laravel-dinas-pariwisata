import { Html5Qrcode, Html5QrcodeSupportedFormats } from "html5-qrcode";

export class CameraScanner {
    constructor(elementId, onScanSuccess, onScanFailure) {
        this.elementId = elementId;
        this.onScanSuccess = onScanSuccess;
        this.onScanFailure = onScanFailure;
        this.html5QrCode = null;
        this.config = {
            fps: 15
        };
    }

    async start() {
        if (!this.isSecureContext()) {
            console.error("Camera access requires a secure context (HTTPS or localhost).");
            throw new Error("Kamera memerlukan koneksi aman (HTTPS).");
        }

        try {
            if (!this.html5QrCode) {
                this.html5QrCode = new Html5Qrcode(this.elementId);
            }

            // Check if already running to avoid error
            if (this.html5QrCode.isScanning) {
                console.log("Scanner is already running.");
                return;
            }

            await this.html5QrCode.start(
                { facingMode: "environment" },
                this.config,
                (decodedText, decodedResult) => {
                    this.onScanSuccess(decodedText, decodedResult);
                },
                (errorMessage) => {
                    if (this.onScanFailure) {
                        this.onScanFailure(errorMessage);
                    }
                }
            );
        } catch (err) {
            console.error("Failed to start camera:", err);
            throw err;
        }
    }

    async stop() {
        if (this.html5QrCode && (this.html5QrCode.isScanning || this.html5QrCode.getState() === 2 || this.html5QrCode.getState() === 3)) {
            try {
                // If paused (3), we must resume before stopping in some versions, or just stop. 
                // Stop usually works from any state except NOT_STARTED.
                await this.html5QrCode.stop();
            } catch (err) {
                console.error("Failed to stop camera:", err);
            }
        }
    }

    async pause() {
        if (this.html5QrCode && this.html5QrCode.isScanning) {
            this.html5QrCode.pause();
        }
    }

    async resume() {
        if (this.html5QrCode) {
            // html5-qrcode doesn't have a direct 'resume' state check exposed simply, 
            // but calling resume() when not paused throws an error in some versions.
            // We can use getState() if available
            try {
                // State 3 is PAUSED
                if (this.html5QrCode.getState() === 3) {
                    this.html5QrCode.resume();
                } else if (this.html5QrCode.getState() === 1 || this.html5QrCode.getState() === 0) {
                    // 1 is NOT_STARTED, 0 is UNKNOWN
                    await this.start();
                }
            } catch (e) {
                // Fallback if getState is not available or other error
                try {
                    this.html5QrCode.resume();
                } catch (resumeErr) {
                    // If resume fails, try start
                    await this.start();
                }
            }
        }
    }

    isSecureContext() {
        return window.isSecureContext;
    }

    getState() {
        return this.html5QrCode ? this.html5QrCode.getState() : 0;
    }
}

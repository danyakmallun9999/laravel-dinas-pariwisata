import { Html5Qrcode } from "html5-qrcode";

export class FileUploader {
    constructor(scannerInstance) {
        // We can reuse the Html5Qrcode instance for file scanning if we want, 
        // or create a temp one. Html5Qrcode supports scanFileV2 on the same instance.
        // However, scanFileV2 is a static method on Html5Qrcode in some docs, but instance method in others.
        // Let's us the instance method for consistency if we have one, or a new instance.
        // Better to use the instance to avoid conflicts on the element ID if that's how it's bound.
        this.html5QrCode = scannerInstance;
    }

    async scanFile(file) {
        if (!file) {
            throw new Error("No file selected.");
        }

        try {
            // If we don't have an instance passing in (e.g. camera not init), 
            // we might need a dummy element or handle it differently.
            // Assuming the camera scanner initializes the instance on the "reader" element.
            // If not, we might need new Html5Qrcode("reader") here.

            if (!this.html5QrCode) {
                throw new Error("Scanner instance not initialized.");
            }

            const result = await this.html5QrCode.scanFileV2(file, false);
            return result;
        } catch (err) {
            console.error("Error scanning file:", err);
            throw err;
        }
    }
}

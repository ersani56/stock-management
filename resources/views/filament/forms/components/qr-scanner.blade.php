<div x-data="qrScanner()">
    <div class="mb-4">
        <button type="button" @click="startScanner" class="bg-blue-500 text-white px-4 py-2 rounded">
            Buka Scanner QR
        </button>
        <button type="button" @click="stopScanner" class="bg-red-500 text-white px-4 py-2 rounded ml-2">
            Tutup Scanner
        </button>
    </div>

    <div id="qr-reader" style="width: 100%; display: none;"></div>

    <input type="hidden" id="scanned_qr" x-model="scannedCode" @change="updateForm">
</div>

<script>
    function qrScanner() {
        return {
            scannedCode: '',
            html5QrCode: null,

            startScanner() {
                const qrReader = document.getElementById('qr-reader');
                qrReader.style.display = 'block';

                this.html5QrCode = new Html5Qrcode("qr-reader");
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    this.scannedCode = decodedText;
                    this.stopScanner();
                    this.updateForm();
                };

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    qrCodeSuccessCallback
                ).catch(err => {
                    console.error(err);
                });
            },

            stopScanner() {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        const qrReader = document.getElementById('qr-reader');
                        qrReader.style.display = 'none';
                        this.html5QrCode = null;
                    }).catch(err => {
                        console.error(err);
                    });
                }
            },

            updateForm() {
                if (this.scannedCode) {
                    // Cari barang berdasarkan kode_barang
                    fetch(`/api/barang-by-kode/${this.scannedCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                // Update form dengan data barang
                                const form = document.querySelector('form');
                                const event = new Event('change');

                                // Set nilai barang_id
                                const barangIdField = form.querySelector('[name="barang_id"]');
                                if (barangIdField) {
                                    barangIdField.value = data.id;
                                    barangIdField.dispatchEvent(event);
                                }
                            }
                        });
                }
            }
        };
    }
</script>

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
@endpush

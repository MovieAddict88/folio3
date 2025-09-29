<h1 class="mb-4">Scan Student QR Code</h1>

<div class="card">
    <div class="card-body text-center">
        <div id="qr-reader" style="width: 100%; max-width: 500px; margin: auto;"></div>
        <div id="scan-result" class="mt-3"></div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Handle the scanned code.
        console.log(`Code matched = ${decodedText}`, decodedResult);

        // Stop scanning
        html5QrcodeScanner.clear();

        // Send the scanned ID to the server
        fetch('index.php?page=record_attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ qr_code_id: decodedText }),
        })
        .then(response => response.json())
        .then(data => {
            let resultDiv = document.getElementById('scan-result');
            if (data.success) {
                resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            let resultDiv = document.getElementById('scan-result');
            resultDiv.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        });
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
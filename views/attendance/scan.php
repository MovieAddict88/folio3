<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h3 class="text-center">Scan QR Code for Attendance</h3>
                    </div>
                    <div class="card-body text-center">
                        <video id="preview" class="w-100"></video>
                        <div id="message" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function (content) {
            document.getElementById('message').innerText = 'Scanning...';

            fetch('/attendance/mark', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'student_id=' + encodeURIComponent(content)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('message').className = 'alert alert-success';
                    document.getElementById('message').innerText = data.message;
                } else {
                    document.getElementById('message').className = 'alert alert-danger';
                    document.getElementById('message').innerText = data.message;
                }
                // Optional: Stop the scanner after a successful scan
                // scanner.stop();
            })
            .catch(error => {
                document.getElementById('message').className = 'alert alert-danger';
                document.getElementById('message').innerText = 'An error occurred.';
                console.error('Error:', error);
            });
        });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
                document.getElementById('message').innerText = 'No cameras found.';
            }
        }).catch(function (e) {
            console.error(e);
            document.getElementById('message').innerText = 'Error accessing camera.';
        });
    </script>
</body>
</html>
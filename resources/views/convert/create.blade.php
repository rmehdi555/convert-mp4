<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload MP4 File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Upload MP4 File for Conversion</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('convert.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="mp4_file" class="form-label">Select MP4 File</label>
                                <input type="file" class="form-control @error('mp4_file') is-invalid @enderror" 
                                       id="mp4_file" name="mp4_file" accept=".mp4" required>
                                @error('mp4_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum file size: 10GB</div>
                            </div>

                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <h6>Conversion Process:</h6>
                                    <ul class="mb-0">
                                        <li>Your MP4 file will be converted to HLS (M3U8) format</li>
                                        <li>Conversion runs in the background using queue</li>
                                        <li>You'll be redirected to the conversion list to monitor progress</li>
                                        <li>Once completed, you can download the M3U8 file</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('convert.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Upload & Convert</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

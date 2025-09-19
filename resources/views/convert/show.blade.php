@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversion Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Conversion Details</h1>
                    <a href="{{ route('convert.index') }}" class="btn btn-secondary">Back to List</a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Conversion #{{ $convertMp4->id }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>File Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>MP4 File:</strong></td>
                                        <td>{{ basename($convertMp4->path_mp4) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge {{ $convertMp4->status?->badgeClass() ?? 'bg-secondary' }}">
                                                {{ $convertMp4->status?->label() ?? 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $convertMp4->created_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ $convertMp4->updated_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                @if($convertMp4->path_m3u8)
                                    <h6>Output File</h6>
                                    <p><strong>M3U8 File:</strong> {{ basename($convertMp4->path_m3u8) }}</p>
                                    <a href="{{ route('convert.download', $convertMp4) }}" class="btn btn-success mb-3">Download M3U8</a>
                                    
                                    <!-- Video Player -->
                                    <div class="mt-3">
                                        <h6>Video Preview</h6>
                                        
                                        <!-- Debug Info -->
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <strong>M3U8 Path:</strong> {{ $convertMp4->path_m3u8 }}<br>
                                                <strong>M3U8 URL:</strong> {{ Storage::url($convertMp4->path_m3u8) }}<br>
                                                <strong>File Exists:</strong> {{ Storage::disk('public')->exists($convertMp4->path_m3u8) ? 'Yes' : 'No' }}<br>
                                                <strong>MP4 URL:</strong> {{ Storage::url($convertMp4->path_mp4) }}
                                            </small>
                                        </div>
                                        
                                        @if(Storage::disk('public')->exists($convertMp4->path_m3u8))
                                            <video controls width="100%" height="300" class="border rounded">
                                                <source src="{{ Storage::url($convertMp4->path_m3u8) }}" type="application/x-mpegURL">
                                                <source src="{{ Storage::url($convertMp4->path_mp4) }}" type="video/mp4">
                                                Your browser does not support the video tag or HLS streaming.
                                            </video>
                                        @else
                                            <div class="alert alert-warning">
                                                <strong>M3U8 file not found!</strong><br>
                                                The conversion may have failed or the file is not accessible.
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Note:</strong> If the video doesn't play, try downloading the M3U8 file and use a compatible player like VLC.
                                            </small>
                                        </div>
                                    </div>
                                @endif

                                @if($convertMp4->error_message)
                                    <h6 class="text-danger">Error Details</h6>
                                    <div class="alert alert-danger">
                                        <pre>{{ $convertMp4->error_message }}</pre>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($convertMp4->status?->value === 'processing')
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <h6>Processing...</h6>
                                    <p class="mb-0">Your file is being converted. This may take a few minutes depending on file size.</p>
                                    <div class="mt-2">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Converting MP4 to M3U8...</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @if($convertMp4->status?->value === 'processing')
    <script>
        // Auto-refresh every 5 seconds while processing
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
    @endif
</body>
</html>

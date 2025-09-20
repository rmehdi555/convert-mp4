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
                                                <strong>File Exists:</strong> {{ Storage::disk('public')->exists($convertMp4->path_m3u8) ? 'Yes' : 'No' }}
                                            </small>
                                        </div>
                                        
                                        @if(Storage::disk('public')->exists($convertMp4->path_m3u8))
                                            <video id="videoPlayer" controls width="100%" height="300" class="border rounded">
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
                                                <strong>Note:</strong> This video is streaming in HLS (M3U8) format. If it doesn't play, your browser may not support HLS streaming.
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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    
    <script>
        @if(Storage::disk('public')->exists($convertMp4->path_m3u8))
        // Initialize HLS.js for M3U8 playback
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('videoPlayer');
            const m3u8Url = '{{ Storage::url($convertMp4->path_m3u8) }}';
            
            if (Hls.isSupported()) {
                // HLS.js is supported
                const hls = new Hls();
                hls.loadSource(m3u8Url);
                hls.attachMedia(video);
                
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    console.log('M3U8 manifest loaded successfully');
                });
                
                hls.on(Hls.Events.ERROR, function(event, data) {
                    console.error('HLS Error:', data);
                    if (data.fatal) {
                        switch(data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.log('Fatal network error encountered, try to recover');
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.log('Fatal media error encountered, try to recover');
                                hls.recoverMediaError();
                                break;
                            default:
                                console.log('Fatal error, cannot recover');
                                hls.destroy();
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                // Native HLS support (Safari)
                video.src = m3u8Url;
                video.addEventListener('loadedmetadata', function() {
                    console.log('M3U8 loaded with native support');
                });
            } else {
                // Fallback: show error message
                video.style.display = 'none';
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.innerHTML = '<strong>Error:</strong> Your browser does not support HLS streaming. Please use a modern browser or download the M3U8 file.';
                video.parentNode.insertBefore(errorDiv, video);
            }
        });
        @endif
        
        @if($convertMp4->status?->value === 'processing')
        // Auto-refresh every 5 seconds while processing
        setTimeout(function() {
            location.reload();
        }, 5000);
        @endif
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP4 to M3U8 Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">MP4 to M3U8 Converter</h1>
                
                <div class="mb-4">
                    <a href="{{ route('convert.create') }}" class="btn btn-primary">Upload New MP4 File</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5>Conversion History</h5>
                    </div>
                    <div class="card-body">
                        @if($conversions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>MP4 File</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conversions as $conversion)
                                            <tr>
                                                <td>{{ $conversion->id }}</td>
                                                <td>{{ basename($conversion->path_mp4) }}</td>
                                                <td>
                                                    <span class="badge {{ $conversion->status?->badgeClass() ?? 'bg-secondary' }}">
                                                        {{ $conversion->status?->label() ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>{{ $conversion->created_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('convert.show', $conversion) }}" class="btn btn-sm btn-info">View</a>
                                                    @if($conversion->status === \App\Enums\ConvertStatus::COMPLETED && $conversion->path_m3u8)
                                                        <a href="{{ route('convert.download', $conversion) }}" class="btn btn-sm btn-success">Download M3U8</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{ $conversions->links() }}
                        @else
                            <p class="text-muted">No conversions found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

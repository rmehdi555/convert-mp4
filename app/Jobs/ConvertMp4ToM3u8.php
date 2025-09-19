<?php

namespace App\Jobs;

use App\Enums\ConvertStatus;
use App\Models\ConvertMp4;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConvertMp4ToM3u8 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $convertMp4;

    public function __construct(ConvertMp4 $convertMp4)
    {
        $this->convertMp4 = $convertMp4;
    }

    public function handle()
    {
        try {
            // Update status to processing
            $this->convertMp4->update(['status' => ConvertStatus::PROCESSING]);

            $mp4Path = $this->convertMp4->path_mp4;
            $outputDir = 'converted/' . $this->convertMp4->id;
            $m3u8Path = $outputDir . '/playlist.m3u8';

            // Create output directory
            Storage::disk('public')->makeDirectory($outputDir);

            // FFmpeg command to convert MP4 to HLS (M3U8)
            $ffmpegCommand = sprintf(
                'ffmpeg -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_list_size 0 -hls_segment_filename %s/segment_%%03d.ts -f hls %s 2>&1',
                escapeshellarg(storage_path('app/public/' . $mp4Path)),
                escapeshellarg(storage_path('app/public/' . $outputDir)),
                escapeshellarg(storage_path('app/public/' . $m3u8Path))
            );

            // Execute FFmpeg command
            $output = [];
            $returnCode = 0;
            exec($ffmpegCommand, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $output));
            }

            // Update record with success
            $this->convertMp4->update([
                'path_m3u8' => $m3u8Path,
                'status' => ConvertStatus::COMPLETED,
                'error_message' => null,
            ]);

            Log::info('MP4 to M3U8 conversion completed', [
                'convert_id' => $this->convertMp4->id,
                'mp4_path' => $mp4Path,
                'm3u8_path' => $m3u8Path,
            ]);

        } catch (\Exception $e) {
            // Update record with error
            $this->convertMp4->update([
                'status' => ConvertStatus::FAILED,
                'error_message' => $e->getMessage(),
            ]);

            Log::error('MP4 to M3U8 conversion failed', [
                'convert_id' => $this->convertMp4->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->convertMp4->update([
            'status' => ConvertStatus::FAILED,
            'error_message' => $exception->getMessage(),
        ]);
    }
}

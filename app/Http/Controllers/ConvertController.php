<?php

namespace App\Http\Controllers;

use App\Enums\ConvertStatus;
use App\Jobs\ConvertMp4ToM3u8;
use App\Models\ConvertMp4;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertController extends Controller
{
    public function index()
    {
        $conversions = ConvertMp4::orderBy('created_at', 'desc')->paginate(10);
        return view('convert.index', compact('conversions'));
    }

    public function create()
    {
        return view('convert.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'mp4_file' => 'required|file|mimes:mp4|max:10240000', // Max 10GB
        ]);

        $file = $request->file('mp4_file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        // Create conversion record
        $convertMp4 = ConvertMp4::create([
            'path_mp4' => $path,
            'status' => ConvertStatus::PENDING,
        ]);

        // Dispatch conversion job
        ConvertMp4ToM3u8::dispatch($convertMp4);

        return redirect()->route('convert.index')
            ->with('success', 'File uploaded successfully. Conversion started in background.');
    }

    public function show(ConvertMp4 $convertMp4)
    {
        return view('convert.show', compact('convertMp4'));
    }

    public function download(ConvertMp4 $convertMp4)
    {
        if ($convertMp4->status !== ConvertStatus::COMPLETED || !$convertMp4->path_m3u8) {
            return redirect()->back()->with('error', 'Conversion not completed or file not available.');
        }

        $filePath = storage_path('app/public/' . $convertMp4->path_m3u8);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath);
    }
}

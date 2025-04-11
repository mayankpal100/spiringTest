<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class GenerateQrCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($this->user->address);
        $image = Http::get($url)->body();

        $filename = 'qr_codes/' . Str::uuid() . '.png';
        Storage::put('public/' . $filename, $image);
        echo "generating";
        $this->user->update(['qr_code_path' => 'storage/' . $filename]);
    }
}

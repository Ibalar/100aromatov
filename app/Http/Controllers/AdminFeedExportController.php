<?php

namespace App\Http\Controllers;

use App\Models\FeedExportProfile;
use App\Services\FeedExportService;
use Illuminate\Http\Response;

class AdminFeedExportController extends Controller
{
    public function download(FeedExportProfile $profile, FeedExportService $service): Response
    {
        $xml = $service->generate($profile);
        $ext = $profile->platform === 'google' ? 'xml' : 'yml';
        $filename = 'feed-' . $profile->platform . '-' . $profile->id . '.' . $ext;

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

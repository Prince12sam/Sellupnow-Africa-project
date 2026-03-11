<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FirebaseController extends Controller
{
    public function index()
    {
        $this->migrateLegacyPublicCredentialFile();

        $credentialPath = $this->credentialPath();
        $hasConfig = File::exists($credentialPath);
        $projectId = null;

        if ($hasConfig) {
            $payload = json_decode(File::get($credentialPath), true);
            $projectId = is_array($payload) ? ($payload['project_id'] ?? null) : null;
        }

        return view('admin.firebase.index', compact('hasConfig', 'projectId'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $file = $request->file('file');

        $json = json_decode($file->get(), true);

        if (array_key_exists('type', $json) && array_key_exists('project_id', $json) && array_key_exists('private_key', $json) && array_key_exists('client_email', $json) && array_key_exists('client_id', $json)) {
            $destination = $this->credentialPath();
            File::ensureDirectoryExists(dirname($destination));

            if (File::exists($destination)) {
                File::delete($destination);
            }

            $file->move(dirname($destination), basename($destination));

            $legacyPath = $this->legacyCredentialPath();
            if (File::exists($legacyPath)) {
                File::delete($legacyPath);
            }

            return back()->withSuccess('Firebase config updated successfully');
        }

        return back()->withError('Sorry! the selected file is not a valid firebase config file');
    }

    private function credentialPath(): string
    {
        return (string) config('firebase.projects.app.credentials.file', storage_path('app/firebase_credentials.json'));
    }

    private function legacyCredentialPath(): string
    {
        return storage_path('app/public/firebase_credentials.json');
    }

    private function migrateLegacyPublicCredentialFile(): void
    {
        $legacyPath = $this->legacyCredentialPath();
        $newPath = $this->credentialPath();

        if (! File::exists($legacyPath) || File::exists($newPath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($newPath));
        File::move($legacyPath, $newPath);
    }
}

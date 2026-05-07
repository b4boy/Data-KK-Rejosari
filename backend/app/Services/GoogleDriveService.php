<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected $client;
    protected $drive;
    protected $folderId;
    protected bool $ready = false;

    public function __construct()
    {
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID', 'root');

        $clientId     = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');

        if ($clientId && $clientSecret && $refreshToken) {
            try {
                $this->client = new Client();
                $this->client->setApplicationName('e-KK Rejosari');
                $this->client->setClientId($clientId);
                $this->client->setClientSecret($clientSecret);
                $this->client->addScope(Drive::DRIVE_FILE);
                $this->client->setAccessType('offline');
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

                $this->drive = new Drive($this->client);
                $this->ready = true;
            } catch (\Exception $e) {
                $this->ready = false;
            }
        }
    }

    public function uploadFoto($file, string $fileName): array
    {
        if ($this->ready) {
            return $this->uploadToDrive($file, $fileName);
        }
        return $this->uploadToLocal($file);
    }

    private function uploadToDrive($file, string $fileName): array
    {
        try {
            $metadata = new DriveFile([
                'name'    => $fileName,
                'parents' => [$this->folderId],
            ]);

            $uploaded = $this->drive->files->create($metadata, [
                'data'       => file_get_contents($file->getRealPath()),
                'mimeType'   => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields'     => 'id',
            ]);

            // Set public permission
            $permission = new Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            $this->drive->permissions->create($uploaded->id, $permission);

            $fileId = $uploaded->id;

            // Thumbnail URL - format paling stabil untuk ditampilkan di browser
            $url = "https://drive.google.com/thumbnail?id={$fileId}&sz=w800";

            return [
                'success'  => true,
                'url'      => $url,
                'drive_id' => $fileId,
            ];
        } catch (\Exception $e) {
            return $this->uploadToLocal($file);
        }
    }

    private function uploadToLocal($file): array
    {
        $path    = $file->store('kk_fotos', 'public');
        $fullUrl = url(Storage::url($path));

        return [
            'success'  => true,
            'url'      => $fullUrl,
            'drive_id' => null,
        ];
    }

    public function deleteFoto(?string $driveId): array
    {
        if (!$driveId || !$this->ready) {
            return ['success' => true];
        }
        try {
            $this->drive->files->delete($driveId);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function isReady(): bool
    {
        return $this->ready;
    }
}
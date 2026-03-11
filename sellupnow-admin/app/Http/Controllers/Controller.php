<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function json(?string $message = null, $data = [], $statusCode = 200, array $headers = [])
    {
        $content = [
            'status' => $statusCode >= 200 && $statusCode < 300,
        ];
        if ($message) {
            $content['message'] = $message;
        }

        if (! empty($data)) {
            $content['data'] = $data;
        }

        return response()->json($content, $statusCode, $headers, JSON_PRESERVE_ZERO_FRACTION);
    }

    protected function setEnv($key, $value)
    {
        return $this->setEnvInFile(app()->environmentFilePath(), $key, $value);
    }

    /**
     * Write (or overwrite) a single KEY=value pair in any dotenv file.
     * Values with spaces, quotes, backslashes or hashes are auto-quoted.
     */
    protected function setEnvInFile(string $envFilePath, string $key, $value): array
    {
        try {
            $str = file_get_contents($envFilePath);

            // Wrap value in double-quotes if it contains spaces, quotes, hashes
            // or backslashes so dotenv always parses the file correctly.
            $value = (string) $value;
            if ($value === '' || preg_match('/[\s"\'\\\\#]/', $value)) {
                $inner   = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
                $escaped = '"' . $inner . '"';
            } else {
                $escaped = $value;
            }

            // Check if the key exists in the .env file
            if (strpos($str, "{$key}=") === false) {
                $str .= "{$key}={$escaped}\n";
            } else {
                // Anchor to start of line to avoid partial-key matches
                $str = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $str);
            }

            $str = rtrim($str) . "\n";
            file_put_contents($envFilePath, $str);

            return ['type' => 'success', 'message' => __('Updated Successfully')];
        } catch (Exception $e) {
            return ['type' => 'error', 'message' => $e->getMessage()];
        }
    }
}

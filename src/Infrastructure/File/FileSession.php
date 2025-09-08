<?php

namespace Infrastructure\File;

use Infrastructure\ISession;

class FileSession implements ISession
{
    public function start(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            return session_start();
        }
        return true;
    }

    public function set(string $key, $value): void
    {
        error_log("SET VALUE IN SESSION");
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function destroy($id = null): void
    {
        $this->start();
        session_unset();
        session_destroy();
    }

    public function regenerateId(bool $deleteOldSession = true): bool
    {
        $this->start();
        return session_regenerate_id($deleteOldSession);
    }

    public function getUserSessions(int $userId): array
    {
        error_log("GET USER SESSION ID");
        $sessionPath = ini_get('session.save_path') ?: sys_get_temp_dir();

        $sessions = [];
        $files = glob($sessionPath . '/sess_*');

        foreach ($files as $file) {
            if (is_file($file) && is_readable($file)) {
                $content = @file_get_contents($file);

                if ($content === false || empty($content)) {
                    error_log('Cannot read or empty session file: ' . $file);
                    continue;
                }

                // Десериализация данных сессии
                $data = $this->unserializeSessionData($content);
                

                if ($data === false) {
                    error_log('Cannot unserialize session data from file: ' . $file);
                    continue;
                }

                if (isset($data['userData']['id']) && $data['userData']['id'] === $userId) {
                    error_log('Found user session: ' . print_r($data, true));
                    $sessions[] = [
                        'id' => basename($file, 'sess_'),
                        'data' => $data,
                        'timestamp' => filemtime($file),
                    ];
                }
            } else {
                error_log('Cannot read session file: ' . $file);
            }
        }
        return $sessions;
    }

    private function unserializeSessionData(string $sessionData): array|false
    {
        $returnData = [];
        $offset = 0;
        $len = strlen($sessionData);

        while ($offset < $len) {
            // ищем разделитель
            $pos = strpos($sessionData, '|', $offset);
            if ($pos === false) {
                break;
            }

            $varname = substr($sessionData, $offset, $pos - $offset);
            $offset = $pos + 1;

            $value = null;
            $success = false;

            // подбираем правильную длину сериализованного блока
            for ($i = 1; $offset + $i <= $len; $i++) {
                $chunk = substr($sessionData, $offset, $i);
                $val = @unserialize($chunk);
                if ($val !== false || $chunk === 'b:0;') {
                    $value = $val;
                    $offset += $i;
                    $success = true;
                    break;
                }
            }

            if (!$success) {
                return false;
            }

            $returnData[$varname] = $value;
        }

        return $returnData;
    }
}

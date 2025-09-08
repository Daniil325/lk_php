<?php

namespace Infrastructure\Database;

use Infrastructure\ISession;

class DatabaseSession implements ISession
{
    private $db_session;

    public function __construct($db_session)
    {
        $this->db_session = $db_session->getConnection();
        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );
        register_shutdown_function('session_write_close');
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        error_log("Reading session ID: $id");
        $stmt = $this->db_session->prepare('SELECT user_id, user.email FROM sessions JOIN user ON sessions.user_id = user.id WHERE sessions.id = ?');
        if (!$stmt) {
            error_log('Prepare failed in read: ' . $this->db_session->error);
            return '';
        }
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($userId, $email);
        $fetched = $stmt->fetch();
        $stmt->close();

        if ($fetched && $email !== null) {
            $userData = [
                'id' => (int)$userId,
                'email' => $email,
            ];

            // Формат php serialize_handler: key|<serialized_value>
            $sessionData = 'userData|' . serialize($userData);

            error_log("Read OK. ID=$id, sessionData=$sessionData");
            return $sessionData;
        }

        error_log("Read session ID: $id, no data");
        return '';
    }


    public function write($id): bool
    {
        $userId = isset($_SESSION['userData']['id']) ? (int)$_SESSION['userData']['id'] : null;
        error_log("User ID: " . ($userId ?? 'null'));
        $timestamp = time();
        $dateIn =  date("Y-m-d H:i:s");
        $stmt = $this->db_session->prepare('
            INSERT INTO sessions (id, user_id, timestamp, dateIn)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE user_id = ?, timestamp = ?, dateIn = ?
        ');
        if (!$stmt) {
            error_log('Prepare failed in write: ' . $this->db_session->error);
            return false;
        }
        $stmt->bind_param('siisiss', $id, $userId, $timestamp, $dateIn, $userId, $timestamp, $dateIn);
        $result = $stmt->execute();
        if (!$result) {
            error_log('Execute failed in write: ' . $stmt->error);
        } else {
            error_log('Session written successfully: ID=' . $id);
        }
        $stmt->close();
        return $result;
    }

    public function destroy(?string $id = null): void
    {
        $id = $id ?? session_id();
        error_log("Destroying session ID: $id, Stack trace: " . debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        $stmt = $this->db_session->prepare('DELETE FROM sessions WHERE id = ?');
        if (!$stmt) {
            error_log('Prepare failed in destroy: ' . $this->db_session->error);
            return;
        }
        $stmt->bind_param('s', $id);
        $stmt->execute();
        if ($stmt->error) {
            error_log('Destroy failed: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function gc($maxLifetime): int|false
    {
        $expire = time() - $maxLifetime;
        error_log("Running GC, expire before: $expire, maxLifetime: $maxLifetime");
        $stmt = $this->db_session->prepare('DELETE FROM sessions WHERE timestamp < ?');
        if (!$stmt) {
            error_log('Prepare failed in gc: ' . $this->db_session->error);
            return false;
        }
        $stmt->bind_param('i', $expire);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        if ($stmt->error) {
            error_log('GC failed: ' . $stmt->error);
        }
        $stmt->close();
        error_log("GC removed $affectedRows rows");
        return $affectedRows;
    }

    public function start(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            error_log("Starting session");
            return session_start();
        }
        return true;
    }

    public function set(string $key, $value): void
    {
        $this->start();
        error_log("Setting session key: $key, Value: " . print_r($value, true));
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        $this->start();
        $value = $_SESSION[$key] ?? $default;
        error_log("Getting session key: $key, Value: " . print_r($value, true));
        return $value;
    }

    public function has(string $key): bool
    {
        $this->start();
        $has = isset($_SESSION[$key]);
        error_log("Checking session key: $key, Exists: " . ($has ? 'true' : 'false'));
        return $has;
    }

    public function remove(string $key): void
    {
        $this->start();
        error_log("Removing session key: $key");
        unset($_SESSION[$key]);
    }

    public function regenerateId(bool $deleteOldSession = true): bool
    {
        $this->start();
        error_log("Regenerating session ID, delete old: " . ($deleteOldSession ? 'true' : 'false'));
        if ($deleteOldSession) {
            $this->destroy(session_id());
        }
        return session_regenerate_id($deleteOldSession);
    }

    public function getUserSessions(int $userId): array
    {
        error_log("Fetching sessions for userId: $userId");
        $stmt = $this->db_session->prepare('SELECT sessions.id, user_id, user.email, timestamp, dateIn FROM sessions JOIN user ON sessions.user_id = user.id WHERE user_id = ?');
        if (!$stmt) {
            error_log('Prepare failed in getUserSessions: ' . $this->db_session->error);
            return [];
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = [
                'id' => $row['id'],
                'data' => [
                    'dateIn' => $row['dateIn'],
                    'userData' => [
                        'id' => $row['user_id'],
                        'email' => $row['email'],
                    ]
                ],
                'timestamp' => $row['timestamp'],

            ];
        }
        $stmt->close();
        error_log('Returning ' . count($sessions) . ' sessions for userId: ' . $userId);
        return $sessions;
    }
}

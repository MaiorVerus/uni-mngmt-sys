

<?php

class User
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(string $username, string $email, string $hash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, $email, $hash]);
        return (int) $this->pdo->lastInsertId();
    }
}

?>
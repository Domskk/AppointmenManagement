<?php
function execute(PDO $conn, string $call, array $params = [], string $fetch = 'all'): ?array {
    $stmt = $conn->prepare($call);
    $stmt->execute($params);

    $result = match ($fetch) {
        'all'  => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'one'  => $stmt->fetch(PDO::FETCH_ASSOC) ?: null,
        'none' => null,
        default => throw new InvalidArgumentException("Invalid fetch mode: $fetch"),
    };

    $stmt->closeCursor();
    return $result;
}

function request_body(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}
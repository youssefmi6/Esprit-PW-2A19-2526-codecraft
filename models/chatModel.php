<?php
/**
 * DTO: Chat (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class ChatModel
{
    private int $sender_id = 0;
    private int $receiver_id = 0;
    private string $message_text = '';
    private ?string $created_at = null;

    private int $id = 0;
    private string $nom = '';
    private string $prenom = '';
    private string $photo = '';
    private string $email = '';
    private ?string $last_message_at = null;

    public function __construct(array $data = [])
    {
        $this->sender_id = (int)($data['sender_id'] ?? 0);
        $this->receiver_id = (int)($data['receiver_id'] ?? 0);
        $this->message_text = (string)($data['message_text'] ?? '');
        $this->created_at = $data['created_at'] ?? null;

        $this->id = (int)($data['id'] ?? 0);
        $this->nom = (string)($data['nom'] ?? '');
        $this->prenom = (string)($data['prenom'] ?? '');
        $this->photo = (string)($data['photo'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->last_message_at = $data['last_message_at'] ?? null;
    }

    public function getSenderId(): int { return $this->sender_id; }
    public function setSenderId(int $v): void { $this->sender_id = $v; }
    public function getReceiverId(): int { return $this->receiver_id; }
    public function setReceiverId(int $v): void { $this->receiver_id = $v; }
    public function getMessageText(): string { return $this->message_text; }
    public function setMessageText(string $v): void { $this->message_text = $v; }
    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $v): void { $this->created_at = $v; }

    public function getId(): int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $v): void { $this->nom = $v; }
    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $v): void { $this->prenom = $v; }
    public function getPhoto(): string { return $this->photo; }
    public function setPhoto(string $v): void { $this->photo = $v; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $v): void { $this->email = $v; }
    public function getLastMessageAt(): ?string { return $this->last_message_at; }
    public function setLastMessageAt(?string $v): void { $this->last_message_at = $v; }
}


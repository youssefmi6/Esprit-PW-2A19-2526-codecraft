<?php
/**
 * DTO: Commentaire (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class CommentModel
{
    private int $id_comment = 0;
    private int $id = 0; // user id
    private int $id_res = 0; // resource id
    private string $message = '';
    private ?string $date = null;

    private string $nom = '';
    private string $prenom = '';
    private string $photo = '';
    private int $likes_count = 0;
    private int $dislikes_count = 0;
    private int $user_reaction = 0;

    public function __construct(array $data = [])
    {
        $this->id_comment = (int)($data['id_comment'] ?? 0);
        $this->id = (int)($data['id'] ?? 0);
        $this->id_res = (int)($data['id_res'] ?? 0);
        $this->message = (string)($data['message'] ?? '');
        $this->date = $data['date'] ?? null;

        $this->nom = (string)($data['nom'] ?? '');
        $this->prenom = (string)($data['prenom'] ?? '');
        $this->photo = (string)($data['photo'] ?? '');
        $this->likes_count = (int)($data['likes_count'] ?? 0);
        $this->dislikes_count = (int)($data['dislikes_count'] ?? 0);
        $this->user_reaction = (int)($data['user_reaction'] ?? 0);
    }

    public function getIdComment(): int { return $this->id_comment; }
    public function setIdComment(int $v): void { $this->id_comment = $v; }
    public function getUserId(): int { return $this->id; }
    public function setUserId(int $v): void { $this->id = $v; }
    public function getResourceId(): int { return $this->id_res; }
    public function setResourceId(int $v): void { $this->id_res = $v; }
    public function getMessage(): string { return $this->message; }
    public function setMessage(string $v): void { $this->message = $v; }
    public function getDate(): ?string { return $this->date; }
    public function setDate(?string $v): void { $this->date = $v; }
}


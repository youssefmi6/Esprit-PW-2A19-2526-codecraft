<?php
/**
 * DTO: Rating (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class RatingModel
{
    private int $id_res = 0;
    private int $id_user = 0;
    private int $rating = 0;

    public function __construct(array $data = [])
    {
        $this->id_res = (int)($data['id_res'] ?? 0);
        $this->id_user = (int)($data['id_user'] ?? 0);
        $this->rating = (int)($data['rating'] ?? 0);
    }

    public function getResourceId(): int { return $this->id_res; }
    public function setResourceId(int $v): void { $this->id_res = $v; }

    public function getUserId(): int { return $this->id_user; }
    public function setUserId(int $v): void { $this->id_user = $v; }

    public function getRating(): int { return $this->rating; }
    public function setRating(int $v): void { $this->rating = $v; }
}


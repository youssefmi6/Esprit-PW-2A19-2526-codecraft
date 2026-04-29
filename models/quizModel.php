<?php
/**
 * DTO: Quiz Attempt (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class QuizModel
{
    private int $id = 0;
    private int $resource_id = 0;
    private int $user_id = 0;
    private array $questions_json = [];
    private array $answers_json = [];
    private float $score = 0.0;
    private int $passed = 0;
    private ?string $certificate_code = null;
    private ?string $created_at = null;

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->resource_id = (int)($data['resource_id'] ?? 0);
        $this->user_id = (int)($data['user_id'] ?? 0);
        $this->questions_json = is_array($data['questions_json'] ?? null) ? $data['questions_json'] : (array)($data['questions'] ?? []);
        $this->answers_json = is_array($data['answers_json'] ?? null) ? $data['answers_json'] : (array)($data['answers'] ?? []);
        $this->score = (float)($data['score'] ?? 0);
        $this->passed = (int)($data['passed'] ?? 0);
        $this->certificate_code = $data['certificate_code'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getResourceId(): int { return $this->resource_id; }
    public function setResourceId(int $v): void { $this->resource_id = $v; }
    public function getUserId(): int { return $this->user_id; }
    public function setUserId(int $v): void { $this->user_id = $v; }
    public function getQuestions(): array { return $this->questions_json; }
    public function setQuestions(array $v): void { $this->questions_json = $v; }
    public function getAnswers(): array { return $this->answers_json; }
    public function setAnswers(array $v): void { $this->answers_json = $v; }
    public function getScore(): float { return $this->score; }
    public function setScore(float $v): void { $this->score = $v; }
    public function isPassed(): bool { return $this->passed === 1; }
    public function setPassed(int $v): void { $this->passed = $v; }
    public function getCertificateCode(): ?string { return $this->certificate_code; }
    public function setCertificateCode(?string $v): void { $this->certificate_code = $v; }
}


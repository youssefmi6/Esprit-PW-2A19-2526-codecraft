<?php
/**
 * DTO: User (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class UserModel
{
    private int $id = 0;
    private string $nom = '';
    private string $prenom = '';
    private string $universite = '';
    private string $filiere = '';
    private string $email = '';
    private string $mdp = '';
    private string $tel = '';
    private string $bio = '';
    private string $photo = '';
    private int $role = 1;
    private int $score = 0;

    private int $is_active = 1;
    private ?string $activation_token = null;
    private ?string $activation_token_expires_at = null;

    private int $face_enabled = 0;
    private ?string $face_descriptor = null;

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->nom = (string)($data['nom'] ?? '');
        $this->prenom = (string)($data['prenom'] ?? '');
        $this->universite = (string)($data['universite'] ?? '');
        $this->filiere = (string)($data['filiere'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->mdp = (string)($data['mdp'] ?? '');
        $this->tel = (string)($data['tel'] ?? '');
        $this->bio = (string)($data['bio'] ?? '');
        $this->photo = (string)($data['photo'] ?? '');
        $this->role = (int)($data['role'] ?? 1);
        $this->score = (int)($data['score'] ?? 0);

        $this->is_active = (int)($data['is_active'] ?? 1);
        $this->activation_token = $data['activation_token'] ?? null;
        $this->activation_token_expires_at = $data['activation_token_expires_at'] ?? null;

        $this->face_enabled = (int)($data['face_enabled'] ?? 0);
        $this->face_descriptor = $data['face_descriptor'] ?? null;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $v): void { $this->nom = $v; }
    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $v): void { $this->prenom = $v; }
    public function getUniversite(): string { return $this->universite; }
    public function setUniversite(string $v): void { $this->universite = $v; }
    public function getFiliere(): string { return $this->filiere; }
    public function setFiliere(string $v): void { $this->filiere = $v; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $v): void { $this->email = $v; }
    public function getMdp(): string { return $this->mdp; }
    public function setMdp(string $v): void { $this->mdp = $v; }
    public function getTel(): string { return $this->tel; }
    public function setTel(string $v): void { $this->tel = $v; }
    public function getBio(): string { return $this->bio; }
    public function setBio(string $v): void { $this->bio = $v; }
    public function getPhoto(): string { return $this->photo; }
    public function setPhoto(string $v): void { $this->photo = $v; }
    public function getRole(): int { return $this->role; }
    public function setRole(int $v): void { $this->role = $v; }
    public function getScore(): int { return $this->score; }
    public function setScore(int $v): void { $this->score = $v; }

    public function getIsActive(): int { return $this->is_active; }
    public function setIsActive(int $v): void { $this->is_active = $v; }
    public function getActivationToken(): ?string { return $this->activation_token; }
    public function setActivationToken(?string $v): void { $this->activation_token = $v; }
    public function getActivationTokenExpiresAt(): ?string { return $this->activation_token_expires_at; }
    public function setActivationTokenExpiresAt(?string $v): void { $this->activation_token_expires_at = $v; }

    public function getFaceEnabled(): int { return $this->face_enabled; }
    public function setFaceEnabled(int $v): void { $this->face_enabled = $v; }
    public function getFaceDescriptor(): ?string { return $this->face_descriptor; }
    public function setFaceDescriptor(?string $v): void { $this->face_descriptor = $v; }
}


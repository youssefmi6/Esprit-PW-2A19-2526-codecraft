<?php
/**
 * DTO: Ressource (aucun SQL ici).
 * Les requêtes PDO sont centralisées dans `controllers/sql_queries.php`.
 */
class ResourceModel
{
    private int $id_res = 0;
    private int $user_id = 0;
    private string $titre = '';
    private string $description = '';
    private string $matiere = '';
    private string $type = '';
    private string $niveau = '';
    private string $acces = '';
    private float $prix = 0.0;
    private string $fichier = '';
    private string $photo = '';
    private int $pages = 0;
    private int $downloads = 0;
    private ?string $date_creation = null;
    private float $note_moyenne = 0.0;

    public function __construct(array $data = [])
    {
        $this->id_res = (int)($data['id_res'] ?? $data['id'] ?? 0);
        $this->user_id = (int)($data['user_id'] ?? $data['id'] ?? 0);
        $this->titre = (string)($data['titre'] ?? '');
        $this->description = (string)($data['description'] ?? '');
        $this->matiere = (string)($data['matiere'] ?? '');
        $this->type = (string)($data['type'] ?? '');
        $this->niveau = (string)($data['niveau'] ?? '');
        $this->acces = (string)($data['acces'] ?? '');
        $this->prix = (float)($data['prix'] ?? 0);
        $this->fichier = (string)($data['fichier'] ?? '');
        $this->photo = (string)($data['photo'] ?? '');
        $this->pages = (int)($data['pages'] ?? 0);
        $this->downloads = (int)($data['downloads'] ?? 0);
        $this->date_creation = $data['date_creation'] ?? null;
        $this->note_moyenne = (float)($data['note_moyenne'] ?? 0);
    }

    public function getIdRes(): int { return $this->id_res; }
    public function setIdRes(int $v): void { $this->id_res = $v; }
    public function getUserId(): int { return $this->user_id; }
    public function setUserId(int $v): void { $this->user_id = $v; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $v): void { $this->titre = $v; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $v): void { $this->description = $v; }
    public function getMatiere(): string { return $this->matiere; }
    public function setMatiere(string $v): void { $this->matiere = $v; }
    public function getType(): string { return $this->type; }
    public function setType(string $v): void { $this->type = $v; }
    public function getNiveau(): string { return $this->niveau; }
    public function setNiveau(string $v): void { $this->niveau = $v; }
    public function getAcces(): string { return $this->acces; }
    public function setAcces(string $v): void { $this->acces = $v; }
    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $v): void { $this->prix = $v; }
    public function getFichier(): string { return $this->fichier; }
    public function setFichier(string $v): void { $this->fichier = $v; }
    public function getPhoto(): string { return $this->photo; }
    public function setPhoto(string $v): void { $this->photo = $v; }
    public function getPages(): int { return $this->pages; }
    public function setPages(int $v): void { $this->pages = $v; }
    public function getDownloads(): int { return $this->downloads; }
    public function setDownloads(int $v): void { $this->downloads = $v; }
    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $v): void { $this->date_creation = $v; }
    public function getNoteMoyenne(): float { return $this->note_moyenne; }
    public function setNoteMoyenne(float $v): void { $this->note_moyenne = $v; }
}


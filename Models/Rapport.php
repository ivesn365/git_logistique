<?php
class Rapport {

    private $id;
    private $titre;
    private $contenu;
    private $date_creation;

    public function __construct($id, $titre, $contenu, $date_creation) {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_creation = $date_creation;
    }

    public function ajouter() {
        $sql = "INSERT INTO rapports (`titre`,`contenu`) VALUES ('$this->titre','$this->contenu')";
        return Query::CRUD($sql);
    }

    public static function affichers($query): array {
        return Query::CRUD($query)->fetchAll(PDO::FETCH_OBJ);
    }

    public function supprimer() {
        $sql = "DELETE FROM rapports WHERE `id`='$this->id'";
        return Query::CRUD($sql);
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getDateCreation() { return $this->date_creation; }
}
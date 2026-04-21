<?php
class Sortie {

    private $id;
    private $piece_id;
    private $quantite;
    private $date_sortie;
    private $motif;

    public function __construct($id, $piece_id, $quantite, $date_sortie, $motif) {
        $this->id = $id;
        $this->piece_id = $piece_id;
        $this->quantite = $quantite;
        $this->date_sortie = $date_sortie;
        $this->motif = $motif;
    }

    public function ajouter() {
        
        $sql = "INSERT INTO sorties (`piece_id`,`quantite`,`date_sortie`,motif) 
                VALUES ('$this->piece_id','$this->quantite','$this->date_sortie','$this->motif')";
        Query::CRUD($sql);
        // mise à jour du stock
        $sql2 = "UPDATE pieces SET stock = stock - $this->quantite WHERE id='$this->piece_id'";
        return Query::CRUD($sql2);
    }

    public function supprimer() {
        $sql = "DELETE FROM sorties WHERE `id`='$this->id'";
        return Query::CRUD($sql);
    }

    public static function toDolist($query): array {
        $tab = [];
        if ($query) {
            while ($i = $query->fetch(PDO::FETCH_OBJ)) {
                $tab[] = new Sortie(
                    $i->id,
                    $i->piece_id,
                    $i->quantite,
                    $i->date_sortie,
                    $i->motif 
                );
            }
        }
        return $tab;
    }
    public static function affichers($query): array {
        return self::toDolist(Query::CRUD($query));
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getPieceId() { return $this->piece_id; }
    public function getQuantite() { return $this->quantite; }
    public function getDateSortie() { return $this->date_sortie; }
    public function getMotif() { return $this->motif; }
}

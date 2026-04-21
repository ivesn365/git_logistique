<?php
class Entree {

    private $id;
    private $piece_id;
    private $quantite;
    private $date_entree;
    private $id_four;
    private $idusers;
    private $date_exp;

    public function __construct($id, $piece_id, $quantite, $date_entree, $id_four, $idusers, $date_exp) {
        $this->id = $id;
        $this->piece_id = $piece_id;
        $this->quantite = $quantite;
        $this->date_entree = $date_entree;
        $this->id_four = $id_four;
        $this->idusers = $idusers;
        $this->date_exp = $date_exp;

    }

    public static function affichers($query): array {
        return self::toDolist(Query::CRUD($query));
    }

    public static function afficher($query) {
        return self::toDo(Query::CRUD($query));
    }
    public function ajouter() {
        $sql = "INSERT INTO entrees (`piece_id`,`quantite`,`date_entree`,id_four,idusers,date_exp) 
                VALUES ('$this->piece_id','$this->quantite','$this->date_entree', '$this->id_four', '$this->idusers', '$this->date_exp')";
        Query::CRUD($sql);
        // mise à jour du stock
        $sql2 = "UPDATE pieces SET stock = stock + $this->quantite WHERE id='$this->piece_id'";
        return Query::CRUD($sql2);
    }

    public static function toDolist($query): array {
        $tab = [];
        if ($query) {
            while ($i = $query->fetch(PDO::FETCH_OBJ)) {
                $tab[] = new Entree(
                    $i->id,
                    $i->piece_id,
                    $i->quantite,
                    $i->date_entree,
                    $i->id_four,
                    $i->idusers,
                    $i->date_exp
                );
            }
        }
        return $tab;
    }
   public static function toDo($query) {
        if ($query) {
            $i = $query->fetch(PDO::FETCH_OBJ);
            return new Entree(
                    $i->id,
                    $i->piece_id,
                    $i->quantite,
                    $i->date_entree,
                    $i->id_four,
                    $i->idusers,
                    $i->date_exp
                );
        }
    }
    public function supprimer() {
        $sql = "DELETE FROM entrees WHERE `id`='$this->id'";
        return Query::CRUD($sql);
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getPieceId() { return $this->piece_id; }
    public function getQuantite() { return $this->quantite; }
    public function getDateEntree() { return $this->date_entree; }
    public function getIdFour() { return $this->id_four; }
    public function getIdusers() { return $this->idusers; }
    public function getDate_exp() { return $this->date_exp; }
}
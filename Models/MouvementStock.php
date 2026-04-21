<?php

class MouvementStock {

    private $piece_id;
    private $type;
    private $quantite;
    private $reference;
    private $commentaire;
    private $utilisateur_id;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = htmlspecialchars(trim($value));
            }
        }
    }

    public function enregistrer() {

        // 1️⃣ Enregistrer le mouvement
        Query::CRUD(
            "INSERT INTO mouvements_stock 
            (piece_id, type, quantite, reference, commentaire, utilisateur_id)
            VALUES (?, ?, ?, ?, ?, ?)",
            [
                $this->piece_id,
                $this->type,
                $this->quantite,
                $this->reference,
                $this->commentaire,
                $this->utilisateur_id
            ]
        );

        // 2️⃣ Mettre à jour le stock
        if ($this->type === "ENTREE") {
            Query::CRUD(
                "UPDATE pieces SET stock_actuel = stock_actuel + ? WHERE id = ?",
                [$this->quantite, $this->piece_id]
            );
        }

        if ($this->type === "SORTIE") {
            Query::CRUD(
                "UPDATE pieces SET stock_actuel = stock_actuel - ? WHERE id = ?",
                [$this->quantite, $this->piece_id]
            );
        }

        return true;
    }

    public static function historique($piece_id) {
        return Query::CRUD(
            "SELECT * FROM mouvements_stock WHERE piece_id = ? ORDER BY date_mouvement DESC",
            [$piece_id]
        );
    }
}

<?php

class Fournisseur {

    private $id;
    private $nom;
    private $telephone;
    private $email;
    private $adresse;
    

    public function __construct($id, $nom, $telephone, $email, $adresse) {
        $this->id = $id;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->adresse = $adresse;
    }

    public static function toDoList($query): array {
            $tab = [];
            $key = self::keys();
            if ($query) {
                while ($i = $query->fetch(PDO::FETCH_OBJ)) {
                    $tab[] = new Fournisseur(
                        $i->id,
                        $key->decrypt($i->nom),
                        $key->decrypt($i->telephone),
                        $key->decrypt($i->email),
                        $key->decrypt($i->adresse),
                        $i->date_creation
                    );
                }
            }
    
            return $tab;
    }
    
     /* ==========================
       Conversion un seul objet
    ========================== */

    public static function toDo($query): ?Fournisseur {
        if ($query && $i = $query->fetch(PDO::FETCH_OBJ)) {
            $key = self::keys();
            return new Fournisseur(
                $i->id,
                $key->decrypt($i->nom),
                $key->decrypt($i->telephone),
                $key->decrypt($i->email),
                $key->decrypt($i->adresse),
                $i->date_creation
            );
        }

        return null;
    }

   public static function keys(): AES {
        return new AES("252B6C961AAF3A776FF1B6BCB2139");
    }
    /* ==========================
       Liste via Query::CRUD
    ========================== */

    public static function affichers($sql): array {
        return self::toDoList(Query::CRUD($sql));
    }

    /* ==========================
       Un seul
    ========================== */

    public static function afficher($sql): ?Fournisseur {
        return self::toDo(Query::CRUD($sql));
    }


    public function create() {
        $key = self::keys();
        $nom = $key->encrypt($this->nom);
        $telephone = $key->encrypt($this->telephone);
        $email = $key->encrypt($this->email);
        $adresse = $key->encrypt($this->adresse);
        return Query::CRUD(
            "INSERT INTO fournisseurs (nom, telephone, email, adresse)
             VALUES ('$nom', '$telephone', '$email', '$adresse')");
    }
    
    public function modifier() {
        $key = self::keys();
        $nom = $key->encrypt($this->nom);
        $telephone = $key->encrypt($this->telephone);
        $email = $key->encrypt($this->email);
        $adresse = $key->encrypt($this->adresse);
        
        $sql = "UPDATE fournisseurs SET
                nom='$nom', telephone='$telephone',
                email='$email', adresse='$adresse'
                WHERE id='$this->id'";
        return Query::CRUD($sql);
    }

    public static function getAll() {
        return Query::CRUD("SELECT * FROM fournisseurs ORDER BY nom ASC");
    }
        public function supprimer() {
        $sql = "DELETE FROM fournisseurs WHERE id='$this->id'";
        return Query::CRUD($sql);
    }

    public function getId(){ return $this->id; }
    public function getNom(){ return $this->nom; }
    public function getTelephone(){ return $this->telephone; }
    public function getEmail(){ return $this->email; }
    public function getAdresse(){ return $this->adresse; }

}

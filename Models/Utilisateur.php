<?php
class Utilisateur {

    private $id;
    private $nom;
    private $role;

    public function __construct($id, $nom, $role) {
        $this->id = $id;
        $this->nom = $nom;
        $this->role = $role;
    }

    public static function keys(): AES {
        return new AES("252B6C961AAF3A776FF1B6BCB2139");
    }

    public static function toDoList($query): array {
        $tab = [];
        $key = self::keys();
        if ($query) {
            while ($i = $query->fetch(PDO::FETCH_OBJ)) {
                $tab[] = new Utilisateur(
                    $i->id,
                    $key->decrypt($i->nom ?? ''),
                    $i->role
                );
            }
        }
        return $tab;
    }
    
     public static function toDo($query) {
        $key = self::keys();
        if ($query) {
            $i = $query->fetch(PDO::FETCH_OBJ);
              return new Utilisateur(
                    $i->id,
                    $key->decrypt($i->nom ?? ''),
                    $i->role
                );
            }
      
    }

    public function ajouter() {
        $key = self::keys();
        (new Connexions(0,$this->nom, "12345",$this->role))->ajouter();
        $nom = $key->encrypt(Query::securisation($this->nom));
        $sql = "INSERT INTO utilisateurs (`nom`,`role`) VALUES ('$nom','$this->role')";
        return Query::CRUD($sql);
    }

    public static function affichers($query): array {
        return self::toDoList(Query::CRUD($query));
    }  
    
    public static function afficher($query) {
        return self::toDo(Query::CRUD($query));
    }  

    public function modifier() {
        $key = self::keys();
        $nom = $key->encrypt($this->nom);
        $sql = "UPDATE utilisateurs SET `nom`='$nom',`role`='$this->role' WHERE `id`='$this->id'";
        return Query::CRUD($sql);
    }

    public function supprimer() {
        $sql = "DELETE FROM utilisateurs WHERE `id`='$this->id'";
        return Query::CRUD($sql);
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getRole() { return $this->role; }
}
<?php
class Piece {

    private $id;
    private $code;
    private $nom;
    private $type;
    private $categorie;
    private $sous_categorie;
    private $unite;
    private $reference;
    private $compatibilite;
    private $emplacement;
    private $stock;
    private $stock_min;
    private $stock_max;
    private $seuil;
    private $prix;
    private $criticite;
    private $classe_abc;
    private $numero_lot;

    public function __construct(
        $id,
        $code,
        $nom,
        $type,
        $categorie,
        $sous_categorie,
        $unite,
        $reference,
        $compatibilite,
        $emplacement,
        $stock,
        $stock_min,
        $stock_max,
        $seuil,
        $prix,
        $criticite,
        $classe_abc,
        $numero_lot
    ){
        $this->id = $id;
        $this->code = $code;
        $this->nom = $nom;
        $this->type = $type;
        $this->categorie = $categorie;
        $this->sous_categorie = $sous_categorie;
        $this->unite = $unite;
        $this->reference = $reference;
        $this->compatibilite = $compatibilite;
        $this->emplacement = $emplacement;
        $this->stock = $stock;
        $this->stock_min = $stock_min;
        $this->stock_max = $stock_max;
        $this->seuil = $seuil;
        $this->prix = $prix;
        $this->criticite = $criticite;
        $this->classe_abc = $classe_abc;
        $this->numero_lot = $numero_lot;
    }

    public static function keys(): AES {
        return new AES("252B6C961AAF3A776FF1B6BCB2139");
    }

    public static function toDoList($query): array {

        $tab = [];
        $key = self::keys();

        if($query){
            while($i = $query->fetch(PDO::FETCH_OBJ)){

                $tab[] = new Piece(
                    $i->id,
                    $key->decrypt($i->code ?? ''),
                    $key->decrypt($i->nom ?? ''),
                    $key->decrypt($i->type ?? ''),
                    $i->categorie,
                    $i->sous_categorie,
                    $i->unite,
                    $i->reference,
                    $i->compatibilite,
                    $i->emplacement,
                    $i->stock,
                    $i->stock_min,
                    $i->stock_max,
                    $i->seuil,
                    $i->prix,
                    $i->criticite,
                    $i->classe_abc,
                    $i->numero_lot
                );
            }
        }

        return $tab;
    }


public static function toDo($query) {
        $key = self::keys();

        if($query){
           $i = $query->fetch(PDO::FETCH_OBJ);

            return new Piece(
                    $i->id,
                    $key->decrypt($i->code ?? ''),
                    $key->decrypt($i->nom ?? ''),
                    $key->decrypt($i->type ?? ''),
                    $i->categorie,
                    $i->sous_categorie,
                    $i->unite,
                    $i->reference,
                    $i->compatibilite,
                    $i->emplacement,
                    $i->stock,
                    $i->stock_min,
                    $i->stock_max,
                    $i->seuil,
                    $i->prix,
                    $i->criticite,
                    $i->classe_abc,
                    $i->numero_lot
                );
            }


    }


 public static function affichers($query){ return self::toDoList(Query::CRUD($query)); }
    public static function afficher($query){ return self::toDo(Query::CRUD($query)); }
    public function ajouter(){

        $key = self::keys();

        $code = $key->encrypt(Query::securisation($this->code));
        $nom = $key->encrypt(Query::securisation($this->nom));
        $type = $key->encrypt(Query::securisation($this->type));

        $sql = "INSERT INTO pieces
        (code,nom,type,categorie,sous_categorie,unite,reference,compatibilite,
        emplacement,stock,stock_min,stock_max,seuil,prix,criticite,classe_abc,numero_lot)

        VALUES(
        '$code',
        '$nom',
        '$type',
        '$this->categorie',
        '$this->sous_categorie',
        '$this->unite',
        '$this->reference',
        '$this->compatibilite',
        '$this->emplacement',
        '$this->stock',
        '$this->stock_min',
        '$this->stock_max',
        '$this->seuil',
        '$this->prix',
        '$this->criticite',
        '$this->classe_abc',
        '$this->numero_lot'
        )";

        return Query::CRUD($sql);
    }

    public function modifier(){

        $key = self::keys();

        $code = $key->encrypt(Query::securisation($this->code));
        $nom = $key->encrypt(Query::securisation($this->nom));
        $type = $key->encrypt(Query::securisation($this->type));

        $sql = "UPDATE pieces SET
        code='$code',
        nom='$nom',
        type='$type',
        categorie='$this->categorie',
        sous_categorie='$this->sous_categorie',
        unite='$this->unite',
        reference='$this->reference',
        compatibilite='$this->compatibilite',
        emplacement='$this->emplacement',
        stock='$this->stock',
        stock_min='$this->stock_min',
        stock_max='$this->stock_max',
        seuil='$this->seuil',
        prix='$this->prix',
        criticite='$this->criticite',
        classe_abc='$this->classe_abc',
        numero_lot='$this->numero_lot'

        WHERE id='$this->id'";

        return Query::CRUD($sql);
    }

    public static function supprimer($id){
        return Query::CRUD("DELETE FROM pieces WHERE id='$id'");
    }

    public function getId(){ return $this->id; }
    public function getCode(){ return $this->code; }
    public function getNom(){ return $this->nom; }
    public function getType(){ return $this->type; }
    public function getCategorie(){ return $this->categorie; }
    public function getSousCategorie(){ return $this->sous_categorie; }
    public function getUnite(){ return $this->unite; }
    public function getReference(){ return $this->reference; }
    public function getCompatibilite(){ return $this->compatibilite; }
    public function getEmplacement(){ return $this->emplacement; }
    public function getStock(){ return $this->stock; }
    public function getStockMin(){ return $this->stock_min; }
    public function getStockMax(){ return $this->stock_max; }
    public function getSeuil(){ return $this->seuil; }
    public function getPrix(){ return $this->prix; }
    public function getCriticite(){ return $this->criticite; }
    public function getClasseABC(){ return $this->classe_abc; }
    public function getNumeroLot(){ return $this->numero_lot; }
    
    public static function genererSKU($categorie, $nom){
    
        $prefixCat = strtoupper(substr($categorie,0,3));
        $prefixNom = strtoupper(substr($nom,0,4));
    
        $count = Query::CRUD("SELECT COUNT(*) as total FROM pieces")
                 ->fetch(PDO::FETCH_ASSOC)['total'] + 1;
    
        $numero = str_pad($count,3,"0",STR_PAD_LEFT);
    
        return $prefixCat."-".$prefixNom."-".$numero;
    }

}
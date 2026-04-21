<?php
class Connexions {
    private $id;
    private $username;
    private $password;
    private $role;

    public function __construct($id, $username, $password, $role) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public static function keys(): AES {
        return new AES("252B6C961AAF3Ajd776FF1B6BCB2139");
    }

    // --- Login ---
    public static function login($username, $password) {
        $key = self::keys();
        $password = md5($password);
        $username = $key->encrypt(Query::securisation($username));
        $sql = "SELECT * FROM connexion WHERE username = '$username' AND password = '$password'";
        $user = Query::CRUD($sql)->fetch(PDO::FETCH_OBJ);

        if ($user) {
            return new Connexions($user->id, $key->decrypt($user->username), $user->password, $key->decrypt($user->role));
        }
        return null;
    }

    // --- Ajouter un nouvel utilisateur ---
    public function ajouter() {
        $pdo = Connexion::GetConnexion();
        $key = self::keys();

        // Hash du mot de passe
        $hashPassword = md5($this->password);

        $sql = "INSERT INTO connexion (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'username' => $key->encrypt($this->username),
            'password' => $hashPassword,
            'role'     => $key->encrypt($this->role)
        ]);
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getRole() { return $this->role; }
}
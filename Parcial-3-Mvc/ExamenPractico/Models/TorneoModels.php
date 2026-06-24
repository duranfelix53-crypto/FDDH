<?php
    require_once(__DIR__ . "/../config/DataBase.php");
    class torneosModel {

        public $PDO;

        public function __construct()
        {
            $conecction = new DataBase();
            $this->PDO = $conecction->connect();
        }

        public function insert($nombreTorneo, $organizador, $patrocinadores, $sede, $categoria,
        $premio1, $premio2, $premio3, $otroPremio, $usuario, $contrasena){
            $contrasena = $this->passwordEncrypt($contrasena);
            $statement = $this->PDO->prepare("INSERT INTO torneos VALUES(null, :nombreTorneo,
            :organizador, :patrocinadores, :sede, :categoria, :premio1, :premio2, :premio3,
            :otroPremio, :usuario, :contrasena)");
            $statement->bindParam(":nombreTorneo", $nombreTorneo);
            $statement->bindParam(":organizador", $organizador);
            $statement->bindParam(":patrocinadores", $patrocinadores);
            $statement->bindParam(":sede", $sede);
            $statement->bindParam(":categoria", $categoria);
            $statement->bindParam(":premio1", $premio1);
            $statement->bindParam(":premio2", $premio2);
            $statement->bindParam(":premio3", $premio3);
            $statement->bindParam(":otroPremio", $otroPremio);
            $statement->bindParam(":usuario", $usuario);
            $statement->bindParam(":contrasena", $contrasena);
            return ($statement->execute()) ? $this->PDO->lastInsertId() : false ;
        }

        public function passwordEncrypt($password){
            $passwordEncrypted = password_hash($password, PASSWORD_DEFAULT);
            return $passwordEncrypted;
        }

        public function passwordDencryted($passwordEncrypted, $passwordCandidate){
            return (password_verify($passwordCandidate, $passwordEncrypted)) ? true : false;
        }

        public function read(){
            $statement = $this->PDO->prepare("SELECT * FROM torneos");
            return ($statement->execute()) ? $statement->fetchAll() : false;
        }

        public function readOne($id){
            $statement = $this->PDO->prepare("SELECT * FROM torneos WHERE id= :id limit 1");
            $statement->bindParam(":id", $id);
            return ($statement->execute()) ? $statement->fetch() : false;
        }

        public function update($id, $nombreTorneo, $organizador, $patrocinadores, $sede,
        $categoria, $premio1, $premio2, $premio3, $otroPremio){
            $statement = $this->PDO->prepare("UPDATE torneos SET nombreTorneo = :nombreTorneo,
            organizador = :organizador,patrocinadores = :patrocinadores, SEDE = :sede, CATEGORIA
            = :categoria, premio1 = :premio1, premio2 = :premio2, premio3 = :premio3, otroPremio
            = :otroPremio WHERE id = :id");

            $statement->bindParam(":id", $id);
            $statement->bindParam(":nombreTorneo", $nombreTorneo);
            $statement->bindParam(":organizador", $organizador);
            $statement->bindParam(":patrocinadores", $patrocinadores);
            $statement->bindParam(":sede", $sede);
            $statement->bindParam(":categoria", $categoria);
            $statement->bindParam(":premio1", $premio1);
            $statement->bindParam(":premio2", $premio2);
            $statement->bindParam(":premio3", $premio3);
            $statement->bindParam(":otroPremio", $otroPremio);

            return ($statement->execute()) ? $id : false;
        }

        public function delete($id){
            $statement = $this->PDO->prepare("DELETE FROM torneos WHERE id= :id");
            $statement->bindParam(":id", $id);
            return ($statement->execute()) ? true : false;
        }
    }

?>
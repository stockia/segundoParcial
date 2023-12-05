<?php

require_once __DIR__ . '/../db/AccesoDatos.php';

class Usuario {
    public $id;
    public $rol;
    public $username;
    public $password;

    public function InsertarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT INTO usuarios (username, rol, password) 
            VALUES (:username, :rol, :password)");
        $consulta->bindValue(':username', $this->username, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':password', $this->password, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosUsuarios() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "usuario");
    }

    public static function TraerUnUsuario($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public static function TraerUnUsuarioPorNombre($nombre) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios WHERE nombre=:nombre');
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public static function TraerUnUsuarioPorUsername($username) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios WHERE username=:username');
        $consulta->bindValue(':username', $username, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public static function TraerUsuariosPorTipo($rol) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios WHERE rol=:rol');
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->execute(array(':rol' => $rol));
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public function ModificarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE usuarios 
            SET nombre=:nombre, rol=:rol, username=:username, password=:password
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':username', $this->username, PDO::PARAM_STR);
        $consulta->bindValue(':password', $this->password, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function BorrarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE usuarios
            SET statusUsuario='borrado'
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public static function ValidarCredenciales($username, $password) {
        $usuario = Usuario::TraerUnUsuarioPorUsername($username);

        if ($usuario && $usuario->password == $password) {
            return $usuario;
        } else {
            return null;
        }
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getrol() {
        return $this->rol;
    }
}
?>

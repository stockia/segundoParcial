<?php
class Logs{
    public $id;
    public $date;
    public $metodo;
    public $path;

    public function TraerTodosLogs($orden = "ASC") {
        $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT * FROM log ORDER BY date " . $orden);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Logs");
    }
    
}
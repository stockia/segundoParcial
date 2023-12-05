<?php

    class LogsReservas {
        public $id;
        public $fecha;
        public $username;
        public $metodo;
        public $path;
        
        public function TraerTodosLogsReserva() {
            $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT * FROM log_reservas");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "LogsReservas");
        }
    }
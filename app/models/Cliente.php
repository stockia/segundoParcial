<?php
class Cliente {
    public $nombreApellido;
    public $tipoDocumento;
    public $numeroDocumento;
    public $email;
    public $tipoCliente;
    public $pais;
    public $ciudad;
    public $telefono;
    public $id;
    public $modalidadPago;
    public $estado;

    public function InsertarCliente() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $estado = "activo";
        $modalidadPagoCliente = $this->modalidadPago == null ? "efectivo" : $this->modalidadPago;
        $response = "";

        if (Cliente::buscarClientePorIdDocumento($this->id, $this->numeroDocumento)) {
            $consulta = $objAccesoDatos->RetornarConsulta(
                "UPDATE clientes
                SET nombreApellido = :nombreApellido, tipoDocumento = :tipoDocumento, numeroDocumento = :numeroDocumento, email = :email, tipoCliente = :tipoCliente, pais = :pais, ciudad = :ciudad, telefono = :telefono, modalidadPago = :modalidadPago
                WHERE id = :id 
                AND numeroDocumento = :numeroDocumento"
            );

            $response = "Cliente actualizado";
        } else {
            $consulta = $objAccesoDatos->RetornarConsulta(
                "INSERT INTO clientes (id, nombreApellido, tipoDocumento, numeroDocumento, email, tipoCliente, pais, ciudad, telefono, modalidadPago, estado) 
                VALUES (:id, :nombreApellido, :tipoDocumento, :numeroDocumento, :email, :tipoCliente, :pais, :ciudad, :telefono, :modalidadPago, :estado)"
            );

            $response = "Cliente creado";
        }

        $consulta->bindValue(':nombreApellido', $this->nombreApellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
        $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
        $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_STR);
        $consulta->bindValue(':modalidadPago', $modalidadPagoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = $this->numeroDocumento . $this->tipoCliente . "." . $extension;
    
            $rutaCarpeta = __DIR__ . '/../ImagenesDeClientes/2023/';
            $rutaArchivo = $rutaCarpeta . $nombreArchivo;
    
            if (!file_exists($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true);
            }
    
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaArchivo)) {
            } else {
            }
        }

        return $response;
    }

    public function ModificarCliente() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta(
            "UPDATE clientes
            SET nombreApellido = :nombreApellido, 
                tipoDocumento = :tipoDocumento, 
                numeroDocumento = :numeroDocumento, 
                email = :email, 
                tipoCliente = :tipoCliente, 
                pais = :pais, 
                ciudad = :ciudad, 
                telefono = :telefono, 
                modalidadPago = :modalidadPago
            WHERE tipoDocumento = :tipoDocumentoCond 
            AND numeroDocumento = :numeroDocumentoCond" 
        );
    
        $consulta->bindValue(':nombreApellido', $this->nombreApellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
        $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
        $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_STR);
        $consulta->bindValue(':modalidadPago', $this->modalidadPago, PDO::PARAM_STR);
    
        $consulta->bindValue(':tipoDocumentoCond', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumentoCond', $this->numeroDocumento, PDO::PARAM_STR);
    
        $consulta->execute();
    
        return $consulta->rowCount();
    }
    

    public function BorrarCliente() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta(
            "UPDATE clientes
            SET estado = 'borrado'
            WHERE id = :id 
            AND tipoCliente = :tipoCliente"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function TraerTodosLosClientes() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM clientes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
    }

    public static function buscarClientePorTipoNumeroDocumento($tipoDocumento, $numeroDocumento) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT pais, ciudad, telefono 
            FROM clientes 
            WHERE tipoDocumento = :tipoDocumento 
            AND numeroDocumento = :numeroDocumento"
        );
        $consulta->bindValue(':tipoDocumento', $tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $numeroDocumento, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Cliente');
    }

    public static function buscarClientePorIdDocumento($id, $numeroDocumento) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * FROM clientes WHERE id = :id AND numeroDocumento = :numeroDocumento"
        );

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':numeroDocumento', $numeroDocumento, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Cliente');
    }

    public static function TraerUnCliente($id) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objAccesoDatos->RetornarConsulta(
            "SELECT * FROM clientes WHERE id = :id"
        );

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Cliente');
    }
}
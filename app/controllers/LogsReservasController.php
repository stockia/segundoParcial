<?php

require_once __DIR__ . '/../models/LogsReservas.php';

class LogsReservasController extends LogsReservas {
    public function DescargarComoPDF($request, $response, $args) {
        $logsReservas = LogsReservas::TraerTodosLogsReserva();
    
        $directory = __DIR__ . '/../archivosTemporales/';
        $filename = 'logs_reservas.pdf';
        $filepath = $directory . $filename;
    
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); 
        }
    
        $handle = fopen($filepath, 'w');
    
        foreach ($logsReservas as $log) {
            $line = implode(' ', get_object_vars($log)) . "\n";
            fwrite($handle, $line);
        }
        fclose($handle);
    
        $pdfContent = file_get_contents($filepath);
    
        $responseBody = $response->getBody();
        $responseBody->write($pdfContent);
    
        return $response
            // ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    } 
    
}
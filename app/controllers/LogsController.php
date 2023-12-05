<?php

require_once __DIR__ . '/../models/Logs.php';

class LogsController extends Logs {

    public function DescargarComoCSV($request, $response, $args) {
        $queryParams = $request->getQueryParams();
        $orden = $queryParams['orden'] ?? 'ASC';
        $productos = Logs::TraerTodosLogs();
    
        $directory = __DIR__ . '/../archivosTemporales/';
        $filename = 'logs.csv';
        $filepath = $directory . $filename;
    
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); 
        }
    
        $handle = fopen($filepath, 'w');
    
        foreach ($logs as $log) {
            fputcsv($handle, get_object_vars($log));
        }
        fclose($handle);
    
        $csvContent = file_get_contents($filepath);
    
        $responseBody = $response->getBody();
        $responseBody->write($csvContent);
    
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    } 
}
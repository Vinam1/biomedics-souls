<?php

class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', [
            'title' => 'Página no encontrada',
        ]);
    }
}

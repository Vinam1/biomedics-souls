<?php

class ProductController extends Controller
{
    public function show(string $slug): void
    {
        $product = Producto::findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Producto no encontrado',
            ]);
            return;
        }

        $badges = Etiqueta::forProduct($product['id']);
        $images = Producto::images($product['id']);
        $reviews = Resena::forProduct($product['id']);
        $currentUser = $this->getCurrentUser();
        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $hasReviewed = false;
        $canReview = false;
        if ($currentUser) {
            $hasReviewed = Resena::userHasReviewed((int) $currentUser['id'], (int) $product['id']);
            $canReview = !$hasReviewed && Resena::canReview((int) $currentUser['id'], (int) $product['id']);
        }

        $mappedProduct = [
            'id' => $product['id'],
            'name' => $product['nombre'],
            'slug' => $product['slug'],
            'price' => $product['precio_descuento'] ?: $product['precio'],
            'rating' => (float) ($product['calificacion_promedio'] ?: 0),
            'reviews_count' => (int) ($product['total_resenas'] ?: 0),
            'image' => !empty($images) ? asset_url('img/products/' . $images[0]['url_imagen']) : null,
            'thumbnail' => !empty($images) ? asset_url('img/products/' . $images[0]['url_imagen']) : null,
            'short_description' => $product['descripcion_corta'],
            'presentation' => $product['contenido_neto'],
            'content' => $product['contenido_neto'],
            'category' => $product['categoria_nombre'],
            'benefits' => array_filter(array_map('trim', explode("\n", $product['beneficios']))),
            'description' => $product['descripcion_larga'],
            'usage_instructions' => $product['modo_empleo'],
            'sku' => $product['sku'],
            'category_slug' => $product['categoria_slug'],
            'status' => $product['estatus'],
            'is_out_of_stock' => Producto::isOutOfStockStatus($product['estatus'] ?? null),
        ];

        $this->view('pages/producto', [
            'title' => htmlspecialchars($mappedProduct['name']),
            'product' => $mappedProduct,
            'badges' => $badges,
            'images' => $images,
            'reviews' => $reviews,
            'flashSuccess' => $flashSuccess,
            'flashError' => $flashError,
            'canReview' => $canReview,
            'hasReviewed' => $hasReviewed,
        ]);
    }

    public function addReview(string $slug): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $user = $this->getCurrentUser();
        if (!$user) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        $product = Producto::findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Producto no encontrado',
            ]);
            return;
        }

        $rating = (int) ($_POST['rating'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'La calificación debe estar entre 1 y 5.';
            header('Location: ' . site_url('producto/' . $slug));
            exit;
        }

        if (Resena::userHasReviewed($user['id'], $product['id'])) {
            $_SESSION['error'] = 'Ya has reseñado este producto.';
            header('Location: ' . site_url('producto/' . $slug));
            exit;
        }

        if (!Resena::canReview($user['id'], $product['id'])) {
            $_SESSION['error'] = 'Debes haber recibido el producto para poder reseñarlo.';
            header('Location: ' . site_url('producto/' . $slug));
            exit;
        }

        $success = Resena::create([
            'cliente_id' => $user['id'],
            'producto_id' => $product['id'],
            'calificacion' => $rating,
            'titulo' => $title,
            'comentario' => $comment,
        ]);

        if ($success) {
            $_SESSION['success'] = 'Gracias por tu reseña.';
        } else {
            $_SESSION['error'] = 'Error al guardar la reseña.';
        }

        header('Location: ' . site_url('producto/' . $slug));
        exit;
    }
}

<?php

class Cart
{
    /**
     * Obtiene los items del carrito con toda la información del producto (incluyendo imagen)
     */
    public static function getItems(): array
    {
        $items = [];
        $cart = $_SESSION['cart'] ?? [];

        foreach ($cart as $productId => $quantity) {
            $product = Producto::findById((int) $productId);

            if (!$product) {
                continue;
            }

            // === CORRECCIÓN PRINCIPAL: Cargar imagen siempre ===
            if (empty($product['imagen_principal'])) {
                $images = Producto::images((int) $productId);
                $product['imagen_principal'] = $images[0]['url_imagen'] ?? null;
            }

            // Calcular precio final (con descuento si existe)
            $price = $product['precio_descuento'] ?? $product['precio'];

            $items[] = [
                'product'  => $product,
                'quantity' => (int) $quantity,
                'subtotal' => $price * (int) $quantity,
            ];
        }

        return $items;
    }

    public static function add(int $productId, int $quantity = 1): void
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    public static function remove(int $productId): void
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    public static function setQuantity(int $productId, int $quantity): void
    {
        if (!isset($_SESSION['cart'])) {
            return;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            return;
        }

        $_SESSION['cart'][$productId] = $quantity;
    }

    public static function getTotal(): float
    {
        $total = 0.0;
        foreach (self::getItems() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }
}
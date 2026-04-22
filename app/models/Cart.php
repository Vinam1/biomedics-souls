<?php

class Cart
{
    public static function getItems(): array
    {
        $items = [];
        $cart = $_SESSION['cart'] ?? [];
        $products = Producto::findManyByIds(array_keys($cart));

        foreach ($cart as $productId => $quantity) {
            $product = $products[(int) $productId] ?? null;
            if (!$product) {
                continue;
            }

            $price = $product['precio_descuento'] ?? $product['precio'];

            $items[] = [
                'product' => $product,
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

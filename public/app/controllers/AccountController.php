<?php

class AccountController extends Controller
{
    public function updateProfile(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'email' => strtolower(trim($_POST['email'] ?? '')),
            'telefono' => preg_replace('/\D+/', '', $_POST['telefono'] ?? ''),
        ];

        if ($data['nombre'] === '' || $data['apellidos'] === '' || $data['email'] === '') {
            $this->redirectWithMessage('config', 'error', 'Nombre, apellidos y correo son obligatorios.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage('config', 'error', 'Ingresa un correo electrónico válido.');
        }

        $existing = Usuario::findByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== (int) $user['id']) {
            $this->redirectWithMessage('config', 'error', 'Ese correo ya está registrado por otra cuenta.');
        }

        if ($data['telefono'] !== '' && !preg_match('/^[0-9]{10}$/', $data['telefono'])) {
            $this->redirectWithMessage('config', 'error', 'El teléfono debe tener exactamente 10 dígitos.');
        }

        Usuario::updateProfile((int) $user['id'], $data);
        $this->redirectWithMessage('config', 'success', 'Tu información personal fue actualizada.');
    }

    public function saveAddress(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();

        $addressId = (int) ($_POST['address_id'] ?? 0);
        $data = [
            'calle' => trim($_POST['calle'] ?? ''),
            'numero_exterior' => trim($_POST['numero_exterior'] ?? ''),
            'numero_interior' => trim($_POST['numero_interior'] ?? ''),
            'colonia' => trim($_POST['colonia'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'estado' => trim($_POST['estado'] ?? ''),
            'pais' => trim($_POST['pais'] ?? 'Mexico'),
            'codigo_postal' => preg_replace('/\D+/', '', $_POST['codigo_postal'] ?? ''),
            'referencias' => trim($_POST['referencias'] ?? ''),
            'es_principal' => isset($_POST['es_principal']),
        ];

        if ($data['calle'] === '' || $data['numero_exterior'] === '' || $data['colonia'] === '' || $data['ciudad'] === '' || $data['estado'] === '' || $data['pais'] === '' || $data['codigo_postal'] === '') {
            $this->redirectWithMessage('direcciones', 'error', 'Completa todos los campos obligatorios de la dirección.');
        }

        if (!preg_match('/^[0-9]{5}$/', $data['codigo_postal'])) {
            $this->redirectWithMessage('direcciones', 'error', 'El código postal debe tener 5 dígitos.');
        }

        if ($addressId > 0) {
            $address = Direccion::findByIdForCliente($addressId, (int) $user['id']);
            if (!$address) {
                $this->redirectWithMessage('direcciones', 'error', 'La dirección que intentas editar no existe.');
            }
            Direccion::update($addressId, (int) $user['id'], $data);
            $this->redirectWithMessage('direcciones', 'success', 'La dirección fue actualizada correctamente.');
        }

        Direccion::create((int) $user['id'], $data);
        $this->redirectWithMessage('direcciones', 'success', 'La dirección fue agregada correctamente.');
    }

    public function deleteAddress(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();

        Direccion::softDelete((int) $id, (int) $user['id']);
        $this->redirectWithMessage('direcciones', 'success', 'La dirección fue eliminada.');
    }

    public function savePaymentMethod(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();

        $paymentId = (int) ($_POST['payment_id'] ?? 0);
        $data = [
            'tipo' => trim($_POST['tipo'] ?? ''),
            'brand' => trim($_POST['brand'] ?? ''),
            'ultimo_cuatro' => preg_replace('/\D+/', '', $_POST['ultimo_cuatro'] ?? ''),
            'tipo_tarjeta' => trim($_POST['tipo_tarjeta'] ?? ''),
            'nickname' => trim($_POST['nickname'] ?? ''),
            'es_predeterminado' => isset($_POST['es_predeterminado']),
            'activo' => isset($_POST['activo']) ? (bool) $_POST['activo'] : true,
        ];

        $allowedTypes = ['tarjeta', 'mercado_pago', 'spei', 'oxxo', 'transferencia', 'otro'];
        if (!in_array($data['tipo'], $allowedTypes, true)) {
            $this->redirectWithMessage('pagos', 'error', 'Selecciona un tipo de método de pago válido.');
        }

        if ($data['tipo'] === 'tarjeta') {
            if (!preg_match('/^[0-9]{4}$/', $data['ultimo_cuatro'])) {
                $this->redirectWithMessage('pagos', 'error', 'Para tarjetas debes capturar los últimos 4 dígitos.');
            }
            if (!in_array($data['tipo_tarjeta'], ['credito', 'debito'], true)) {
                $this->redirectWithMessage('pagos', 'error', 'Indica si la tarjeta es de crédito o débito.');
            }
        } else {
            $data['ultimo_cuatro'] = '';
            $data['tipo_tarjeta'] = '';
        }

        if ($paymentId > 0) {
            $paymentMethod = MetodoPago::findByIdForCliente($paymentId, (int) $user['id']);
            if (!$paymentMethod) {
                $this->redirectWithMessage('pagos', 'error', 'El método de pago que intentas editar no existe.');
            }
            MetodoPago::update($paymentId, (int) $user['id'], $data);
            $this->redirectWithMessage('pagos', 'success', 'El método de pago fue actualizado correctamente.');
        }

        MetodoPago::create((int) $user['id'], $data);
        $this->redirectWithMessage('pagos', 'success', 'El método de pago fue agregado correctamente.');
    }

    public function deletePaymentMethod(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();

        MetodoPago::softDelete((int) $id, (int) $user['id']);
        $this->redirectWithMessage('pagos', 'success', 'El método de pago fue eliminado.');
    }

    private function redirectWithMessage(string $tab, string $type, string $message): void
    {
        $_SESSION['account_flash'] = [
            'type' => $type,
            'message' => $message,
        ];

        header('Location: ' . site_url('cuenta?tab=' . $tab));
        exit;
    }
}

<?php

class Forma extends BaseEntityModel
{
    protected static string $table = 'formas_presentacion';
    protected static array $columns = ['id', 'nombre'];
    protected static bool $hasSlug = false;
}

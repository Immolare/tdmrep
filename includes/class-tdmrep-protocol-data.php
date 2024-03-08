<?php
declare(strict_types=1);

class Tdmrep_Protocol_Data
{
    private const OPTION_NAME = 'tdmrep_protocol';

    public function __construct()
    {
        // Rien à faire ici pour l'instant
    }

    public function get_protocol(): ?string
    {
        return get_option(self::OPTION_NAME, 'header');
    }

    public function save_protocol(string $protocol): bool
    {
        return update_option(self::OPTION_NAME, $protocol);
    }

    public function delete_protocol(): bool
    {
        return delete_option(self::OPTION_NAME);
    }
}
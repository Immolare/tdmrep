<?php
declare(strict_types=1);

class Tdmrep_Duty
{
    private const OBTAINCONSENT = 'obtainConsent';
    private const COMPENSATE = 'compensate';

    private string $action;

    public function __construct(string $action)
    {
        if (!in_array($action, ['', self::OBTAINCONSENT, self::COMPENSATE])) {
            throw new InvalidArgumentException("Invalid duty");
        }
        $this->action = $action;
    }

    public function get_action(): string
    {
        return $this->action;
    }

    public function to_array_or_null(): ?array
    {
        $action = $this->get_action();

        return [
            'action' => $action
        ];
    }

    public function to_json_ld(): array
    {
        return [
            'action' => $this->get_action()
        ];
    }

    public static function from_json_ld(array $data): self
    {
        return new self($data['action']);
    }
}
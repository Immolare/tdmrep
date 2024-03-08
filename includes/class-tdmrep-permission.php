<?php
class Tdmrep_Permission
{
    private const TDMMINE = 'tdm:mine';

    private ?string $target = null;
    private ?string $action = null;
    private array $duties = [];
    private array $constraints = [];

    public function __construct()
    {
        // Rien à faire ici pour l'instant
    }

    public function get_target(): ?string
    {
        return $this->target;
    }

    public function set_target(string $target): void
    {
        $this->target = $target;
    }

    public function get_action(): ?string
    {
        return $this->action;
    }

    public function set_action(string $action): void
    {
        if ($action != self::TDMMINE) {
            throw new InvalidArgumentException("Invalid action");
        }
        $this->action = $action;
    }

    public function get_duties(): array
    {
        return $this->duties;
    }

    public function add_duty(Tdmrep_Duty $duty): void
    {
        $this->duties[] = $duty;
    }

    public function get_constraints(): array
    {
        return $this->constraints;
    }

    public function add_constraint(Tdmrep_Constraint $constraint): void
    {
        $this->constraints[] = $constraint;
    }

    public function to_array_or_null(): ?array
    {
        $duties = array_map(function($duty) {
            return $duty->to_array_or_null() ? $duty->to_array_or_null()['action'] : null;
        }, $this->get_duties());
        
        $constraints = array_map(function($constraint) {
            return $constraint->to_array_or_null() ? $constraint->to_array_or_null()['right_operand'] : null;
        }, $this->get_constraints());

        $target = $this->get_target();
        $action = $this->get_action();

        if (empty($target) && empty($action) && empty($duties) && empty($constraints)) {
            return null;
        }

        return [
            'target' => $target,
            'action' => $action,
            'duties' => reset($duties),
            'constraints' => reset($constraints),
        ];
    }

    public function to_json_ld(): array
    {
        $json_ld = [];

        if (!empty($this->get_target())) {
            $json_ld['target'] = $this->get_target();
        }

        if (!empty($this->get_action())) {
            $json_ld['action'] = $this->get_action();
        }

        if (!empty($this->get_duties())) {
            $json_ld['duty'] = array_map(fn($duty) => $duty->to_json_ld(), $this->get_duties());
        }

        if (!empty($this->get_constraints())) {
            $json_ld['constraint'] = array_map(fn($constraint) => $constraint->to_json_ld(), $this->get_constraints());
        }

        return $json_ld;
    }

    public static function from_json_ld(array $data): self
    {
        $permission = new self();

        $permission->target = $data['target'] ?? null;
        $permission->action = $data['action'] ?? null;

        if (isset($data['duty'])) {
            foreach ($data['duty'] as $duty) {
                $permission->add_duty(Tdmrep_Duty::from_json_ld($duty));
            }
        }

        if (isset($data['constraint'])) {
            foreach ($data['constraint'] as $constraint) {
                $permission->add_constraint(Tdmrep_Constraint::from_json_ld($constraint));
            }
        }

        return $permission;
    }
}
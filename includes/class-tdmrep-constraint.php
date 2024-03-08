<?php
declare(strict_types=1);

class Tdmrep_Constraint
{
    private const RESEARCH = 'research';
    private const NON_RESEARCH = 'non-research';

    private string $left_operand;
    private string $operator;
    private ?string $right_operand;

    public function __construct(?string $purpose)
    {
        $this->left_operand = 'purpose';
        $this->operator = 'eq';
        if ($purpose === self::RESEARCH) {
            $this->right_operand = self::RESEARCH;
        } elseif ($purpose === self::NON_RESEARCH) {
            $this->right_operand = self::NON_RESEARCH;
        } elseif (empty($purpose)) {
            $this->right_operand = null;
        }else {
            throw new InvalidArgumentException('Invalid purpose');
        }
    }

    public function get_left_operand(): string
    {
        return $this->left_operand;
    }

    public function get_operator(): string
    {
        return $this->operator;
    }

    public function get_right_operand(): ?string
    {
        return $this->right_operand;
    }

    public function to_array_or_null(): ?array
    {
        $right_operand = $this->get_right_operand();
    
        return [
            'right_operand' => $right_operand
        ];
    }

    public function to_json_ld(): array
    {
        return [
            'leftOperand' => $this->get_left_operand(),
            'operator' => $this->get_operator(),
            'rightOperand' => $this->get_right_operand()
        ];
    }

    public static function from_json_ld(array $data): self
    {
        return new self($data['rightOperand']);
    }
}
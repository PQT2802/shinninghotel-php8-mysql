<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function __construct(
        private array $data,
        private array $rules,
        private array $context = []
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->addError($field, $this->label($field) . ' is required.');
                    continue;
                }
                if ($value === null || $value === '') {
                    continue;
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (is_string($value) && mb_strlen($value) > $max) {
                        $this->addError($field, $this->label($field) . " must not exceed {$max} characters.");
                    }
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (is_string($value) && mb_strlen($value) < $min) {
                        $this->addError($field, $this->label($field) . " must be at least {$min} characters.");
                    }
                }
                if ($rule === 'email' && !filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Invalid email address.');
                }
                if (str_starts_with($rule, 'in:')) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array((string) $value, $allowed, true)) {
                        $this->addError($field, $this->label($field) . ' has an invalid value.');
                    }
                }
                if (str_starts_with($rule, 'unique:')) {
                    $parts = explode(',', substr($rule, 7));
                    $table = $parts[0] ?? '';
                    $column = $parts[1] ?? $field;
                    $excludeId = $this->context['exclude_id'] ?? null;
                    if ($this->valueExists($table, $column, (string) $value, $excludeId)) {
                        $this->addError($field, $this->label($field) . ' is already taken.');
                    }
                }
            }
        }
    }

    private function valueExists(string $table, string $column, string $value, ?int $excludeId): bool
    {
        $allowedTables = ['users', 'pages', 'news', 'rooms', 'room_categories'];
        if (!in_array($table, $allowedTables, true)) {
            return false;
        }
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";
        $params = ['value' => $value];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function label(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}

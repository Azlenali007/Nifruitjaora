<?php

namespace App\Helpers;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool {
        $this->errors = [];
        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                if ($rule === 'required' && ($value === null || trim((string)$value) === '')) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                    break;
                }

                if ($value === null || $value === '') {
                    continue; // Optional field if not required
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be numeric.';
                } elseif ($rule === 'min' && strlen((string)$value) < (int)$params[0]) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$params[0]} characters.";
                } elseif ($rule === 'max' && strlen((string)$value) > (int)$params[0]) {
                    $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " may not exceed {$params[0]} characters.";
                } elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = 'Please provide a valid email address.';
                } elseif ($rule === 'phone') {
                    $cleaned = preg_replace('/[^0-9]/', '', (string)$value);
                    if (strlen($cleaned) < 10 || strlen($cleaned) > 12) {
                        $this->errors[$field][] = 'Please provide a valid 10-digit mobile number.';
                    }
                } elseif ($rule === 'pincode') {
                    $cleaned = preg_replace('/[^0-9]/', '', (string)$value);
                    if (strlen($cleaned) !== 6) {
                        $this->errors[$field][] = 'Please provide a valid 6-digit Indian PIN code.';
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}

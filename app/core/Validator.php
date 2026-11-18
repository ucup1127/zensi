<?php
declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data  = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function validate(): bool
    {
        $this->errors = [];
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $ruleString);
            foreach ($rules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $method = 'rule' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $param);
                }
            }
        }
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $errs) {
            return $errs[0] ?? null;
        }
        return null;
    }

    // ---- Rules ----

    private function ruleRequired(string $field, mixed $value): void
    {
        if (is_array($value) ? empty($value) : trim((string) $value) === '') {
            $this->errors[$field][] = "{$field} wajib diisi.";
        }
    }

    private function ruleString(string $field, mixed $value): void
    {
        if ($value !== null && !is_string($value)) {
            $this->errors[$field][] = "{$field} harus berupa teks.";
        }
    }

    private function ruleMin(string $field, mixed $value, ?string $param): void
    {
        // Jika tipenya string, cek panjang karakter
        if (is_string($value)) {
            if (mb_strlen($value) < (int) $param) {
                $this->errors[$field][] = "{$field} minimal {$param} karakter.";
            }
        } 
        // Jika tipenya angka asli (integer/float), cek nilainya
        elseif (is_int($value) || is_float($value)) {
            if ($value < (float) $param) {
                $this->errors[$field][] = "{$field} minimal {$param}.";
            }
        }
    }

    private function ruleMax(string $field, mixed $value, ?string $param): void
    {
        // Jika tipenya string, cek panjang karakter
        if (is_string($value)) {
            if (mb_strlen($value) > (int) $param) {
                $this->errors[$field][] = "{$field} maksimal {$param} karakter.";
            }
        } 
        // Jika tipenya angka asli (integer/float), cek nilainya
        elseif (is_int($value) || is_float($value)) {
            if ($value > (float) $param) {
                $this->errors[$field][] = "{$field} maksimal {$param}.";
            }
        }
    }

    private function ruleEmail(string $field, mixed $value): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$field} harus berupa email valid.";
        }
    }

    private function ruleNumeric(string $field, mixed $value): void
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->errors[$field][] = "{$field} harus berupa angka.";
        }
    }

    private function ruleIn(string $field, mixed $value, ?string $param): void
    {
        $allowed = explode(',', (string) $param);
        if ($value !== null && !in_array((string) $value, $allowed, true)) {
            $this->errors[$field][] = "{$field} tidak valid.";
        }
    }
}
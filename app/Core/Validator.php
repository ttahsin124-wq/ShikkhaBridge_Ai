<?php
class Validator
{
    public array $errors = [];

    public function __construct(private array $data) {}

    private function val(string $k): string { return trim((string)($this->data[$k] ?? '')); }

    public function required(string $k, string $label): self
    {
        if ($this->val($k) === '') $this->errors[$k] = "$label can't be empty.";
        return $this;
    }

    public function email(string $k, string $label = 'Email'): self
    {
        if ($this->val($k) !== '' && !filter_var($this->val($k), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$k] = "$label is not a valid email address.";
        }
        return $this;
    }

    public function min(string $k, int $n, string $label): self
    {
        if ($this->val($k) !== '' && mb_strlen($this->val($k)) < $n) $this->errors[$k] = "$label must be at least $n characters.";
        return $this;
    }

    public function max(string $k, int $n, string $label): self
    {
        if (mb_strlen($this->val($k)) > $n) $this->errors[$k] = "$label must be at most $n characters.";
        return $this;
    }

    /** Passwords need at least one letter and one number (length is checked with min()). */
    public function password(string $k, string $label = 'Password'): self
    {
        $v = (string)($this->data[$k] ?? '');
        if ($v !== '' && !isset($this->errors[$k]) && (!preg_match('/[A-Za-z]/', $v) || !preg_match('/\d/', $v))) {
            $this->errors[$k] = "$label must include at least one letter and one number.";
        }
        return $this;
    }

    public function matches(string $k, string $other, string $label): self
    {
        if (($this->data[$k] ?? '') !== ($this->data[$other] ?? '')) $this->errors[$k] = "$label does not match.";
        return $this;
    }

    public function in(string $k, array $allowed, string $label): self
    {
        if (!in_array($this->data[$k] ?? null, $allowed, true)) $this->errors[$k] = "$label is invalid.";
        return $this;
    }

    public function fails(): bool { return !empty($this->errors); }
}

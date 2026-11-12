<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;
use InvalidArgumentException;

/**
 * Validasi input sederhana.
 * PRD: 2.2.x (acceptance criteria)
 *
 *   - static make(array $data, array $rules, array $labels = []): array — kembalikan array error per field (pesan Bahasa Indonesia)
 *   - Aturan: required, min:n, max:n, in:a,b, date (Y-m-d), time (H:i), color (#RRGGBB)
 *
 * Contoh:
 *   Validator::make($_POST, ['full_name' => 'required|min:3|max:100'], ['full_name' => 'Nama lengkap']);
 *   → ['full_name' => 'Nama lengkap wajib diisi.'] jika kosong. Hanya pesan pertama per field.
 */
final class Validator
{
    public static function make(array $data, array $rules, array $labels = []): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', (string) $fieldRules);
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', (string) $field));
            $raw = $data[$field] ?? null;
            $value = is_scalar($raw) ? trim((string) $raw) : null;
            $empty = $value === null || $value === '';

            foreach ($fieldRules as $rule) {
                [$name, $param] = array_pad(explode(':', (string) $rule, 2), 2, null);

                if ($name === 'required') {
                    if ($empty) {
                        $errors[$field] = "$label wajib diisi.";
                        break;
                    }
                    continue;
                }

                // Aturan lain hanya diperiksa jika field diisi.
                if ($empty) {
                    break;
                }

                $message = self::check($name, $param, $value, $label);
                if ($message !== null) {
                    $errors[$field] = $message;
                    break;
                }
            }
        }

        return $errors;
    }

    private static function check(string $name, ?string $param, string $value, string $label): ?string
    {
        switch ($name) {
            case 'min':
                return mb_strlen($value) < (int) $param ? "$label minimal $param karakter." : null;

            case 'max':
                return mb_strlen($value) > (int) $param ? "$label maksimal $param karakter." : null;

            case 'in':
                $allowed = explode(',', (string) $param);
                return in_array($value, $allowed, true) ? null : "Pilihan $label tidak valid.";

            case 'date':
                $d = DateTime::createFromFormat('!Y-m-d', $value);
                return ($d && $d->format('Y-m-d') === $value)
                    ? null
                    : "$label harus berupa tanggal yang valid.";

            case 'time':
                return preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $value)
                    ? null
                    : "$label harus berupa jam yang valid, contoh 08:30.";

            case 'color':
                return preg_match('/^#[0-9a-f]{6}$/i', $value)
                    ? null
                    : "$label harus berupa kode warna, contoh #0F766E.";

            default:
                throw new InvalidArgumentException('Aturan validasi tidak dikenal: ' . $name);
        }
    }
}

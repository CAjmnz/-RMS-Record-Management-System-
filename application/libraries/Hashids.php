<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple reversible ID hasher for CI3
 * Drop into application/libraries/Hashids.php
 *
 * Usage:
 *   $this->load->library('hashids');
 *   $hash = $this->hashids->encode(42);     // e.g. "a9Kp2m"
 *   $id   = $this->hashids->decode('a9Kp2m'); // 42  (false on invalid input)
 */
class Hashids
{
    private $salt;
    public function __construct()
{
    $CI =& get_instance();
    $this->salt = $CI->config->item('encryption_key');
}
    private $alphabet   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private $min_length = 6;

    // ── Encode integer → hash string ──────────────────────────────────
    public function encode($number)
    {
        if (!is_numeric($number) || (int) $number < 1) return false;

        $number   = (int) $number;
        $alphabet = $this->_shuffle($this->alphabet); // always a string
        $base     = strlen($alphabet);

        $hash = '';
        do {
            $hash   = $alphabet[$number % $base] . $hash;
            $number = (int) floor($number / $base);
        } while ($number > 0);

        // Pad to min_length using first char of shuffled alphabet
        while (strlen($hash) < $this->min_length) {
            $hash = $alphabet[0] . $hash;
        }

        return $hash;
    }

    // ── Decode hash string → integer ──────────────────────────────────
    public function decode($hash)
    {
        if (empty($hash) || !is_string($hash)) return false;

        $alphabet = $this->_shuffle($this->alphabet); // always a string
        $base     = strlen($alphabet);
        $pad_char = $alphabet[0];

        // Strip leading padding
        $stripped = ltrim($hash, $pad_char);
        if ($stripped === '') $stripped = $pad_char;

        $number = 0;
        for ($i = 0, $len = strlen($stripped); $i < $len; $i++) {
            $pos = strpos($alphabet, $stripped[$i]);
            if ($pos === false) return false; // character not in alphabet = forged
            $number = $number * $base + $pos;
        }

        if ($number < 1) return false;

        // Round-trip verification — prevents accepting forged/padded hashes
        if ($this->encode($number) !== $hash) return false;

        return $number;
    }

    // ── Fisher-Yates shuffle seeded by salt — always returns a string ─
    private function _shuffle($alphabet)
    {
        // Work entirely with a string, never convert to array
        $salt     = $this->salt;
        $salt_len = strlen($salt);
        $len      = strlen($alphabet);

        if ($salt_len === 0) return $alphabet;

        $index   = 0;
        $integer = 0;

        for ($i = $len - 1; $i > 0; $i--) {
            $integer += ord($salt[$index]);
            $j        = ($integer + $index + ord($salt[$index])) % $i;
            $index    = ($index + 1) % $salt_len;

            // Swap characters at positions $i and $j
            if ($i !== $j) {
                $tmp        = $alphabet[$i];
                $alphabet[$i] = $alphabet[$j];
                $alphabet[$j] = $tmp;
            }
        }

        return $alphabet; // still a string — PHP string indexing preserves type
    }
}
<?php

namespace App\Services;

class PaymentService
{
    /**
     * Validate credit card using Luhn algorithm
     */
    public static function validateCardNumber($cardNumber)
    {
        // Remove spaces and hyphens
        $cardNumber = preg_replace('/\s+|-/', '', $cardNumber);
        
        // Check if it's only digits
        if (!preg_match('/^\d+$/', $cardNumber)) {
            return false;
        }
        
        // Check length (most cards are 13-19 digits)
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }
        
        // Apply Luhn algorithm
        return self::luhnCheck($cardNumber);
    }
    
    /**
     * Luhn algorithm implementation
     */
    private static function luhnCheck($cardNumber)
    {
        $digits = array_reverse(str_split($cardNumber));
        $sum = 0;
        
        foreach ($digits as $key => $digit) {
            if ($key % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }
        
        return $sum % 10 === 0;
    }
    
    /**
     * Validate card expiration date
     */
    public static function validateExpiration($month, $year)
    {
        // Validate month (01-12)
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            return false;
        }
        
        // Validate year (2-4 digits)
        if (!is_numeric($year) || strlen($year) < 2 || strlen($year) > 4) {
            return false;
        }
        
        // Convert 2-digit year to 4-digit
        if (strlen($year) === 2) {
            $year = intval($year) < 50 ? '20' . $year : '19' . $year;
        }
        
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));
        
        $expiryYear = intval($year);
        $expiryMonth = intval($month);
        
        // Check if card is expired
        if ($expiryYear < $currentYear) {
            return false;
        }
        
        if ($expiryYear === $currentYear && $expiryMonth < $currentMonth) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate CVV (3 or 4 digits)
     */
    public static function validateCVV($cvv)
    {
        // Remove spaces
        $cvv = preg_replace('/\s+/', '', $cvv);
        
        // Check if it's 3 or 4 digits
        return preg_match('/^\d{3,4}$/', $cvv) === 1;
    }
    
    /**
     * Validate cardholder name
     */
    public static function validateCardholderName($name)
    {
        // At least 3 characters, max 255
        return strlen($name) >= 3 && strlen($name) <= 255;
    }
    
    /**
     * Process credit card payment
     */
    public static function processPayment(array $paymentData)
    {
        $errors = [];
        
        // Validate card number
        if (empty($paymentData['card_number'])) {
            $errors[] = 'Card number is required';
        } elseif (!self::validateCardNumber($paymentData['card_number'])) {
            $errors[] = 'Invalid card number';
        }
        
        // Validate expiration
        if (empty($paymentData['expiry_month']) || empty($paymentData['expiry_year'])) {
            $errors[] = 'Expiration date is required';
        } elseif (!self::validateExpiration($paymentData['expiry_month'], $paymentData['expiry_year'])) {
            $errors[] = 'Card has expired or invalid expiration date';
        }
        
        // Validate CVV
        if (empty($paymentData['cvv'])) {
            $errors[] = 'CVV is required';
        } elseif (!self::validateCVV($paymentData['cvv'])) {
            $errors[] = 'Invalid CVV format';
        }
        
        // Validate cardholder name
        if (empty($paymentData['cardholder_name'])) {
            $errors[] = 'Cardholder name is required';
        } elseif (!self::validateCardholderName($paymentData['cardholder_name'])) {
            $errors[] = 'Invalid cardholder name';
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }
        
        // In production, integrate with a payment gateway like Stripe, PayPal, etc.
        // For now, simulate payment processing
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'transaction_id' => 'TXN-' . bin2hex(random_bytes(8)),
            'status' => 'completed',
        ];
    }
    
    /**
     * Detect card type
     */
    public static function getCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\s+|-/', '', $cardNumber);
        
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            return 'visa';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
            return 'mastercard';
        } elseif (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
            return 'amex';
        } elseif (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
            return 'discover';
        }
        
        return 'unknown';
    }
}

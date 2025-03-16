<?php

namespace App\Traits;

trait DocumentValidation
{
    /**
     * Valida um CPF
     */
    public function validateCPF($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        // Verifica o primeiro dígito verificador
        if ($cpf[9] != $digit1) {
            return false;
        }

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        // Verifica o segundo dígito verificador
        return $cpf[10] == $digit2;
    }

    /**
     * Valida um CNPJ
     */
    public function validateCNPJ($cnpj)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        $weight = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight == 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        // Verifica o primeiro dígito verificador
        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Calcula o segundo dígito verificador
        $sum = 0;
        $weight = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight == 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        // Verifica o segundo dígito verificador
        return $cnpj[13] == $digit2;
    }

    /**
     * Valida um documento (CPF ou CNPJ)
     */
    public function validateDocument($document)
    {
        $document = preg_replace('/[^0-9]/', '', $document);
        
        if (strlen($document) === 11) {
            return $this->validateCPF($document);
        } elseif (strlen($document) === 14) {
            return $this->validateCNPJ($document);
        }
        
        return false;
    }

    /**
     * Formata um documento (CPF ou CNPJ)
     */
    public function formatDocument($document)
    {
        $document = preg_replace('/[^0-9]/', '', $document);
        
        if (strlen($document) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document);
        } elseif (strlen($document) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document);
        }
        
        return $document;
    }
}

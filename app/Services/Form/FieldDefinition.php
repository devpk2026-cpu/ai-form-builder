<?php

namespace App\Services\Form;

use App\Enums\FieldType;

class FieldDefinition
{
    public static function all(): array
    {
        return [
            FieldType::TEXT->value => [
                'label' => 'Text',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::TEXTAREA->value => [
                'label' => 'Textarea',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::NUMBER->value => [
                'label' => 'Number',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::EMAIL->value => [
                'label' => 'Email',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::PHONE->value => [
                'label' => 'Phone',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::DATE->value => [
                'label' => 'Date',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::DROPDOWN->value => [
                'label' => 'Dropdown',
                'supports_options' => true,
                'supports_validation' => true,
            ],

            FieldType::RADIO->value => [
                'label' => 'Radio',
                'supports_options' => true,
                'supports_validation' => true,
            ],

            FieldType::CHECKBOX->value => [
                'label' => 'Checkbox',
                'supports_options' => true,
                'supports_validation' => true,
            ],

            FieldType::FILE->value => [
                'label' => 'File Upload',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::RATING->value => [
                'label' => 'Rating',
                'supports_options' => false,
                'supports_validation' => true,
            ],

            FieldType::SECTION->value => [
                'label' => 'Section Heading',
                'supports_options' => false,
                'supports_validation' => false,
            ],
        ];
    }

    public static function exists(string $type): bool
    {
        return array_key_exists($type, self::all());
    }

    public static function get(string $type): ?array
    {
        return self::all()[$type] ?? null;
    }
}
<?php

namespace App\Enums;

enum FieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case DATE = 'date';
    case DROPDOWN = 'dropdown';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';
    case FILE = 'file';
    case RATING = 'rating';
    case SECTION = 'section';
}
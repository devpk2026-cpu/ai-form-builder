<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
            ]
        );

        Form::create([
            'user_id' => $user->id,
            'title' => 'Internship Application',
            'slug' => 'internship-application',
            'description' => 'Sample internship application form.',
            'schema_json' => [
                'version' => '1.0',
                'title' => 'Internship Application',
                'description' => 'Sample internship application form.',
                'settings' => [
                    'submit_button' => 'Submit Application',
                ],
                'sections' => [
                    [
                        'id' => 'personal_information',
                        'title' => 'Personal Information',
                        'fields' => [
                            [
                                'id' => 'full_name',
                                'type' => 'text',
                                'key' => 'full_name',
                                'label' => 'Full Name',
                                'placeholder' => 'Enter your full name',
                                'help_text' => null,
                                'default' => null,
                                'required' => true,
                                'validation' => [
                                    'min_length' => 2,
                                    'max_length' => 100,
                                ],
                            ],
                            [
                                'id' => 'email',
                                'type' => 'email',
                                'key' => 'email',
                                'label' => 'Email Address',
                                'placeholder' => 'Enter your email',
                                'help_text' => null,
                                'default' => null,
                                'required' => true,
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
            'status' => 'published',
        ]);
    }
}
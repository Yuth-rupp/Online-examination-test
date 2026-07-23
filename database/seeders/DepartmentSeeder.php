<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed real-world department names (not placeholder "Department A/B")
     * for every institution currently in the system. Safe to run more
     * than once — existing departments are matched by (institution_id,
     * code) and left untouched.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Data Science',                       'code' => 'DS'],
            ['name' => 'Information Technology Engineering',  'code' => 'ITE'],
            ['name' => 'Computer Science',                    'code' => 'CS'],
            ['name' => 'Business Administration',             'code' => 'BA'],
            ['name' => 'Electrical Engineering',               'code' => 'EE'],
            ['name' => 'Civil Engineering',                    'code' => 'CE'],
            ['name' => 'Mathematics',                          'code' => 'MATH'],
            ['name' => 'English Language',                     'code' => 'ENG'],
        ];

        // Fall back to creating a default institution if none exists yet,
        // so this seeder works even on a totally empty database.
        $institutions = Institution::all();
        if ($institutions->isEmpty()) {
            $institutions = collect([
                Institution::create([
                    'name'      => 'Main Institution',
                    'is_active' => true,
                ]),
            ]);
        }

        foreach ($institutions as $institution) {
            foreach ($departments as $dept) {
                Department::firstOrCreate(
                    [
                        'institution_id' => $institution->id,
                        'code'           => $dept['code'],
                    ],
                    [
                        'name'        => $dept['name'],
                        'description' => null,
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}

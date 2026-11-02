<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    /** @var list<string> */
    protected array $userFields = ['name', 'phone', 'locale'];

    /** @var list<string> */
    protected array $patientFields = [
        'date_of_birth',
        'gender',
        'address',
        'city',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    public function patientProfile(User $user): Patient
    {
        $patient = $user->patient()->with('user')->first();

        if (! $patient) {
            throw new ModelNotFoundException(__('profile_api.not_found'));
        }

        return $patient;
    }

    public function updatePatientProfile(User $user, array $data): Patient
    {
        $patient = $this->patientProfile($user);

        $user->update(array_intersect_key($data, array_flip($this->userFields)));

        $patient->update(array_intersect_key($data, array_flip($this->patientFields)));

        return $patient->fresh(['user']);
    }

    // public function exportPatientData(User $user): StreamedResponse
    // {
    //     $patient = $user->patient;

    //     $data = [
    //         'account' => [
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'phone' => $user->phone,
    //             'locale' => $user->locale,
    //             'created_at' => $user->created_at?->toISOString(),
    //         ],

    //         'profile' => [
    //             'date_of_birth' => $patient?->date_of_birth?->toDateString(),
    //             'gender' => $patient?->gender,
    //             'address' => $patient?->address,
    //             'city' => $patient?->city,
    //             'country' => $patient?->country,
    //             'emergency_contact_name' => $patient?->emergency_contact_name,
    //             'emergency_contact_phone' => $patient?->emergency_contact_phone,
    //         ],
    //     ];

    //     return response()->streamDownload(
    //         function () use ($data) {
    //             echo json_encode(
    //                 $data,
    //                 JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    //             );
    //         },
    //         'my-data.json',
    //         [
    //             'Content-Type' => 'application/json',
    //         ]
    //     );
    // }

    public function exportPatientData(User $user)
    {
        $patient = $user->patient;

        $data = [
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'locale' => $user->locale,
                'created_at' => $user->created_at?->toISOString(),
            ],

            'profile' => [
                'date_of_birth' => $patient?->date_of_birth?->toDateString(),
                'gender' => $patient?->gender,
                'address' => $patient?->address,
                'city' => $patient?->city,
                'country' => $patient?->country,
                'emergency_contact_name' => $patient?->emergency_contact_name,
                'emergency_contact_phone' => $patient?->emergency_contact_phone,
            ],
        ];

        return response()->streamDownload(
            function () use ($data) {
                $handle = fopen('php://output', 'w');

                // UTF-8 BOM for Excel compatibility
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    //'Section',
                    'Field',
                    'Value',
                ]);

                foreach ($data as $section => $fields) {
                    foreach ($fields as $field => $value) {
                        fputcsv($handle, [
                            //ucfirst($section),
                            ucwords(str_replace('_', ' ', $field)),
                            $value,
                        ]);
                    }
                }

                fclose($handle);
            },
            'my-data.csv',
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="my-data.csv"',
            ]
        );
    }


    public function deletePatientAccount(User $user): void
    {
        DB::transaction(function () use ($user) {

            $patient = $user->patient;

            /*
             * Delete/anonymize related personal data here.
             */

            $patient?->delete();

            /*
             * If you are using SoftDeletes on User:
             */
            $user->delete();
        });
    }


}

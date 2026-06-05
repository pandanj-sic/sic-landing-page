<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admission::create([
            'title' => 'Admission Requirements',
            'content' => '<h3>For Freshmen</h3><ul><li>Original High School Report Card (Form 138)</li><li>Certificate of Good Moral Character</li><li>Photocopy of PSA Birth Certificate</li><li>Two (2) recent 2x2 ID pictures</li></ul><h3>For Transferees</h3><ul><li>Honorable Dismissal or Transfer Credentials</li><li>Official Transcript of Records or Copy of Grades</li><li>Certificate of Good Moral Character</li><li>Two (2) recent 2x2 ID pictures</li></ul>',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Admission::create([
            'title' => 'Enrollment Procedure',
            'content' => '<ol><li><strong>Step 1: Application</strong> - Fill out the online application form and submit the scanned copy of requirements.</li><li><strong>Step 2: Verification</strong> - Wait for the Registrar\'s office to verify and evaluate your documents.</li><li><strong>Step 3: Assessment</strong> - Proceed to the Business Office for assessment of tuition and fees.</li><li><strong>Step 4: Payment</strong> - Pay the corresponding fees at the Cashier or authorized payment channels.</li><li><strong>Step 5: Completion</strong> - Claim your official Certificate of Registration (COR) and student ID.</li></ol>',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Admission::create([
            'title' => 'Tuition & Fees',
            'content' => '<p>At San Isidro College, we offer highly competitive tuition fees and flexible installment schemes to support our students\' academic journey.</p><p>For detailed assessment, fee breakdown, and downpayment schedules, please contact the Business and Finance Office at finance@sic.edu.ph or visit the registrar portal.</p>',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}

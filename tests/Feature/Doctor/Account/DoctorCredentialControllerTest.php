<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorCredential;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoctorCredentialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_is_redirected_to_login(): void
    {
        $this->post(route('doctor.account.professional-profile.credentials.store'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_add_a_credential_with_private_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.credentials.store'), [
                'type' => 'board',
                'title' => 'البورد الأردني في الطب الباطني',
                'issuer' => 'المجلس الطبي الأردني',
                'country' => 'الأردن',
                'issued_on' => '2020-06-18',
                'expires_on' => null,
                'document' => UploadedFile::fake()->create('board.pdf', 300, 'application/pdf'),
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'credentials']));
        $response->assertSessionHas('status', 'تمت إضافة المؤهل وإرساله للمراجعة.');

        $credential = DoctorCredential::query()->sole();
        $this->assertSame($doctor->id, $credential->doctor_id);
        $this->assertSame(DoctorCredential::STATUS_PENDING, $credential->verification_status);
        $this->assertSame('board.pdf', $credential->document_original_name);
        Storage::disk('local')->assertExists($credential->document_path);
    }

    public function test_credential_requires_valid_fields_and_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.professional-profile'))
            ->post(route('doctor.account.professional-profile.credentials.store'), [
                'type' => 'invalid',
                'title' => '',
                'issuer' => '',
                'country' => '',
                'issued_on' => now()->addDay()->toDateString(),
                'document' => UploadedFile::fake()->create('script.php', 10, 'text/x-php'),
            ]);

        $response->assertInvalid([
            'type' => 'نوع المؤهل المحدد غير صحيح.',
            'title' => 'أدخل اسم المؤهل أو الشهادة.',
            'issuer' => 'أدخل اسم الجهة المصدرة.',
            'country' => 'أدخل الدولة.',
            'issued_on' => 'تاريخ الإصدار لا يمكن أن يكون في المستقبل.',
            'document' => 'يجب أن يكون ملف الإثبات PDF أو JPG أو PNG.',
        ], 'credential');
        $this->assertDatabaseEmpty('doctor_credentials');
    }

    public function test_doctor_can_download_only_their_credential_document(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $credential = DoctorCredential::factory()->for($doctor)->create();
        Storage::disk('local')->put($credential->document_path, 'private document');

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile.credentials.document', $credential))
            ->assertOk();

        $otherDoctor = Doctor::factory()->create();
        $this->actingAs($otherDoctor, 'doctor')
            ->get(route('doctor.account.professional-profile.credentials.document', $credential))
            ->assertNotFound();
    }

    public function test_doctor_can_delete_a_pending_credential_and_its_document(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $credential = DoctorCredential::factory()->for($doctor)->create();
        Storage::disk('local')->put($credential->document_path, 'private document');

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.credentials.destroy', $credential));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'credentials']));
        $this->assertModelMissing($credential);
        Storage::disk('local')->assertMissing($credential->document_path);
    }

    public function test_verified_credential_cannot_be_deleted(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $credential = DoctorCredential::factory()->verified()->for($doctor)->create();
        Storage::disk('local')->put($credential->document_path, 'private document');

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.credentials.destroy', $credential));

        $response->assertInvalid(['credential' => 'لا يمكن حذف مؤهل موثّق. تواصل مع الدعم لتعديله.']);
        $this->assertModelExists($credential);
        Storage::disk('local')->assertExists($credential->document_path);
    }
}

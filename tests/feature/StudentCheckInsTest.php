<?php

use App\Models\CheckInModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Database;

/**
 * @internal
 */
final class StudentCheckInsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;

    protected $namespace = null;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
    }

    public function testStudentCanCreateMultipleCheckInsInOneDay(): void
    {
        $student = $this->createUser('checkin-multiple', 'student');
        $this->authenticateAs($student);

        $page = $this->getAuthenticated('student/check-ins');
        $page->assertOK();
        $page->assertSee('Private check-ins');
        $page->assertSee('Only you can see check-ins; Aralytics shows pattern counts.');

        $this->postAuthenticated('student/check-ins', ['workload_rating' => '1', 'energy_rating' => '5'])->assertRedirect();
        $this->postAuthenticated('student/check-ins', ['workload_rating' => '5', 'energy_rating' => '2'])->assertRedirect();

        $saved = (new CheckInModel())->findAllForOwner((int) $student->id);
        $this->assertCount(2, $saved);
        $this->assertSame(5, (int) $saved[0]['workload_rating']);
        $this->assertSame(2, (int) $saved[0]['energy_rating']);
        $this->assertSame(1, (int) $saved[1]['workload_rating']);
        $this->assertSame(5, (int) $saved[1]['energy_rating']);
        $this->assertNotSame((int) $saved[0]['id'], (int) $saved[1]['id']);

        $page = $this->getAuthenticated('student/check-ins');
        $page->assertSee('Workload: 5/5 · Energy: 2/5');
        $page->assertSee('Workload: 1/5 · Energy: 5/5');
    }

    public function testRatingsMustBeBetweenOneAndFive(): void
    {
        $student = $this->createUser('checkin-validation', 'student');
        $this->authenticateAs($student);

        foreach ([
            ['workload_rating' => '0', 'energy_rating' => '3'],
            ['workload_rating' => '3', 'energy_rating' => '6'],
            ['workload_rating' => '-1', 'energy_rating' => '3'],
            ['workload_rating' => '2.5', 'energy_rating' => '3'],
            ['workload_rating' => '4', 'energy_rating' => ''],
        ] as $input) {
            $this->postAuthenticated('student/check-ins', $input)->assertRedirect();
        }

        $this->assertSame([], (new CheckInModel())->findAllForOwner((int) $student->id));
    }

    public function testStudentCanEditAndDeleteOwnCheckIn(): void
    {
        $student = $this->createUser('checkin-crud', 'student');
        $model = new CheckInModel();
        $checkInId = (int) $model->createForOwner((int) $student->id, 2, 3, '2026-10-17 10:15:00');
        $this->authenticateAs($student);

        $edit = $this->getAuthenticated('student/check-ins/' . $checkInId . '/edit');
        $edit->assertOK();
        $edit->assertSee('Edit private check-in');
        $this->postAuthenticated('student/check-ins/' . $checkInId, ['workload_rating' => '4', 'energy_rating' => '5'])->assertRedirect();

        $updated = $model->findForOwner($checkInId, (int) $student->id);
        $this->assertSame(4, (int) $updated['workload_rating']);
        $this->assertSame(5, (int) $updated['energy_rating']);
        $this->assertSame('2026-10-17 10:15:00', $updated['recorded_at']);

        $delete = $this->getAuthenticated('student/check-ins/' . $checkInId . '/delete');
        $delete->assertOK();
        $delete->assertSee('Delete private check-in?');
        $this->postAuthenticated('student/check-ins/' . $checkInId . '/delete/confirm')->assertRedirect();
        $this->assertNotNull($model->findForOwner($checkInId, (int) $student->id));
        $this->postAuthenticated('student/check-ins/' . $checkInId . '/delete/confirm', ['confirm_delete' => '1'])->assertRedirect();
        $this->assertNull($model->findForOwner($checkInId, (int) $student->id));
    }

    public function testStudentCannotReadOrChangeAnotherOwnersCheckIn(): void
    {
        $owner = $this->createUser('checkin-private-owner', 'student');
        $other = $this->createUser('checkin-private-other', 'student');
        $model = new CheckInModel();
        $checkInId = (int) $model->createForOwner((int) $other->id, 5, 4, '2026-10-17 09:00:00');
        $this->authenticateAs($owner);

        $list = $this->getAuthenticated('student/check-ins');
        $list->assertOK();
        $list->assertDontSee('Workload: 5/5 · Energy: 4/5');
        $this->assertNotFound(fn () => $this->getAuthenticated('student/check-ins/' . $checkInId . '/edit'));
        $this->assertNotFound(fn () => $this->getAuthenticated('student/check-ins/' . $checkInId . '/delete'));
        $this->assertNotFound(fn () => $this->postAuthenticated('student/check-ins/' . $checkInId, ['workload_rating' => '1', 'energy_rating' => '1']));
        $this->assertNotFound(fn () => $this->postAuthenticated('student/check-ins/' . $checkInId . '/delete/confirm', ['confirm_delete' => '1']));

        $saved = $model->findForOwner($checkInId, (int) $other->id);
        $this->assertSame(5, (int) $saved['workload_rating']);
        $this->assertSame(4, (int) $saved['energy_rating']);
    }

    public function testIndividualCheckInValuesAreExcludedFromAdminAndAralyticsReport(): void
    {
        $student = $this->createUser('checkin-excluded-student', 'student');
        $this->assertNotFalse((new CheckInModel())->createForOwner((int) $student->id, 2, 4, '2026-10-17 08:00:00'));

        $this->authenticateAs($student);
        $report = $this->getAuthenticated('student/aralytics');
        $report->assertOK();
        $report->assertDontSee('Workload: 2/5');
        $report->assertDontSee('Energy: 4/5');
        $search = $this->getAuthenticated('student/topics');
        $search->assertOK();
        $search->assertDontSee('Workload: 2/5');
        $search->assertDontSee('Energy: 4/5');

        auth()->logout();
        $admin = $this->createUser('checkin-excluded-admin', 'admin');
        $this->authenticateAs($admin);
        $adminPage = $this->getAuthenticated('admin');
        $adminPage->assertOK();
        $adminPage->assertDontSee('Private check-ins');
        $adminPage->assertDontSee('Workload: 2/5');
        $this->getAuthenticated('student/check-ins')->assertStatus(403);
    }

    public function testCheckInRoutesRequireStudentRoleAndCsrf(): void
    {
        $admin = $this->createUser('checkin-admin-route', 'admin');
        $this->authenticateAs($admin);
        $this->getAuthenticated('student/check-ins')->assertStatus(403);
        $this->postAuthenticated('student/check-ins', ['workload_rating' => '3', 'energy_rating' => '3'])->assertStatus(403);

        auth()->logout();
        $response = $this->get('student/check-ins');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testCheckInCreationRequiresCsrf(): void
    {
        $student = $this->createUser('checkin-csrf', 'student');
        $this->authenticateAs($student);
        try {
            $this->post('student/check-ins', ['workload_rating' => '3', 'energy_rating' => '4']);
            $this->fail('Saving a check-in without a CSRF token must be rejected.');
        } catch (SecurityException) {
            $this->assertTrue(true);
        }

        $this->assertSame([], (new CheckInModel())->findAllForOwner((int) $student->id));
    }

    private function createUser(string $username, string $group): User
    {
        $model = new UserModel();
        $user = new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]);
        $userId = $model->insert($user);
        $created = $model->findById((int) $userId);
        $created->addGroup($group);

        return $created;
    }

    private function authenticateAs(User $user): void
    {
        $this->actingAs($user);
        $this->withSession($_SESSION);
    }

    private function getAuthenticated(string $path): TestResponse
    {
        $this->withSession($_SESSION);
        $response = $this->get($path);
        $this->withSession($_SESSION);

        return $response;
    }

    private function postAuthenticated(string $path, array $data = []): TestResponse
    {
        $data[csrf_token()] = csrf_hash();
        $this->withSession($_SESSION);
        $response = $this->post($path, $data);
        $this->withSession($_SESSION);

        return $response;
    }

    private function assertNotFound(callable $request): void
    {
        try {
            $request();
            $this->fail('A foreign check-in must be indistinguishable from a missing record.');
        } catch (PageNotFoundException) {
            $this->assertTrue(true);
        }
    }
}

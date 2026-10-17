<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Services\CheckInSummaryService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use DateTimeZone;

class CheckIns extends BaseController
{
    public function index(): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);
        $checkIns = (new CheckInModel())->findAllForOwner($this->ownerId());

        return view('student/check-ins/index', [
            'checkIns' => $checkIns,
            'summary' => (new CheckInSummaryService())->summarize(
                $checkIns,
                '7_days',
                new DateTimeImmutable('now', $timezone),
                $timezone,
            ),
            'errors' => session()->getFlashdata('errors') ?? [],
            'pageTitle' => 'Private check-ins',
        ]);
    }

    public function create(): RedirectResponse
    {
        $input = $this->validRatingInput('student/check-ins');
        if ($input instanceof RedirectResponse) {
            return $input;
        }

        $created = (new CheckInModel())->createForOwner(
            $this->ownerId(),
            $input['workload_rating'],
            $input['energy_rating'],
            $this->now(),
        );

        if ($created === false) {
            return redirect()->to(site_url('student/check-ins'))->withInput()->with('error', 'The check-in could not be saved. Please try again.');
        }

        return redirect()->to(site_url('student/check-ins'))->with('success', 'Private check-in saved.');
    }

    public function edit(int $checkInId): string
    {
        $checkIn = (new CheckInModel())->findForOwner($checkInId, $this->ownerId());
        if ($checkIn === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('student/check-ins/edit', [
            'checkIn' => $checkIn,
            'errors' => session()->getFlashdata('errors') ?? [],
            'pageTitle' => 'Edit private check-in',
        ]);
    }

    public function update(int $checkInId): RedirectResponse
    {
        $ownerId = $this->ownerId();
        if ((new CheckInModel())->findForOwner($checkInId, $ownerId) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $returnPath = 'student/check-ins/' . $checkInId . '/edit';
        $input = $this->validRatingInput($returnPath);
        if ($input instanceof RedirectResponse) {
            return $input;
        }

        if (! (new CheckInModel())->updateForOwner(
            $checkInId,
            $ownerId,
            $input['workload_rating'],
            $input['energy_rating'],
        )) {
            return redirect()->to(site_url($returnPath))->withInput()->with('error', 'The check-in could not be updated. Please try again.');
        }

        return redirect()->to(site_url('student/check-ins'))->with('success', 'Private check-in updated.');
    }

    public function delete(int $checkInId): string
    {
        $checkIn = (new CheckInModel())->findForOwner($checkInId, $this->ownerId());
        if ($checkIn === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('student/check-ins/delete', [
            'checkIn' => $checkIn,
            'pageTitle' => 'Delete private check-in',
        ]);
    }

    public function confirmDelete(int $checkInId): RedirectResponse
    {
        $ownerId = $this->ownerId();
        if ((new CheckInModel())->findForOwner($checkInId, $ownerId) === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getPost('confirm_delete') !== '1') {
            return redirect()->to(site_url('student/check-ins'))->with('error', 'Confirm deletion to remove the check-in.');
        }
        if (! (new CheckInModel())->deleteForOwner($checkInId, $ownerId)) {
            return redirect()->to(site_url('student/check-ins'))->with('error', 'The check-in could not be deleted.');
        }

        return redirect()->to(site_url('student/check-ins'))->with('success', 'Private check-in deleted.');
    }

    /** @return array{workload_rating: int, energy_rating: int}|RedirectResponse */
    private function validRatingInput(string $returnPath): array|RedirectResponse
    {
        $workloadRating = $this->request->getPost('workload_rating');
        $energyRating = $this->request->getPost('energy_rating');
        $data = [
            'workload_rating' => is_string($workloadRating) ? $workloadRating : '',
            'energy_rating' => is_string($energyRating) ? $energyRating : '',
        ];
        $rules = [
            'workload_rating' => 'required|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[5]',
            'energy_rating' => 'required|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[5]',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url($returnPath))->withInput()->with('errors', $this->validator->getErrors());
        }

        return [
            'workload_rating' => (int) $data['workload_rating'],
            'energy_rating' => (int) $data['energy_rating'],
        ];
    }

    private function ownerId(): int
    {
        $user = auth()->user();
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $user->id;
    }

    private function now(): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);

        return (new DateTimeImmutable('now', $timezone))->format('Y-m-d H:i:s');
    }
}

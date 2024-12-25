<?php

namespace App\Console\Commands;

use App\Models\Vacancy;
use Illuminate\Console\Command;

class UpdateVacancyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacancy:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vacancies = Vacancy::where('status', true)->get();

        foreach ($vacancies as $vacancy) {
            if ($vacancy->isLimitReached()) {
                $vacancy->update(['status' => false]);

                $vacancy->rejectRemainingApplicants();
            }
        }

        $this->info('Status lowongan berhasil diperbarui.');
    }
}

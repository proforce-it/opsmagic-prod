<?php

namespace App\Console\Commands;

use App\Models\Job\ClientJobPayRate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePayRateStatus extends Command
{
    protected $signature = 'payrate:update-status';
    protected $description = 'Update pay rate status based on pay_rate_valid_from date';

    public function handle()
    {
        DB::beginTransaction();
        try {
            $upcomingRates = ClientJobPayRate::query()
                ->where('status', 'U')
                ->where('pay_rate_valid_from', Carbon::today()->format('Y-m-d'))
                ->get();

            if ($upcomingRates->isEmpty()) {
                $this->info('No pay rate changes required.');
                DB::commit();
                return;
            }

            foreach ($upcomingRates as $rate) {
                $jobId = $rate->job_id;

                ClientJobPayRate::query()
                    ->where('job_id', $jobId)
                    ->where('status', 'C')
                    ->update([
                        'status' => 'P',
                        'pay_rate_valid_to' => Carbon::today()->subDay()->format('Y-m-d'),
                    ]);

                $rate->update(['status' => 'C']);
            }

            DB::commit();
            $this->info('Pay rate script successfully called.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
        }
    }
}

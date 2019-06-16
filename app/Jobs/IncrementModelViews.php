<?php namespace App\Jobs;

use App\Person;
use App\Title;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Session\Store;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IncrementModelViews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Person|Title
     */
    private $model;

    /**
     * Create a new command instance.
     *
     * @param Person|Title $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the console command.
     *
     * @param Store $session
     * @return void
     */
    public function handle(Store $session)
    {
        if ( ! $this->shouldIncrement($session)) return;

        $session->put("{$this->model->type}-views.{$this->model->id}", Carbon::now()->timestamp);

        $this->incrementViews();
    }

    /**
     * Check if model views should be incremented.
     *
     * @param Store $session
     * @return boolean
     */
    private function shouldIncrement(Store $session)
    {
        $views = $session->get("{$this->model->type}-views");

        //user has not viewed this model yet
        if ( ! $views || ! isset($views[$this->model->id])) return true;

        //see if user last viewed this model over 10 hours ago
        $time = Carbon::createFromTimestamp($views[$this->model->id]);

        return Carbon::now()->diffInHours($time) > 10;
    }

    /**
     * Increment views or plays of specified model.
     */
    private function incrementViews()
    {
        $this->model->increment('views');
    }
}

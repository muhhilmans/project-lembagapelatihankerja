<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled blog posts that have reached their publish date/time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Running blog:publish-scheduled command at " . now());

        $blogs = Blog::where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->get();

        $count = $blogs->count();

        foreach ($blogs as $blog) {
            $blog->update(['status' => 'published']);
            $this->info("Published: {$blog->title}");
            Log::info("Published blog: {$blog->title} (ID: {$blog->id})");
        }

        if ($count > 0) {
            $this->info("Total {$count} blog(s) published.");
            Log::info("Total {$count} blog(s) published successfully.");
        } else {
            $this->info("No scheduled blogs to publish.");
        }

        return Command::SUCCESS;
    }
}

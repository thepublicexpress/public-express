protected $listen = [
    // ... existing listeners ...
    
    \App\Events\NewsSubmitted::class => [
        \App\Listeners\SendNewsSubmittedNotificationToAdmin::class,
    ],
    \App\Events\NewsApproved::class => [
        \App\Listeners\SendNewsApprovedNotificationToReporter::class,
    ],
    \App\Events\NewsRejected::class => [
        \App\Listeners\SendNewsRejectedNotificationToReporter::class,
    ],
];
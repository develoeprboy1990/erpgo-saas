protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\CheckTenant::class,
        // other middleware...
    ],
];

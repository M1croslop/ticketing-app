<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$agent = App\Models\User::where('role', 'agent')->first();
$ticket = App\Models\Ticket::first();

auth()->login($agent);

$request = App\Http\Requests\StoreCommentRequest::create("/tickets/{$ticket->id}/comments", 'POST', ['body' => 'Test comment from agent']);
$request->setContainer(app())->setRedirector(app(\Illuminate\Routing\Redirector::class));
$request->validateResolved();

$controller = new App\Http\Controllers\CommentController();
$response = $controller->store($request, $ticket);

dump($ticket->comments()->get()->toArray());

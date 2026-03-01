Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API working'
    ]);
});
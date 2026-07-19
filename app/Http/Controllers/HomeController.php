<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke()
    {
        $carouselDirectory = public_path('assets/images/carousel');
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

        $carouselSlides = collect(File::isDirectory($carouselDirectory) ? File::files($carouselDirectory) : [])
            ->filter(fn ($file) => in_array(Str::lower($file->getExtension()), $allowedExtensions, true))
            ->sortBy(fn ($file) => $file->getFilename(), SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn ($file) => [
                'url' => asset('assets/images/carousel/'.$file->getFilename()),
                'alt' => Str::headline(pathinfo($file->getFilename(), PATHINFO_FILENAME)),
            ])
            ->values();

        return view('home', [
            'carouselSlides' => $carouselSlides,
        ]);
    }
}

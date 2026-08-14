<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    private const PAGE_META = [
        'services' => [
            'title' => 'Services',
            'eyebrow' => '// Services',
            'headline' => 'Software services built around real business operations.',
            'summary' => 'From product strategy and process mapping to secure cloud deployment, Navkwa helps organisations move from fragmented workflows to reliable digital systems.',
            'signal_copy' => 'Strategy, engineering, deployment, and support shaped around how your organisation already works.',
            'signals' => ['Product strategy', 'Secure engineering', 'Cloud delivery'],
        ],
        'products' => [
            'title' => 'Products',
            'eyebrow' => '// Products',
            'headline' => 'Focused products for industries where operational systems matter.',
            'summary' => 'Navkwa builds products that solve practical problems in African markets, beginning with construction, field services, and internal business operations.',
            'signal_copy' => 'Commercial products and internal platforms designed for measurable operational control.',
            'signals' => ['Navkwa Build', 'FixAm', 'Operations Portal'],
        ],
        'industries' => [
            'title' => 'Industries',
            'eyebrow' => '// Industries',
            'headline' => 'Industry knowledge changes how software should be built.',
            'summary' => 'We design systems for sectors where visibility, approvals, cost control, records, logistics, and reporting directly affect performance.',
            'signal_copy' => 'Sector-aware design for teams that need reliable systems, not generic templates.',
            'signals' => ['Construction', 'Healthcare', 'Logistics'],
        ],
        'work' => [
            'title' => 'Work',
            'eyebrow' => '// Work',
            'headline' => 'Products and systems we are building with transparency.',
            'summary' => 'Until public case studies are available, Navkwa shows active products and internal systems honestly instead of presenting illustrative work as delivered client projects.',
            'signal_copy' => 'A clear view of active products, internal systems, and the case studies that will follow.',
            'signals' => ['Active products', 'Internal systems', 'Future case studies'],
        ],
        'about' => [
            'title' => 'About',
            'eyebrow' => '// Company',
            'headline' => 'A Ghanaian technology company building practical systems for African businesses.',
            'summary' => 'Navkwa Group Ltd. combines product strategy, engineering discipline, and operational understanding to turn complex processes into dependable software.',
            'signal_copy' => 'Ghana-based, Africa-focused, and built around continuity, ownership, and disciplined delivery.',
            'signals' => ['Ghana-based', 'Africa-focused', 'Secure handover'],
        ],
        'contact' => [
            'title' => 'Contact',
            'eyebrow' => '// Contact',
            'headline' => 'Start a conversation with Navkwa.',
            'summary' => 'Tell us what you are building, improving, replacing, or automating. We review enquiries and respond within one business day.',
            'signal_copy' => 'Share the business problem, expected outcome, and timeline so we can frame the next step clearly.',
            'signals' => ['Discovery', 'Scope clarity', 'Business response'],
        ],
    ];

    public function __invoke()
    {
        return view('home', [
            'carouselSlides' => $this->carouselSlides(),
            'siteSettings' => AdminSetting::query()->pluck('value', 'key'),
        ]);
    }

    private function carouselSlides()
    {
        $carouselDirectory = public_path('assets/images/carousel');
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

        return collect(File::isDirectory($carouselDirectory) ? File::files($carouselDirectory) : [])
            ->filter(fn ($file) => in_array(Str::lower($file->getExtension()), $allowedExtensions, true))
            ->sortBy(fn ($file) => $file->getFilename(), SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn ($file) => [
                'url' => asset('assets/images/carousel/'.$file->getFilename()),
                'alt' => 'Navkwa software team collaborating in a modern workspace',
            ])
            ->values();
    }

    public function services()
    {
        return $this->sitePage('services');
    }

    public function products()
    {
        return $this->sitePage('products');
    }

    public function industries()
    {
        return $this->sitePage('industries');
    }

    public function work()
    {
        return $this->sitePage('work');
    }

    public function about()
    {
        return $this->sitePage('about');
    }

    public function contact()
    {
        return $this->sitePage('contact');
    }

    public function navkwaBuild()
    {
        return view('products.navkwa-build', [
            'siteSettings' => AdminSetting::query()->pluck('value', 'key'),
            'navkwaBuildPlans' => config('navkwa_build.plans'),
            'navkwaBuildAnnualBillableMonths' => config('navkwa_build.annual_billable_months', 10),
            'navkwaBuildCurrency' => config('navkwa_build.currency', 'GHS'),
        ]);
    }

    private function sitePage(string $page)
    {
        abort_unless(isset(self::PAGE_META[$page]), 404);

        return view('pages.site-page', [
            'activePage' => $page,
            'page' => ['slug' => $page, ...self::PAGE_META[$page]],
            'carouselSlides' => $this->carouselSlides(),
            'siteSettings' => AdminSetting::query()->pluck('value', 'key'),
        ]);
    }
}

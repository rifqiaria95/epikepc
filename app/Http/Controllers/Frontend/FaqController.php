<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'What services does EPIK provide?',
                'answer' => 'We provide EPC, operation & maintenance, agencies & trading, logistic transportation, and investment-based EPCIC services for oil & gas infrastructure across Indonesia.',
            ],
            [
                'question' => 'How do I start a project consultation?',
                'answer' => 'Contact our team through the contact page or submit a consultation request. We will respond within 1–2 business days.',
            ],
            [
                'question' => 'Does EPIK operate projects nationwide?',
                'answer' => 'Yes. We have delivered projects across multiple regions in Indonesia for clients such as PGN, Pertamina, and the Ministry of Energy and Mineral Resources.',
            ],
            [
                'question' => 'How long does a typical project take?',
                'answer' => 'Project duration depends on scope and complexity. A detailed schedule is provided after site survey and requirement analysis.',
            ],
            [
                'question' => 'Do you provide after-sales support?',
                'answer' => 'Yes. We provide operation, maintenance, and monitoring services to ensure long-term asset reliability after project completion.',
            ],
        ];

        return view('frontend.faq.index', compact('faqs'));
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceFaq;
use App\Models\ServiceFeature;
use App\Models\ServiceType;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSeederMedia;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use CopiesSeederMedia;

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('ServiceSeeder skipped: no users found.');

            return;
        }

        $services = [
            [
                'type_slug' => 'epc-services',
                'title' => 'EPC Services',
                'subtitle' => 'Integrated Engineering, Procurement, and Construction for oil & gas infrastructure.',
                'description' => '<p>PT EPIK provides comprehensive EPC services covering gas compressor packages, pipeline & piping, pressure vessels and tanks, steel structure fabrication and erection, mechanical work, electrical & instrument work, painting, fireproofing, insulation, HDD & auger services, onshore civil & building works, telecommunications infrastructure, natural gas/CNG/LNG facilities, and SO2 & CO2 removal.</p><p>Our EPCIC capability supports large-scale construction with disciplined execution from design through commissioning.</p>',
                'image' => 'image19.png',
                'features' => [
                    'Gas compressor package',
                    'Pipeline & piping for oil & gas',
                    'Pressure vessel and tank',
                    'Steel structure fabrication & erection',
                    'Mechanical, electrical & instrument work',
                    'Painting, fireproofing & insulation',
                    'HDD & auger services',
                    'Onshore civil & building with piling',
                    'Natural gas, CNG & LNG facilities',
                    'R&D high-end intelligent oil & gas equipment',
                ],
                'faqs' => [
                    ['question' => 'What industries does EPIK EPC serve?', 'answer' => 'We focus on upstream and downstream oil & gas infrastructure, including pipeline networks, metering stations, and LNG-related facilities.'],
                    ['question' => 'Does EPIK provide EPCIC services?', 'answer' => 'Yes. We deliver Engineering, Procurement, Construction, Installation, and Commissioning for large-scale energy projects.'],
                ],
                'details' => [
                    ['title' => 'Engineering & Design', 'subtitle' => 'Front-end engineering support', 'price' => 0, 'description' => 'Technical planning, material specification, and constructability review for pipeline and facility projects.'],
                    ['title' => 'Procurement', 'subtitle' => 'Material & equipment supply', 'price' => 0, 'description' => 'Procurement of pipes, valves, instruments, and supporting materials aligned with project specifications.'],
                    ['title' => 'Construction & Commissioning', 'subtitle' => 'Site execution to handover', 'price' => 0, 'description' => 'Field construction, testing, and commissioning for safe and reliable operation.'],
                ],
            ],
            [
                'type_slug' => 'operation-maintenance',
                'title' => 'Operation & Maintenance',
                'subtitle' => 'Comprehensive O&M for gas distribution pipelines and supporting facilities.',
                'description' => '<p>We provide comprehensive services in the operation and maintenance of distribution pipelines and their facilities, including pipeline network maintenance and monitoring, inspection, cathodic protection systems, handling and gas stations, and operational disruption response.</p><p>Our scope covers ORFs, offshore and nearshore pipelines, mooring system supervision, monthly O&M reporting, and prompt emergency response.</p>',
                'image' => 'image27.jpeg',
                'features' => [
                    'Pipeline network maintenance & monitoring',
                    'Inspection & cathodic protection',
                    'Gas station operation support',
                    'ORF and pipeline asset maintenance',
                    'Valve repair & spare part replacement',
                    'Monthly O&M reporting',
                    'Emergency & unplanned work response',
                ],
                'faqs' => [
                    ['question' => 'What assets are covered in O&M services?', 'answer' => 'Distribution pipelines, onshore receiving facilities, gas stations, valves, cathodic protection systems, and related supporting assets.'],
                ],
                'details' => [
                    ['title' => 'Routine Maintenance', 'subtitle' => 'Scheduled pipeline care', 'price' => 0, 'description' => 'Preventive maintenance, inspection, and reporting to maintain availability and integrity.'],
                    ['title' => 'Corrective Maintenance', 'subtitle' => 'Repair & restoration', 'price' => 0, 'description' => 'Valve painting, spare part replacement, and facility repair works.'],
                ],
            ],
            [
                'type_slug' => 'agencies-trading',
                'title' => 'Agencies & Trading',
                'subtitle' => 'Agency cooperation for gas-related equipment and facilities.',
                'description' => '<p>EPIK engages in supplying various equipment and goods as a general supplier and agency partner for gas-related equipment and facilities.</p><p>Our portfolio includes diaphragm gas meters, pipes through Rucika partnership, valves & fittings, steam turbines, seal & dosing pumps, compressors, and gensets.</p>',
                'image' => 'image28.jpeg',
                'features' => [
                    'Diaphragm gas meter G1.6 (NUSA METER)',
                    'Rucika pipe products (MDPE, HDPE, uPVC, PPR)',
                    'Valve & fitting supply',
                    'Steam turbine & generator',
                    'Seal pump & dosing pump (Milton Roy)',
                    'Gas compressor & air compressor',
                    'Genset supply',
                ],
                'faqs' => [
                    ['question' => 'Is the diaphragm gas meter certified?', 'answer' => 'Yes. EPIK household diaphragm gas meters are registered with the Indonesian Ministry of Trade and passed the Directorate of Metrology test with the NUSA METER label.'],
                ],
                'details' => [
                    ['title' => 'Equipment Supply', 'subtitle' => 'Certified gas equipment', 'price' => 0, 'description' => 'Supply of meters, valves, compressors, and supporting gas facility equipment.'],
                ],
            ],
            [
                'type_slug' => 'logistic-transportation',
                'title' => 'Logistic Transportation',
                'subtitle' => 'Onshore, offshore CNG/LNG, and hazardous waste transportation.',
                'description' => '<p>PT EPIK provides logistic transportation services including onshore, offshore CNG and LNG transportation, and hazardous waste transportation for industrial and energy sector clients.</p>',
                'image' => 'image29.jpeg',
                'features' => [
                    'Onshore transportation',
                    'Offshore CNG transportation',
                    'Offshore LNG transportation',
                    'Hazardous waste transportation',
                ],
                'faqs' => [],
                'details' => [
                    ['title' => 'Energy Logistics', 'subtitle' => 'CNG & LNG mobility', 'price' => 0, 'description' => 'Transportation solutions for compressed and liquefied natural gas logistics requirements.'],
                ],
            ],
            [
                'type_slug' => 'epc-services',
                'title' => 'Mechanical Erection & Steel Structure',
                'subtitle' => 'Expert installation of mechanical equipment and steel structures for oil & gas.',
                'description' => '<p>EPIK has an experienced team in the installation of mechanical equipment such as motors, pumps, tanks, and conveyor systems with on-time and budget-efficient project management.</p><p>We also deliver steel structure projects in the oil & gas industry with quality, speed, and accuracy aligned to industry standards.</p>',
                'image' => 'image30.jpeg',
                'features' => ['Mechanical equipment erection', 'Steel structure fabrication', 'Oil & gas structural projects', 'On-time project delivery', 'Quality & safety compliance'],
                'faqs' => [],
                'details' => [],
            ],
            [
                'type_slug' => 'epc-services',
                'title' => 'Coating Services',
                'subtitle' => 'Corrosion protection for upstream, downstream, and industrial assets.',
                'description' => '<p>We provide engineering services and solutions to protect client assets from corrosion across upstream and downstream plants, tank terminals, mining, oil & gas infrastructure, ports & docks, and other industries.</p><p>Services include fireproofing, internal lining, floor coatings, protective coatings, high temperature coatings, wrapping, and cathodic protection.</p>',
                'image' => 'image31.png',
                'features' => ['Fireproofing', 'Internal lining', 'Protective & high temp coatings', 'Wrapping & cathodic protection', 'Floor coating systems'],
                'faqs' => [],
                'details' => [],
            ],
            [
                'type_slug' => 'epc-services',
                'title' => 'Pipeline & Piping Installation',
                'subtitle' => 'High-quality pipeline construction with experienced welding teams.',
                'description' => '<p>EPIK has highly experienced and disciplined welders delivering high productivity and quality pipeline construction for distribution and interplant pipelines.</p><p>We also provide BOT and BOO scheme offerings to significantly reduce initial capital for clients.</p>',
                'image' => 'image32.jpeg',
                'features' => ['Onshore & offshore pipeline', 'Gas metering station', 'Gas receiving station', 'Distribution & interplant piping', 'BOT & BOO scheme offerings'],
                'faqs' => [],
                'details' => [],
            ],
            [
                'type_slug' => 'epc-services',
                'title' => 'HDD & Auger Services',
                'subtitle' => 'Trenchless pipeline installation under ground obstacles.',
                'description' => '<p>Horizontal Directional Drilling (HDD) is a culvert-less technique for installing pipes under ground obstacles through pilot hole drilling, hole enlargement, pipe installation, and backfilling.</p><p>Advantages include minimal environmental disruption and the ability to cross soil obstacles efficiently.</p>',
                'image' => 'image33.png',
                'features' => ['Horizontal directional drilling', 'Auger boring services', 'Minimal environmental disruption', 'Crossing soil obstacles', 'Urban & river crossing capability'],
                'faqs' => [],
                'details' => [],
            ],
            [
                'type_slug' => 'investment-capital',
                'title' => 'Investment & Capital EPC',
                'subtitle' => 'EPCIC services supported by BOO, BOT, tolling fee, rental, and equity capital schemes.',
                'description' => '<p>EPIK provides capital management services and investment-based EPCIC delivery through BOO, BOT, tolling fee, rental scheme, and equity capital models for large-scale energy infrastructure projects.</p>',
                'image' => 'image34.jpeg',
                'features' => ['BOO & BOT schemes', 'Tolling fee models', 'Rental scheme options', 'Equity capital partnerships', 'Investment-based EPCIC'],
                'faqs' => [],
                'details' => [],
            ],
        ];

        $seededTitles = [];

        foreach ($services as $serviceData) {
            $serviceType = ServiceType::query()->where('slug', $serviceData['type_slug'])->first();

            if (! $serviceType) {
                $this->command?->warn("Service type not found: {$serviceData['type_slug']}");

                continue;
            }

            $image = $this->copyComproImageToStorage($serviceData['image'], 'services');

            $service = Service::updateOrCreate(
                ['title' => $serviceData['title'], 'service_type_id' => $serviceType->id],
                [
                    'subtitle'    => $serviceData['subtitle'],
                    'description' => $serviceData['description'],
                    'image'       => $image,
                    'created_by'  => $userId,
                    'updated_by'  => $userId,
                ]
            );

            $seededTitles[] = $service->title;

            ServiceFeature::query()->where('service_id', $service->id)->delete();
            foreach ($serviceData['features'] as $index => $feature) {
                ServiceFeature::create([
                    'service_id' => $service->id,
                    'feature'    => $feature,
                    'sort_order' => $index,
                ]);
            }

            ServiceFaq::query()->where('service_id', $service->id)->delete();
            foreach ($serviceData['faqs'] as $index => $faq) {
                ServiceFaq::create([
                    'service_id' => $service->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'],
                    'sort_order' => $index,
                ]);
            }

            ServiceDetail::withTrashed()->where('service_id', $service->id)->forceDelete();
            foreach ($serviceData['details'] as $detail) {
                ServiceDetail::create([
                    'service_id'  => $service->id,
                    'title'       => $detail['title'],
                    'subtitle'    => $detail['subtitle'],
                    'price'       => $detail['price'],
                    'description' => $detail['description'],
                    'created_by'  => $userId,
                    'updated_by'  => $userId,
                ]);
            }
        }

        Service::query()->whereNotIn('title', $seededTitles)->delete();

        $this->command?->info('EPIK services seeded successfully from company profile deck!');
    }
}

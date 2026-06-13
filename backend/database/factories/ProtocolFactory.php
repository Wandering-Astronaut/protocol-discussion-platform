<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProtocolFactory extends Factory
{
    private static array $protocols = [
        [
            'title'      => '30-Day Gut Reset Protocol',
            'category'   => 'nutrition',
            'difficulty' => 'intermediate',
            'duration'   => '30 days',
            'tags'       => ['gut-health', 'nutrition', 'detox', 'microbiome'],
            'content'    => "## Overview\nThis protocol is designed to reset and restore optimal gut health through a structured elimination and reintroduction approach.\n\n## Week 1: Elimination Phase\nRemove all processed foods, gluten, dairy, and refined sugars. Focus on whole foods: vegetables, quality proteins, healthy fats.\n\n## Week 2: Healing Foods\nIntroduce bone broth, fermented vegetables (sauerkraut, kimchi), and prebiotic-rich foods like Jerusalem artichoke and green banana.\n\n## Week 3: Probiotic Support\nBegin probiotic supplementation. Start with 10 billion CFU and increase gradually. Continue fermented foods daily.\n\n## Week 4: Reintroduction\nSlowly reintroduce eliminated foods one at a time, noting any reactions. Keep a detailed food journal.\n\n## Key Supplements\n- L-Glutamine: 5g daily\n- Digestive enzymes with meals\n- Magnesium glycinate: 400mg at bedtime\n\n## Lifestyle Factors\nPrioritize 8 hours of sleep. Manage stress through daily meditation or breathwork. Avoid NSAIDs during the protocol.",
        ],
        [
            'title'      => 'Circadian Rhythm Optimization Protocol',
            'category'   => 'sleep',
            'difficulty' => 'beginner',
            'duration'   => '21 days',
            'tags'       => ['sleep', 'circadian-rhythm', 'light-therapy', 'hormones'],
            'content'    => "## Overview\nAlign your biology with natural light-dark cycles to optimize sleep, hormones, and metabolic health.\n\n## Morning Routine\n- Wake at consistent time (ideally 6-7am)\n- Get 10 minutes of sunlight within 30 minutes of waking\n- No blue light screens for first 30 minutes\n- Cold water splash on face to signal cortisol awakening response\n\n## Daytime Protocol\n- Eat meals within a 10-12 hour window\n- Avoid bright overhead lighting after sunset\n- Exercise before 3pm for best circadian benefit\n- Use blue light blocking glasses from 7pm\n\n## Evening Wind-Down\n- Dim lights 2 hours before bed\n- Lower home temperature to 65-68°F\n- Magnesium glycinate 400mg, 1 hour before sleep\n- No screens 1 hour before bed\n\n## Tracking\nUse a sleep tracker (Oura, WHOOP, or Garmin) to monitor HRV, deep sleep, and REM cycles.",
        ],
        [
            'title'      => 'Dopamine Detox & Reset Protocol',
            'category'   => 'mental-health',
            'difficulty' => 'advanced',
            'duration'   => '14 days',
            'tags'       => ['dopamine', 'mental-health', 'focus', 'addiction', 'productivity'],
            'content'    => "## Overview\nA structured approach to resetting dopamine sensitivity and breaking compulsive digital habits.\n\n## Phase 1: Assessment (Days 1-2)\nAudit all dopamine triggers: social media, news, sugar, alcohol, pornography, video games. Identify your top 3-5 compulsive behaviors.\n\n## Phase 2: Hard Reset (Days 3-7)\n- Remove all social media apps from phone\n- No entertainment media (streaming, YouTube)\n- No processed sugar or alcohol\n- Replace with: long walks in nature, journaling, reading physical books, conversation\n\n## Phase 3: Rebuilding (Days 8-14)\n- Introduce healthy dopamine sources: cold exposure, exercise, creative work\n- Gradually reintroduce necessary technology with strict time limits\n- Establish phone-free zones (bedroom, dining table)\n\n## Supplements\n- Tyrosine: 500mg morning\n- Mucuna Pruriens: 300mg\n- Rhodiola: 200mg\n\n## Mindfulness Practice\nDaily 20-minute meditation. Use Wim Hof breathing protocol each morning.",
        ],
        [
            'title'      => 'Nervous System Regulation Protocol',
            'category'   => 'mental-health',
            'difficulty' => 'beginner',
            'duration'   => '8 weeks',
            'tags'       => ['nervous-system', 'stress', 'vagus-nerve', 'breathwork', 'trauma'],
            'content'    => "## Overview\nA somatic-based protocol to shift from chronic sympathetic dominance (fight/flight) to parasympathetic balance (rest/digest).\n\n## Core Practices\n\n### 1. Physiological Sigh (3x daily)\nDouble inhale through nose, long exhale through mouth. This is the fastest way to reduce acute stress.\n\n### 2. Box Breathing (Morning & Evening)\n4 counts in – 4 hold – 4 out – 4 hold. 5 minutes minimum.\n\n### 3. Cold Exposure\nStart with 30-second cold finish in shower. Progress to 2-minute cold showers. Activates vagal tone.\n\n### 4. Humming & Gargling\n5 minutes of humming or gargling warm water daily. Directly stimulates vagus nerve.\n\n### 5. NSDR (Non-Sleep Deep Rest)\n20-minute Yoga Nidra script daily. Evidence-based from Stanford research.\n\n## Weekly Bodywork\nMassage, acupuncture, or craniosacral therapy weekly if accessible.\n\n## Nutrition for Nervous System\n- Omega-3 EPA/DHA: 2g daily\n- Ashwagandha KSM-66: 300mg\n- GABA: 750mg before bed",
        ],
        [
            'title'      => 'Metabolic Health Reset (Insulin Sensitivity)',
            'category'   => 'nutrition',
            'difficulty' => 'intermediate',
            'duration'   => '60 days',
            'tags'       => ['metabolic-health', 'insulin', 'blood-sugar', 'fasting', 'nutrition'],
            'content'    => "## Overview\nRestore insulin sensitivity, reduce visceral fat, and optimize metabolic markers through dietary and lifestyle intervention.\n\n## Dietary Framework\n\n### Eating Window\nStart with 12:12 intermittent fasting. Progress to 16:8 after week 2.\n\n### Food Priorities\n- Protein: 1g per pound lean body mass\n- Fiber: 30g+ daily (vegetables, legumes, psyllium)\n- Fats: Olive oil, avocado, nuts, fatty fish\n- Eliminate: seed oils, refined carbs, liquid calories\n\n### Post-Meal Walks\n10-minute walk after each meal – clinically shown to blunt glucose spikes by 30%.\n\n## Exercise Protocol\n- Zone 2 cardio: 150 min/week (conversational pace)\n- Strength training: 3x/week (builds glucose-absorbing muscle)\n- HIIT: 1x/week\n\n## Monitoring\nContinuous glucose monitor (Levels, Nutrisense) for real-time feedback. Target: fasting glucose <90, postprandial peaks <140.\n\n## Supplements\n- Berberine: 500mg with meals\n- Alpha Lipoic Acid: 300mg\n- Chromium: 200mcg",
        ],
        [
            'title'      => 'Anti-Inflammatory Healing Protocol',
            'category'   => 'nutrition',
            'difficulty' => 'beginner',
            'duration'   => '30 days',
            'tags'       => ['inflammation', 'autoimmune', 'nutrition', 'healing'],
            'content'    => "## Overview\nReduce systemic inflammation through evidence-based dietary, supplement, and lifestyle interventions.\n\n## Core Anti-Inflammatory Foods\n- Fatty fish (salmon, sardines, mackerel): 3-4x weekly\n- Extra virgin olive oil: 3 tablespoons daily\n- Turmeric with black pepper: 1 tsp daily\n- Leafy greens: 2 cups daily\n- Berries: 1 cup daily\n- Ginger: fresh or powdered daily\n\n## Foods to Eliminate\n- All refined vegetable/seed oils\n- Sugar and high-fructose corn syrup\n- Refined grains\n- Processed meats\n- Alcohol (at least first 30 days)\n\n## Supplement Stack\n- Fish oil EPA/DHA: 3g daily\n- Curcumin (BCM-95): 500mg twice daily\n- Vitamin D3: 5000IU + K2\n- Quercetin: 500mg\n- NAC: 600mg\n\n## Lifestyle\n- Sleep 8+ hours\n- Stress management (HRV training)\n- Sauna: 3x weekly if accessible",
        ],
        [
            'title'      => 'Testosterone Optimization Protocol (Men)',
            'category'   => 'hormones',
            'difficulty' => 'intermediate',
            'duration'   => '90 days',
            'tags'       => ['testosterone', 'hormones', 'men-health', 'strength', 'vitality'],
            'content'    => "## Overview\nNatural protocol to optimize testosterone through training, sleep, nutrition, and targeted supplementation.\n\n## Foundational Pillars\n\n### Sleep (Most Critical)\n- 8-9 hours, consistent schedule\n- 70% of testosterone produced during deep sleep\n- Cool, dark room (65-67°F)\n- No alcohol before bed\n\n### Strength Training\n- Heavy compound lifts: squat, deadlift, bench, rows\n- 3-4x per week, 45-60 min sessions\n- Prioritize progressive overload\n- Avoid overtraining (cortisol suppresses T)\n\n### Nutrition\n- Don't fear dietary fat (cholesterol = T precursor)\n- Zinc-rich foods: oysters, red meat, pumpkin seeds\n- Cruciferous vegetables for estrogen clearance\n- Maintain healthy body fat (15-20%)\n\n### Stress Management\nChronic cortisol is enemy #1 of testosterone. Daily breathwork, nature exposure, limit news consumption.\n\n## Supplements\n- Zinc: 30mg (if deficient)\n- Vitamin D3: 5000IU\n- Ashwagandha KSM-66: 600mg\n- Tongkat Ali: 200mg standardized extract\n- Boron: 10mg",
        ],
        [
            'title'      => 'Cold & Heat Therapy Protocol',
            'category'   => 'recovery',
            'difficulty' => 'intermediate',
            'duration'   => 'Ongoing',
            'tags'       => ['cold-therapy', 'sauna', 'recovery', 'hormesis', 'longevity'],
            'content'    => "## Overview\nContrast therapy using cold immersion and heat exposure to enhance recovery, resilience, and longevity.\n\n## Cold Protocol\n\n### Beginner (Weeks 1-2)\nCold shower finishes: 30 seconds at lowest temperature.\n\n### Intermediate (Weeks 3-6)\nCold plunge or ice bath at 50-55°F. Start with 2 minutes, build to 5-10 minutes.\n\n### Advanced\nDaily cold immersion at 45-50°F for 5-11 minutes (weekly total 11+ minutes per Huberman protocol).\n\n### Cold Timing\n- Morning: Activates cortisol, norepinephrine, alertness\n- Post-workout: AVOID if goal is muscle hypertrophy (blunts adaptation)\n- Best: standalone 3-6 hours before/after training\n\n## Heat Protocol\n\n### Sauna (Finnish, Dry)\n- 174°F (79°C), 3-4x per week\n- 20 minutes per session, 2-3 rounds\n- Associated with 40% reduction in all-cause mortality (Finnish cohort study)\n- Growth hormone surge: 2-3 rounds with cooling between\n\n### Contrast Protocol\nAlternate: 15 min sauna → 2 min cold → 10 min sauna → 2 min cold. Powerful vasodilation/constriction training.",
        ],
        [
            'title'      => 'Cognitive Performance Stack Protocol',
            'category'   => 'nootropics',
            'difficulty' => 'advanced',
            'duration'   => 'Ongoing',
            'tags'       => ['nootropics', 'focus', 'cognitive', 'brain-health', 'BDNF'],
            'content'    => "## Overview\nEvidence-based cognitive enhancement through lifestyle, nutrition, and targeted supplementation. Emphasizes long-term brain health over short-term stimulant effects.\n\n## Lifestyle Foundation (Non-Negotiable)\n- Exercise: Most powerful nootropic. 30 min Zone 2 + 2x strength per week\n- Sleep: 8 hours – without this, no supplement matters\n- Sunlight: Morning light regulates cortisol, dopamine, serotonin\n\n## Morning Stack\n- Lion's Mane: 1000mg (NGF/BDNF support)\n- Bacopa Monnieri: 300mg (memory consolidation)\n- Alpha-GPC: 300mg (acetylcholine precursor)\n- L-Theanine: 200mg + Caffeine 100mg (focus without anxiety)\n\n## Afternoon Stack\n- Rhodiola Rosea: 200mg (adaptogen, anti-fatigue)\n- Citicoline: 250mg\n\n## Evening (Recovery & Consolidation)\n- Magnesium L-Threonate: 1500mg (crosses BBB, sleep + memory)\n- Ashwagandha: 300mg (cortisol reduction)\n\n## Weekly Practices\n- Learning something new (neuroplasticity)\n- Social connection (BDNF boost)\n- Nature walks (restorative attention)",
        ],
        [
            'title'      => 'Detox & Liver Support Protocol',
            'category'   => 'detox',
            'difficulty' => 'beginner',
            'duration'   => '21 days',
            'tags'       => ['detox', 'liver', 'phase-2-detox', 'heavy-metals', 'toxins'],
            'content'    => "## Overview\nSupport Phase 1 and Phase 2 liver detoxification pathways through targeted nutrition and supplementation.\n\n## Phase 1 Support (Oxidation)\nThese nutrients activate cytochrome P450 enzymes:\n- B vitamins (especially B2, B3, B6, B12)\n- Vitamin C: 2g daily\n- Glutathione precursors: NAC 600mg\n\n## Phase 2 Support (Conjugation)\nEssential for completing detox and excreting toxins safely:\n- Cruciferous vegetables: broccoli sprouts (highest sulforaphane)\n- DIM (diindolylmethane): 200mg\n- Milk Thistle (silymarin): 400mg\n- Calcium D-Glucarate: 500mg\n\n## Elimination Pathways\nEnsure all exit routes are open:\n- Bowel: 1-2 movements daily (fiber, magnesium)\n- Sweat: Exercise + sauna\n- Urine: 2-3L water daily\n- Lymph: Dry brushing, rebounding, deep breathing\n\n## Heavy Metal Considerations\nFor heavy metal detox, work with practitioner. Chlorella and modified citrus pectin show evidence for gentle chelation.",
        ],
        [
            'title'      => 'Anxiety & Stress Resilience Protocol',
            'category'   => 'mental-health',
            'difficulty' => 'beginner',
            'duration'   => '6 weeks',
            'tags'       => ['anxiety', 'stress', 'resilience', 'GABA', 'adaptogens'],
            'content'    => "## Overview\nMulti-faceted protocol addressing the root causes of anxiety: dysregulated nervous system, nutritional deficiencies, and maladaptive thought patterns.\n\n## Immediate Tools (Use Anytime)\n\n### Physiological Sigh\nDouble inhale (nose) + long exhale (mouth). Fastest way to reduce acute anxiety.\n\n### 4-7-8 Breathing\nInhale 4s → Hold 7s → Exhale 8s. Activates parasympathetic system.\n\n### Cold Water on Wrists/Face\nActivates dive reflex, immediate vagal response.\n\n## Daily Practices\n- Morning: 10 min meditation (Headspace, Insight Timer)\n- Exercise: 30+ min moderate intensity (most powerful anxiolytic)\n- Journaling: Morning pages, 3 pages stream-of-consciousness\n- Nature exposure: Minimum 20 minutes outdoors\n\n## Supplement Protocol\n- Magnesium Glycinate: 400mg (most common deficiency in anxiety)\n- Ashwagandha KSM-66: 300mg\n- L-Theanine: 200mg (as needed)\n- Inositol: 2g (research-backed for anxiety)\n- Vitamin D3: 5000IU (deficiency linked to anxiety/depression)\n\n## Dietary Changes\n- Eliminate caffeine (or reduce drastically)\n- Blood sugar stability (eat every 4-5 hours)\n- Limit alcohol (suppresses GABA, rebounds with anxiety)",
        ],
        [
            'title'      => 'Longevity & Cellular Health Protocol',
            'category'   => 'longevity',
            'difficulty' => 'advanced',
            'duration'   => 'Ongoing',
            'tags'       => ['longevity', 'autophagy', 'NAD+', 'senescence', 'healthspan'],
            'content'    => "## Overview\nEvidence-based protocol targeting key aging hallmarks: mitochondrial dysfunction, cellular senescence, NAD+ decline, and loss of proteostasis.\n\n## Autophagy Activation\n\n### Fasting Protocols\n- 16:8 daily fasting (minimal autophagy benefit)\n- 24-hour fast: monthly\n- 72-hour fast: quarterly (maximum autophagy – medical supervision recommended)\n\n### Rapamycin (Rx Required)\nLow-dose rapamycin gaining evidence for longevity (mTOR inhibition). Consult longevity physician.\n\n## NAD+ Optimization\n- NMN: 500mg morning (NAD+ precursor)\n- NR (alternative): 300mg\n- Resveratrol: 500mg with fat (SIRT1 activator)\n- Exercise: Best natural NAD+ booster\n\n## Mitochondrial Support\n- CoQ10: 200mg (or Ubiquinol for 40+)\n- PQQ: 20mg\n- R-ALA: 300mg\n- Methylene Blue: micro-dosing (emerging evidence)\n\n## Senolytics (Senescent Cell Clearance)\n- Fisetin: 1000mg/day for 2 days monthly\n- Quercetin: 1000mg/day for 2 days monthly\n- Dasatinib/Quercetin protocol (clinical research)\n\n## Biomarkers to Track\nBlood testing every 6 months: telomere length, epigenetic age (TruDiagnostic), full metabolic panel, CRP, homocysteine.",
        ],
        [
            'title'      => 'Immune System Fortification Protocol',
            'category'   => 'immunity',
            'difficulty' => 'beginner',
            'duration'   => '30 days',
            'tags'       => ['immunity', 'immune-system', 'prevention', 'vitamin-C', 'zinc'],
            'content'    => "## Overview\nBuild robust immune resilience through evidence-based nutrition, supplements, and lifestyle practices.\n\n## Core Immune Nutrients\n\n### Vitamin D3 + K2\n- Most people are deficient\n- 5000 IU D3 + 100mcg K2 daily\n- Target blood level: 60-80 ng/mL\n\n### Zinc\n- Critical for T-cell function\n- 30mg zinc bisglycinate daily\n- Don't exceed 40mg without zinc/copper ratio monitoring\n\n### Vitamin C\n- Liposomal C: 1g twice daily\n- During illness: bowel tolerance dosing (2-4g every 4 hours)\n\n### Quercetin\n- 500mg twice daily\n- Zinc ionophore – helps zinc enter cells\n\n## Lifestyle Imperatives\n- Sleep: Immune memory consolidation happens during sleep\n- Stress reduction: Chronic stress devastates NK cell activity\n- Exercise: Moderate (not excessive) exercise enhances surveillance\n\n## Gut-Immune Axis\n70% of immune tissue is in the gut (GALT). Prioritize:\n- Diverse fiber intake (polyphenols)\n- Fermented foods daily\n- Probiotic: Lactobacillus + Bifidobacterium blend",
        ],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $data  = self::$protocols[self::$index % count(self::$protocols)];
        self::$index++;

        return [
            'user_id'      => User::factory(),
            'title'        => $data['title'],
            'content'      => $data['content'],
            'tags'         => $data['tags'],
            'category'     => $data['category'],
            'difficulty'   => $data['difficulty'],
            'duration'     => $data['duration'],
            'avg_rating'   => 0,
            'review_count' => 0,
            'vote_score'   => 0,
            'status'       => 'published',
        ];
    }
}

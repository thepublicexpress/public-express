<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poll;
use App\Models\PollQuestion;

class PollSeeder extends Seeder
{
    public function run()
    {
        // Create the poll (if not exists)
        $poll = Poll::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'UP विधानसभा चुनाव 2027 - Opinion Poll',
                'description' => 'उत्तर प्रदेश की 403 विधानसभा सीटों के लिए जनता की राय।',
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => 1,
            ]
        );

        // Clear old questions
        PollQuestion::where('poll_id', $poll->id)->delete();

        // 11 Questions (as per our earlier list)
        $questions = [
            [
                'question' => 'आप 2027 के UP विधानसभा चुनाव में किस पार्टी को वोट देंगे?',
                'options' => [
                    ['label' => 'भारतीय जनता पार्टी (BJP)', 'value' => 'bjp'],
                    ['label' => 'समाजवादी पार्टी (SP)', 'value' => 'sp'],
                    ['label' => 'बहुजन समाज पार्टी (BSP)', 'value' => 'bsp'],
                    ['label' => 'कांग्रेस (INC)', 'value' => 'inc'],
                    ['label' => 'अन्य', 'value' => 'other'],
                ],
                'order' => 1,
            ],
            [
                'question' => 'अगर आपको मुख्यमंत्री चुनना हो तो किसे चुनेंगे?',
                'options' => [
                    ['label' => 'योगी आदित्यनाथ (BJP)', 'value' => 'yogi'],
                    ['label' => 'अखिलेश यादव (SP)', 'value' => 'akhilesh'],
                    ['label' => 'मायावती (BSP)', 'value' => 'mayawati'],
                    ['label' => 'राहुल गांधी (INC)', 'value' => 'rahul'],
                    ['label' => 'अन्य', 'value' => 'other'],
                ],
                'order' => 2,
            ],
            [
                'question' => 'वर्तमान योगी सरकार के कामकाज से आप कितने संतुष्ट हैं?',
                'options' => [
                    ['label' => 'पूरी तरह संतुष्ट', 'value' => 'fully_satisfied'],
                    ['label' => 'कुछ हद तक संतुष्ट', 'value' => 'somewhat_satisfied'],
                    ['label' => 'न तो संतुष्ट, न असंतुष्ट', 'value' => 'neutral'],
                    ['label' => 'कुछ हद तक असंतुष्ट', 'value' => 'somewhat_dissatisfied'],
                    ['label' => 'पूरी तरह असंतुष्ट', 'value' => 'fully_dissatisfied'],
                ],
                'order' => 3,
            ],
            [
                'question' => 'पिछले 5 वर्षों में आपके क्षेत्र का विकास कैसा रहा?',
                'options' => [
                    ['label' => 'बहुत अच्छा', 'value' => 'very_good'],
                    ['label' => 'अच्छा', 'value' => 'good'],
                    ['label' => 'औसत', 'value' => 'average'],
                    ['label' => 'खराब', 'value' => 'poor'],
                    ['label' => 'बहुत खराब', 'value' => 'very_poor'],
                ],
                'order' => 4,
            ],
            [
                'question' => 'आपके क्षेत्र के वर्तमान विधायक (MLA) के कामकाज से आप कितने संतुष्ट हैं?',
                'options' => [
                    ['label' => 'पूरी तरह संतुष्ट', 'value' => 'fully_satisfied'],
                    ['label' => 'कुछ हद तक संतुष्ट', 'value' => 'somewhat_satisfied'],
                    ['label' => 'न तो संतुष्ट, न असंतुष्ट', 'value' => 'neutral'],
                    ['label' => 'कुछ हद तक असंतुष्ट', 'value' => 'somewhat_dissatisfied'],
                    ['label' => 'पूरी तरह असंतुष्ट', 'value' => 'fully_dissatisfied'],
                ],
                'order' => 5,
            ],
            [
                'question' => 'क्या आप अगले चुनाव में अपना विधायक बदलना चाहेंगे?',
                'options' => [
                    ['label' => 'हाँ, पूरी तरह बदलना चाहता हूँ', 'value' => 'yes_definitely'],
                    ['label' => 'शायद बदलना चाहूँ', 'value' => 'maybe'],
                    ['label' => 'नहीं, वही विधायक चाहिए', 'value' => 'no_same_mla'],
                    ['label' => 'अभी निर्णय नहीं लिया', 'value' => 'undecided'],
                ],
                'order' => 6,
            ],
            [
                'question' => 'क्या आप पार्टी से संतुष्ट हैं लेकिन विधायक से नहीं?',
                'options' => [
                    ['label' => 'हाँ, पार्टी से संतुष्ट हूँ, विधायक से नहीं', 'value' => 'party_satisfied_mla_not'],
                    ['label' => 'नहीं, दोनों से संतुष्ट हूँ', 'value' => 'both_satisfied'],
                    ['label' => 'नहीं, पार्टी से भी नहीं, विधायक से भी नहीं', 'value' => 'both_not_satisfied'],
                    ['label' => 'पार्टी से संतुष्ट नहीं, लेकिन विधायक से संतुष्ट हूँ', 'value' => 'party_not_mla_satisfied'],
                    ['label' => 'कोई राय नहीं', 'value' => 'no_opinion'],
                ],
                'order' => 7,
            ],
            [
                'question' => 'क्या आप विधायक से संतुष्ट हैं लेकिन पार्टी से नहीं?',
                'options' => [
                    ['label' => 'हाँ, विधायक से संतुष्ट हूँ, पार्टी से नहीं', 'value' => 'mla_satisfied_party_not'],
                    ['label' => 'नहीं, दोनों से संतुष्ट हूँ', 'value' => 'both_satisfied'],
                    ['label' => 'नहीं, विधायक से भी नहीं, पार्टी से भी नहीं', 'value' => 'both_not_satisfied'],
                    ['label' => 'विधायक से संतुष्ट नहीं, लेकिन पार्टी से संतुष्ट हूँ', 'value' => 'mla_not_party_satisfied'],
                    ['label' => 'कोई राय नहीं', 'value' => 'no_opinion'],
                ],
                'order' => 8,
            ],
            [
                'question' => 'आपके क्षेत्र की सबसे बड़ी समस्या क्या है?',
                'options' => [
                    ['label' => 'बेरोजगारी', 'value' => 'unemployment'],
                    ['label' => 'कानून-व्यवस्था', 'value' => 'law_order'],
                    ['label' => 'बिजली-पानी की समस्या', 'value' => 'electricity_water'],
                    ['label' => 'सड़कें और बुनियादी ढांचा', 'value' => 'roads_infrastructure'],
                    ['label' => 'शिक्षा और स्वास्थ्य', 'value' => 'education_health'],
                    ['label' => 'किसानों की समस्या', 'value' => 'farmers'],
                    ['label' => 'अन्य', 'value' => 'other'],
                ],
                'order' => 9,
            ],
            [
                'question' => 'क्या आपको लगता है कि इस बार सरकार बदलेगी?',
                'options' => [
                    ['label' => 'हाँ, पूरी तरह बदलेगी', 'value' => 'yes_definitely'],
                    ['label' => 'शायद बदले', 'value' => 'maybe'],
                    ['label' => 'नहीं, वही सरकार आएगी', 'value' => 'no_same_government'],
                    ['label' => 'मुझे नहीं पता', 'value' => 'dont_know'],
                ],
                'order' => 10,
            ],
            [
                'question' => 'आपको क्या लगता है, इस बार कौन सी पार्टी UP में सबसे अधिक सीटें जीतेगी?',
                'options' => [
                    ['label' => 'BJP', 'value' => 'bjp'],
                    ['label' => 'SP', 'value' => 'sp'],
                    ['label' => 'BSP', 'value' => 'bsp'],
                    ['label' => 'Congress', 'value' => 'congress'],
                    ['label' => 'अन्य', 'value' => 'other'],
                ],
                'order' => 11,
            ],
        ];

        foreach ($questions as $q) {
            PollQuestion::create([
                'poll_id' => $poll->id,
                'question' => $q['question'],
                'options' => json_encode($q['options']),
                'order_number' => $q['order'],
                'type' => 'single',
            ]);
        }

        $this->command->info('✅ Poll and questions seeded successfully!');
    }
}
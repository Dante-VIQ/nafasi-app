<?php
// database/seeders/DictionarySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DictionaryEntry;

class DictionarySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // ============================================
            // SWAHILI — EMERGENCY & MEDICAL
            // ============================================
            [
                'word' => 'moto',
                'word_normalized' => 'moto',
                'language' => 'sw',
                'definition' => 'fire, flame, heat',
                'meanings' => ['fire', 'flame', 'heat', 'temperature', 'trouble', 'danger'],
                'tags' => ['emergency', 'fire', 'danger'],
                'emergency_weight' => 0.95,
                'is_emergency_signal' => true,
                'synonyms' => ['ungua', 'waka', 'choma'],
                'source' => 'kamusi',
            ],
            [
                'word' => 'damu',
                'word_normalized' => 'damu',
                'language' => 'sw',
                'definition' => 'blood',
                'meanings' => ['blood', 'bleeding', 'hemorrhage', 'injury'],
                'tags' => ['emergency', 'medical', 'injury'],
                'emergency_weight' => 0.85,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            [
                'word' => 'ajali',
                'word_normalized' => 'ajali',
                'language' => 'sw',
                'definition' => 'accident, misfortune',
                'meanings' => ['accident', 'crash', 'disaster', 'misfortune'],
                'tags' => ['emergency', 'accident'],
                'emergency_weight' => 0.90,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            [
                'word' => 'hatari',
                'word_normalized' => 'hatari',
                'language' => 'sw',
                'definition' => 'danger, risk, peril',
                'meanings' => ['danger', 'risk', 'peril', 'emergency', 'threat'],
                'tags' => ['emergency', 'danger'],
                'emergency_weight' => 0.80,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            
            // ============================================
            // SWAHILI — CRISIS / MENTAL HEALTH
            // ============================================
            [
                'word' => 'kujiua',
                'word_normalized' => 'kujiua',
                'language' => 'sw',
                'definition' => 'suicide, to kill oneself',
                'meanings' => ['suicide', 'self-harm', 'kill oneself'],
                'tags' => ['crisis', 'mental_health', 'suicide'],
                'emergency_weight' => 1.0,
                'is_crisis_signal' => true,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            [
                'word' => 'kufa',
                'word_normalized' => 'kufa',
                'language' => 'sw',
                'definition' => 'to die, death',
                'meanings' => ['die', 'death', 'dying', 'perish'],
                'tags' => ['emergency', 'crisis', 'death'],
                'emergency_weight' => 0.90,
                'is_crisis_signal' => true,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            
            // ============================================
            // SWAHILI — HELP COME TO ME SIGNALS
            // ============================================
            [
                'word' => 'anguka',
                'word_normalized' => 'anguka',
                'language' => 'sw',
                'definition' => 'to fall, fall down',
                'meanings' => ['fall', 'fall down', 'collapse', 'tumble'],
                'tags' => ['emergency', 'injury', 'mobility'],
                'emergency_weight' => 0.70,
                'is_help_come_to_me_signal' => true,
                'is_emergency_signal' => true,
                'source' => 'kamusi',
            ],
            [
                'word' => 'kitandani',
                'word_normalized' => 'kitandani',
                'language' => 'sw',
                'definition' => 'in bed, bedridden',
                'meanings' => ['bedridden', 'in bed', 'cannot move', 'sick in bed'],
                'tags' => ['mobility', 'health'],
                'emergency_weight' => 0.50,
                'is_help_come_to_me_signal' => true,
                'source' => 'kamusi',
            ],
            
            // ============================================
            // SWAHILI — FACILITY HINTS
            // ============================================
            [
                'word' => 'dawa',
                'word_normalized' => 'dawa',
                'language' => 'sw',
                'definition' => 'medicine, drug, chemical',
                'meanings' => ['medicine', 'drug', 'medication', 'chemical', 'treatment'],
                'tags' => ['medical', 'pharmacy'],
                'facility_type_hint' => 'pharmacy',
                'source' => 'kamusi',
            ],
            [
                'word' => 'hospitali',
                'word_normalized' => 'hospitali',
                'language' => 'sw',
                'definition' => 'hospital',
                'meanings' => ['hospital', 'medical center'],
                'tags' => ['medical', 'facility'],
                'facility_type_hint' => 'hospital',
                'source' => 'kamusi',
            ],
            [
                'word' => 'maabara',
                'word_normalized' => 'maabara',
                'language' => 'sw',
                'definition' => 'laboratory',
                'meanings' => ['laboratory', 'lab', 'testing facility'],
                'tags' => ['medical', 'diagnostic'],
                'facility_type_hint' => 'laboratory',
                'source' => 'kamusi',
            ],
            [
                'word' => 'mimba',
                'word_normalized' => 'mimba',
                'language' => 'sw',
                'definition' => 'pregnancy, conception',
                'meanings' => ['pregnancy', 'pregnant', 'expecting', 'conception'],
                'tags' => ['medical', 'maternal'],
                'facility_type_hint' => 'maternity_home',
                'source' => 'kamusi',
            ],
            [
                'word' => 'meno',
                'word_normalized' => 'meno',
                'language' => 'sw',
                'definition' => 'teeth',
                'meanings' => ['teeth', 'tooth', 'dental'],
                'tags' => ['medical', 'dental'],
                'facility_type_hint' => 'dental_clinic',
                'source' => 'kamusi',
            ],
            
            // ============================================
            // SHENG — EMERGENCY
            // ============================================
            [
                'word' => 'noma',
                'word_normalized' => 'noma',
                'language' => 'sheng',
                'definition' => 'serious problem, danger, emergency',
                'meanings' => ['danger', 'problem', 'emergency', 'serious', 'trouble'],
                'tags' => ['emergency', 'danger'],
                'emergency_weight' => 0.85,
                'is_emergency_signal' => true,
                'source' => 'community',
            ],
            [
                'word' => 'kudedi',
                'word_normalized' => 'kudedi',
                'language' => 'sheng',
                'definition' => 'to die, dying',
                'meanings' => ['die', 'dying', 'death', 'perish'],
                'tags' => ['emergency', 'crisis', 'death'],
                'emergency_weight' => 0.95,
                'is_crisis_signal' => true,
                'is_emergency_signal' => true,
                'source' => 'community',
            ],
            [
                'word' => 'kuchapa',
                'word_normalized' => 'kuchapa',
                'language' => 'sheng',
                'definition' => 'to hit, beat, attack; also: to be severe (pain)',
                'meanings' => ['hit', 'beat', 'attack', 'severe pain', 'fighting'],
                'tags' => ['violence', 'emergency', 'pain'],
                'emergency_weight' => 0.75,
                'is_emergency_signal' => true,
                'source' => 'community',
            ],
            [
                'word' => 'gava',
                'word_normalized' => 'gava',
                'language' => 'sheng',
                'definition' => 'police, government',
                'meanings' => ['police', 'government', 'authorities'],
                'tags' => ['police', 'authority'],
                'facility_type_hint' => 'police_station',
                'source' => 'community',
            ],
            
            // ============================================
            // SHENG — HELP COME TO ME
            // ============================================
            [
                'word' => 'nimekwama',
                'word_normalized' => 'nimekwama',
                'language' => 'sheng',
                'definition' => 'I am stuck, trapped',
                'meanings' => ['stuck', 'trapped', 'cannot move', 'stranded'],
                'tags' => ['mobility', 'help_needed'],
                'is_help_come_to_me_signal' => true,
                'source' => 'community',
            ],
            
            // ============================================
            // ENGLISH — same categories
            // ============================================
            [
                'word' => 'fire',
                'word_normalized' => 'fire',
                'language' => 'en',
                'definition' => 'combustion, burning, flames',
                'meanings' => ['fire', 'flame', 'burning', 'combustion', 'blaze'],
                'tags' => ['emergency', 'fire', 'danger'],
                'emergency_weight' => 0.95,
                'is_emergency_signal' => true,
                'source' => 'wordnet',
            ],
            [
                'word' => 'suicide',
                'word_normalized' => 'suicide',
                'language' => 'en',
                'definition' => 'the act of intentionally causing one\'s own death',
                'meanings' => ['suicide', 'self-harm', 'kill oneself', 'end one\'s life'],
                'tags' => ['crisis', 'mental_health', 'suicide'],
                'emergency_weight' => 1.0,
                'is_crisis_signal' => true,
                'is_emergency_signal' => true,
                'source' => 'wordnet',
            ],
            [
                'word' => 'pharmacy',
                'word_normalized' => 'pharmacy',
                'language' => 'en',
                'definition' => 'a shop where medicinal drugs are dispensed',
                'meanings' => ['pharmacy', 'drugstore', 'chemist', 'dispensary'],
                'tags' => ['medical', 'pharmacy'],
                'facility_type_hint' => 'pharmacy',
                'source' => 'wordnet',
            ],
            [
                'word' => 'bleeding',
                'word_normalized' => 'bleeding',
                'language' => 'en',
                'definition' => 'losing blood',
                'meanings' => ['bleeding', 'hemorrhage', 'blood loss', 'injury'],
                'tags' => ['emergency', 'medical', 'injury'],
                'emergency_weight' => 0.90,
                'is_emergency_signal' => true,
                'source' => 'wordnet',
            ],
        ];

        foreach ($entries as $entry) {
            DictionaryEntry::create($entry);
        }
    }
}
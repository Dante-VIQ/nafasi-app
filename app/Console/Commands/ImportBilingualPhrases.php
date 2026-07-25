<?php

namespace App\Console\Commands;

use App\Models\DictionaryEntry;
use Illuminate\Console\Command;

class ImportBilingualPhrases extends Command
{
    protected $signature = 'dictionary:import-bilingual 
                            {file : Path to the JSONL file}
                            {--language-pair=en-sw : Language pair code, e.g., en-sw}';

    protected $description = 'Import emergency/medical English‑Swahili sentence pairs into the dictionary';

    // English keywords that indicate an emergency, medical, or facility‑related sentence
    protected array $englishKeywords = [
        'fire', 'burning', 'accident', 'crash', 'bleeding', 'unconscious',
        'heart attack', 'stroke', 'ambulance', 'police', 'snake', 'flood',
        'chest pain', 'pregnant', 'pharmacy', 'hospital', 'clinic', 'dentist',
        'doctor', 'nurse', 'medicine', 'laboratory', 'x-ray', 'scan',
        'headache', 'fever', 'cough', 'diarrhoea', 'vomiting', 'fracture',
        'wound', 'allergy', 'asthma', 'diabetes', 'hypertension', 'mental',
        'depression', 'anxiety', 'suicide', 'self-harm',
    ];

    // Swahili keywords that indicate emergency/medical
    protected array $swahiliKeywords = [
        'moto', 'ajali', 'damu', 'kupumua', 'maumivu', 'hospitali',
        'daktari', 'dawa', 'mimba', 'kujifungua', 'polisi', 'nyoka',
        'kifua', 'kichwa', 'homa', 'kukohoa', 'kuhara', 'kutapika',
        'kuvunjika', 'jeraha', 'mshtuko', 'kujiua', 'unyogovu',
        'wasiwasi', 'akili', 'maabara', 'scan', 'eksirei',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');
        if (! $handle) {
            $this->error('Could not open file.');

            return Command::FAILURE;
        }

        $this->info('Processing CSV file...');
        $imported = 0;
        $skipped = 0;
        $totalLines = 0;
        $progress = $this->output->createProgressBar();

        // Skip header if present (check if first line looks like a header)
        $firstLine = fgetcsv($handle);
        if ($firstLine) {
            // Check if first line contains typical headers (en, sw, english, swahili)
            $firstLower = array_map('strtolower', $firstLine);
            if (in_array('en', $firstLower) || in_array('english', $firstLower) || in_array('swahili', $firstLower)) {
                $this->info('Header detected, skipping.');
            } else {
                // It's data, process it
                rewind($handle);
            }
        }

        // Count lines for progress bar (optional, can skip if file is huge but 23k is fine)
        $lineCount = 0;
        $temp = fopen($file, 'r');
        while (! feof($temp)) {
            fgets($temp);
            $lineCount++;
        }
        fclose($temp);
        $progress->start($lineCount);

        while (($row = fgetcsv($handle)) !== false) {
            $totalLines++;
            $progress->advance();

            if (count($row) < 2) {
                $skipped++;

                continue;
            }

            // Assume first column = English, second = Swahili (adjust if needed)
$score = trim($row[0]);  // ignore (similarity)
$en    = trim($row[1]);  // English
$sw    = trim($row[2]);  // Swahili

            if (empty($en) || empty($sw)) {
                $skipped++;

                continue;
            }

            $enLower = strtolower($en);
            $swLower = strtolower($sw);

            // Guard: skip unusually long phrases (probably not real Swahili)
// Skip if Swahili column is suspiciously long or empty
if (empty($sw) || str_word_count($sw) > 30) {
    $skipped++;
    continue;
}

            // Check for emergency/medical keywords (same as before)
            $isEmergency = false;
            foreach ($this->englishKeywords as $kw) {
                if (str_contains($enLower, $kw)) {
                    $isEmergency = true;
                    break;
                }
            }
            if (! $isEmergency) {
                foreach ($this->swahiliKeywords as $kw) {
                    if (str_contains($swLower, $kw)) {
                        $isEmergency = true;
                        break;
                    }
                }
            }

            if (! $isEmergency) {
                $skipped++;

                continue;
            }

            // Store as dictionary entry
            $entry = DictionaryEntry::firstOrCreate(
                [
                    'word_normalized' => $swLower,
                    'language' => 'sw',
                ],
                [
                    'word' => $sw,
                    'definition' => $en,
                    'meanings' => [$en],
                    'tags' => ['emergency', 'medical'],
                    'source' => 'huggingface',
                    'emergency_weight' => 0.8,
                    'is_emergency_signal' => true,
                    'part_of_speech' => 'phrase',
                ]
            );

            if (! $entry->wasRecentlyCreated && empty($entry->definition)) {
                $entry->definition = $en;
                $entry->save();
            }

            $imported++;
        }

        fclose($handle);
        $progress->finish();
        $this->newLine();
        $this->info("Done! Processed {$totalLines} rows. Imported {$imported}, skipped {$skipped}.");

        return Command::SUCCESS;
    }
}

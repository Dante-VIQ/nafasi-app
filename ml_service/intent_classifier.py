# ml_service/intent_classifier.py (revised classify method)

import json
import os
from dictionary.dictionary_loader import DictionaryLoader

class IntentClassifier:
    def __init__(self):
        self.dictionary = DictionaryLoader()
        # Keep minimal fallback only for when dictionary is empty
        self.fallback = {
            'crisis': ['kill myself', 'suicide', 'want to die', 'kujiua'],
            'emergency': {
                'fire': ['fire', 'moto', 'burning'],
                'accident': ['accident', 'ajali', 'crash'],
                'medical': ['heart attack', 'bleeding', 'unconscious'],
                'police': ['police', 'attacked', 'robbed'],
            },
            'dispatch': ['can\'t move', 'stuck', 'bedridden'],
            'facility': {
                'pharmacy': ['pharmacy', 'dawa', 'famasi'],
                'laboratory': ['lab', 'maabara', 'test'],
                'dental': ['dental', 'meno'],
                'maternity': ['maternity', 'mimba'],
                'hospital': ['hospital', 'hospitali', 'clinic'],
            }
        }

    def classify(self, text: str, language: str) -> dict:
        text_lower = text.lower().strip()
        words = text_lower.split()

        result = {
            'is_crisis': False,
            'is_emergency': False,
            'emergency_type': None,
            'facility_hints': [],
            'needs_dispatch': False,
            'is_anonymous_report': False,
            'confidence': 0.0,
            'matched_signals': [],
        }

        # Query dictionary for each word
        for word in words:
            entries = self.dictionary.lookup(word, language)
            for entry in entries:
                # Check crisis
                if entry.get('is_crisis'):
                    result['is_crisis'] = True
                    result['confidence'] = max(result['confidence'], 0.95)
                    result['matched_signals'].append(f'crisis:{word}')
                    return result  # Crisis overrides all

                # Check emergency
                if entry.get('is_emergency'):
                    result['is_emergency'] = True
                    result['confidence'] = max(result['confidence'], 0.85)
                    tags = json.loads(entry.get('tags', '[]'))
                    if 'fire' in tags:
                        result['emergency_type'] = 'fire'
                    elif 'accident' in tags:
                        result['emergency_type'] = 'accident'
                    elif 'police' in tags:
                        result['emergency_type'] = 'police'
                    else:
                        result['emergency_type'] = 'medical'
                    result['matched_signals'].append(f'emergency:{word}')

                # Check dispatch
                if entry.get('is_dispatch'):
                    result['needs_dispatch'] = True
                    result['confidence'] = max(result['confidence'], 0.75)
                    result['matched_signals'].append(f'dispatch:{word}')

                # Collect facility hints
                hint = entry.get('facility_hint')
                if hint and hint not in result['facility_hints']:
                    result['facility_hints'].append(hint)
                    result['confidence'] = max(result['confidence'], 0.7)
                    result['matched_signals'].append(f'facility:{hint}:{word}')

        # Also check multi-word phrases (like "siwezi tembea")
        # We'll look up the whole text and also bigrams
        bigrams = [' '.join(words[i:i+2]) for i in range(len(words)-1)]
        for phrase in [text_lower] + bigrams:
            entries = self.dictionary.lookup(phrase, language)
            for entry in entries:
                if entry.get('is_dispatch'):
                    result['needs_dispatch'] = True
                    result['confidence'] = max(result['confidence'], 0.8)
                    result['matched_signals'].append(f'dispatch_phrase:{phrase}')
                if entry.get('is_emergency'):
                    result['is_emergency'] = True
                    result['confidence'] = max(result['confidence'], 0.85)
                    result['matched_signals'].append(f'emergency_phrase:{phrase}')

        # Fallback: use static lists if no signals detected
        if result['confidence'] == 0.0:
            result = self._fallback_classify(text_lower, language, result)
        
        if result['confidence'] == 0.0:
            result['confidence'] = 0.3
            result['facility_hints'] = ['hospital']  # default

        return result

    def _fallback_classify(self, text_lower: str, language: str, result: dict) -> dict:
        # Crisis fallback
        for word in self.fallback['crisis']:
            if word in text_lower:
                result['is_crisis'] = True
                result['confidence'] = 0.9
                return result
        # Emergency fallback
        for etype, keywords in self.fallback['emergency'].items():
            for word in keywords:
                if word in text_lower:
                    result['is_emergency'] = True
                    result['emergency_type'] = etype
                    result['confidence'] = 0.8
                    return result
        # Dispatch fallback
        for phrase in self.fallback['dispatch']:
            if phrase in text_lower:
                result['needs_dispatch'] = True
                result['confidence'] = 0.7
        # Facility hints fallback
        for hint, keywords in self.fallback['facility'].items():
            for word in keywords:
                if word in text_lower and hint not in result['facility_hints']:
                    result['facility_hints'].append(hint)
                    result['confidence'] = max(result['confidence'], 0.65)
        return result
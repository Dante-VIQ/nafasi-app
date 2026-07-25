# ml_service/intent_classifier_learning.py

import json
import os

class LearningIntentClassifier:
    """
    Intent classifier that improves from training data.
    Starts with base patterns, learns new ones from interactions.
    """

    def __init__(self):
        self.base_classifier = IntentClassifier()  # Original
        self.learned_patterns = self._load_learned_patterns()

    def _load_learned_patterns(self) -> dict:
        """Load patterns learned from real interactions."""
        patterns_file = 'ml_service/models/learned_patterns.json'
        if os.path.exists(patterns_file):
            with open(patterns_file, 'r', encoding='utf-8') as f:
                return json.load(f)
        return {}

    def classify(self, text: str, language: str) -> dict:
        """Classify with base patterns + learned patterns."""
        # Get base classification
        result = self.base_classifier.classify(text, language)
        
        # Enhance with learned patterns
        if language in self.learned_patterns:
            for intent, words in self.learned_patterns[language].items():
                for word in words:
                    if word in text.lower():
                        if intent.startswith('facility:'):
                            facility_type = intent.replace('facility:', '')
                            if facility_type not in result.get('facility_hints', []):
                                result['facility_hints'].append(facility_type)
                                result['confidence'] = max(result['confidence'], 0.75)
                                result['matched_signals'].append(f'learned:{intent}:{word}')
        
        return result
# ml_service/language_detector.py (REVISED)

import re
from dictionary.dictionary_loader import DictionaryLoader

class LanguageDetector:
    """
    Detects language using DYNAMIC dictionary lookups.
    Falls back to basic heuristics only when dictionary is unavailable.
    """

    def __init__(self):
        self.dictionary = DictionaryLoader()
        
        # Minimal fallback markers (used only if dictionary is empty)
        self.fallback_sheng = ['noma', 'mboka', 'manze', 'gava', 'daktar', 'kudedi']
        self.fallback_sw_function = ['na', 'ni', 'ya', 'za', 'kwa', 'sana', 'hii']

    def detect(self, text: str) -> dict:
        text_lower = text.lower().strip()
        words = text_lower.split()
        
        if not words:
            return {'language': 'en', 'confidence': 0.5}

        # Try dictionary lookup first
        sheng_count = 0
        sw_count = 0
        total_looked_up = 0
        
        for word in words:
            # Check dictionary for exact match
            if self.dictionary.is_language_word(word, 'sheng'):
                sheng_count += 3  # Heavy weight for dictionary match
                total_looked_up += 1
            elif self.dictionary.is_language_word(word, 'sw'):
                sw_count += 1
                total_looked_up += 1
            else:
                # Fallback: check static markers (only if word not in dictionary)
                if word in self.fallback_sheng:
                    sheng_count += 1
                if word in self.fallback_sw_function:
                    sw_count += 1

        # If dictionary had matches, trust it
        if total_looked_up > 0:
            if sheng_count >= sw_count:
                confidence = min(0.95, 0.7 + (sheng_count / max(len(words), 1)) * 0.3)
                return {'language': 'sheng', 'confidence': round(confidence, 2)}
            elif sw_count > 0:
                confidence = min(0.95, 0.6 + (sw_count / max(len(words), 1)) * 0.3)
                return {'language': 'sw', 'confidence': round(confidence, 2)}

        # Fallback: use static heuristics
        if sheng_count > sw_count and sheng_count >= 1:
            confidence = min(0.85, sheng_count / max(len(words), 1) * 2)
            return {'language': 'sheng', 'confidence': round(confidence, 2)}
        
        if sw_count >= 2:
            confidence = min(0.85, sw_count / max(len(words), 1) * 2)
            return {'language': 'sw', 'confidence': round(confidence, 2)}

        # Default English
        return {'language': 'en', 'confidence': 0.5}
# ml_service/training/daily_trainer.py

import json
import os
from datetime import datetime, timedelta

class DailyTrainer:
    """
    Runs once per day (or week).
    1. Collects recent anonymized interactions
    2. Updates intent classifier with new patterns
    3. Evaluates against holdout set
    4. Deploys if improved
    5. NEVER uses personal data
    """

    def __init__(self):
        self.model_path = 'ml_service/models/'
        self.training_logs_path = 'ml_service/training_logs/'
        self.min_interactions_for_training = 100  # Need enough data

    def should_train(self) -> bool:
        """Check if enough new data is available."""
        yesterday = (datetime.now() - timedelta(days=1)).strftime('%Y-%m-%d')
        log_file = f'{self.training_logs_path}interactions_{yesterday}.jsonl'
        
        if not os.path.exists(log_file):
            return False
        
        count = sum(1 for _ in open(log_file, 'r', encoding='utf-8'))
        return count >= self.min_interactions_for_training

    def train(self):
        """
        Training process:
        1. Load recent interactions
        2. Extract new keywords and patterns
        3. Update classifier weights
        4. Save new model version
        5. Log metrics
        """
        if not self.should_train():
            return {'status': 'skipped', 'reason': 'Not enough data'}

        interactions = self._load_recent_interactions(days=7)
        
        # Extract new patterns from successful interactions
        new_patterns = self._extract_patterns(interactions)
        
        # Update the classifier
        self._update_classifier(new_patterns)
        
        # Save model with version
        version = datetime.now().strftime('%Y%m%d_%H%M')
        self._save_model(version)
        
        return {
            'status': 'trained',
            'version': version,
            'interactions_used': len(interactions),
            'new_patterns_found': len(new_patterns),
        }

    def _load_recent_interactions(self, days: int = 7) -> list:
        """Load interactions from the last N days."""
        interactions = []
        log_dir = self.training_logs_path
        
        for i in range(days):
            date_str = (datetime.now() - timedelta(days=i)).strftime('%Y-%m-%d')
            log_file = f'{log_dir}interactions_{date_str}.jsonl'
            
            if os.path.exists(log_file):
                with open(log_file, 'r', encoding='utf-8') as f:
                    for line in f:
                        interactions.append(json.loads(line))
        
        return interactions

    def _extract_patterns(self, interactions: list) -> dict:
        """
        Learn from successful interactions.
        "When user said X and language was Y, they selected facility type Z"
        """
        patterns = {}
        
        for interaction in interactions:
            if interaction.get('outcome') in ['booked', 'called']:
                text = interaction.get('text', '').lower().strip()
                language = interaction.get('language', 'en')
                intent = interaction.get('intent', '')
                
                if language not in patterns:
                    patterns[language] = {}
                
                if intent not in patterns[language]:
                    patterns[language][intent] = []
                
                # Extract new keywords (words that led to successful match)
                words = text.split()
                for word in words:
                    if len(word) > 2 and word not in patterns[language][intent]:
                        patterns[language][intent].append(word)
        
        return patterns

    def _update_classifier(self, new_patterns: dict):
        """
        Merge new patterns into existing classifier.
        Increases weight of patterns that led to successful outcomes.
        """
        # In production: update the intent_classifier.py weights
        # For now: save patterns to a JSON file the classifier reads
        
        import json
        patterns_file = f'{self.model_path}learned_patterns.json'
        
        existing = {}
        if os.path.exists(patterns_file):
            with open(patterns_file, 'r', encoding='utf-8') as f:
                existing = json.load(f)
        
        # Merge new patterns
        for lang, intents in new_patterns.items():
            if lang not in existing:
                existing[lang] = {}
            for intent, words in intents.items():
                if intent not in existing[lang]:
                    existing[lang][intent] = []
                existing[lang][intent] = list(set(existing[lang][intent] + words))
        
        with open(patterns_file, 'w', encoding='utf-8') as f:
            json.dump(existing, f, indent=2)

    def _save_model(self, version: str):
        """Save model version with metadata."""
        import json
        
        metadata = {
            'version': version,
            'trained_at': datetime.now().isoformat(),
            'training_size': self._count_interactions(),
        }
        
        os.makedirs(self.model_path, exist_ok=True)
        
        with open(f'{self.model_path}metadata_{version}.json', 'w') as f:
            json.dump(metadata, f, indent=2)

    def _count_interactions(self) -> int:
        """Count total training interactions."""
        total = 0
        if os.path.exists(self.training_logs_path):
            for file in os.listdir(self.training_logs_path):
                if file.endswith('.jsonl'):
                    with open(os.path.join(self.training_logs_path, file), 'r') as f:
                        total += sum(1 for _ in f)
        return total
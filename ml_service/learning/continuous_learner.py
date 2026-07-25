# ml_service/learning/continuous_learner.py

import json
import os
from datetime import datetime, timedelta
from typing import Dict, List
from collections import Counter

class ContinuousLearner:
    """
    Learns from every interaction.
    Improves routing, predictions, and community understanding.
    """

    def __init__(self):
        self.logs_path = 'training_logs/'
        self.models_path = 'models/'
        self.learned_patterns = self._load_learned_patterns()

    def _load_learned_patterns(self) -> dict:
        """Load previously learned patterns."""
        patterns_file = f'{self.models_path}learned_patterns.json'
        if os.path.exists(patterns_file):
            with open(patterns_file, 'r', encoding='utf-8') as f:
                return json.load(f)
        return {
            'language_patterns': {},
            'successful_routes': {},
            'peak_demand_times': {},
            'facility_performance': {},
            'community_preferences': {},
            'transport_effectiveness': {},
        }

    def learn_from_interactions(self, days: int = 7) -> dict:
        """Learn from recent interactions."""
        interactions = self._load_interactions(days)
        
        if len(interactions) < 50:
            return {'status': 'insufficient_data', 'count': len(interactions)}

        insights = {
            'language_patterns': self._learn_language_patterns(interactions),
            'successful_routes': self._learn_successful_routes(interactions),
            'peak_demand_times': self._learn_demand_patterns(interactions),
            'facility_performance': self._learn_facility_performance(interactions),
            'community_preferences': self._learn_community_preferences(interactions),
            'transport_effectiveness': self._learn_transport_effectiveness(interactions),
            'new_keywords': self._learn_new_keywords(interactions),
        }

        # Merge with existing patterns
        for key in insights:
            if key in self.learned_patterns:
                self.learned_patterns[key].update(insights[key])
            else:
                self.learned_patterns[key] = insights[key]

        # Save updated patterns
        self._save_patterns()

        return {
            'status': 'learned',
            'interactions_analyzed': len(interactions),
            'new_patterns_found': sum(len(v) for v in insights.values()),
            'patterns': insights,
        }

    def _load_interactions(self, days: int) -> List[dict]:
        """Load anonymized interactions from log files."""
        interactions = []
        
        if not os.path.exists(self.logs_path):
            return interactions

        for i in range(days):
            date_str = (datetime.now() - timedelta(days=i)).strftime('%Y-%m-%d')
            log_file = f'{self.logs_path}interactions_{date_str}.jsonl'
            
            if os.path.exists(log_file):
                with open(log_file, 'r', encoding='utf-8') as f:
                    for line in f:
                        try:
                            interactions.append(json.loads(line))
                        except json.JSONDecodeError:
                            continue

        return interactions

    def _learn_language_patterns(self, interactions: List[dict]) -> dict:
        """Learn language usage patterns."""
        patterns = {}
        
        for interaction in interactions:
            lang = interaction.get('language', 'en')
            if lang not in patterns:
                patterns[lang] = {'count': 0, 'common_phrases': Counter()}
            
            patterns[lang]['count'] += 1
            text = interaction.get('text', '').lower()
            words = text.split()
            for word in words:
                if len(word) > 2:
                    patterns[lang]['common_phrases'][word] += 1

        return patterns

    def _learn_successful_routes(self, interactions: List[dict]) -> dict:
        """Learn which routes were successful."""
        successful = {}
        
        for interaction in interactions:
            if interaction.get('outcome') in ['booked', 'called', 'directions']:
                intent = interaction.get('intent', '')
                facility_type = interaction.get('facility_type', '')
                
                key = f'{intent}→{facility_type}'
                if key not in successful:
                    successful[key] = 0
                successful[key] += 1

        return successful

    def _learn_demand_patterns(self, interactions: List[dict]) -> dict:
        """Learn when demand peaks."""
        demand = {
            'hourly': Counter(),
            'daily': Counter(),
            'monthly': Counter(),
        }
        
        for interaction in interactions:
            timestamp = interaction.get('timestamp', '')
            if timestamp:
                try:
                    dt = datetime.fromisoformat(timestamp)
                    demand['hourly'][dt.hour] += 1
                    demand['daily'][dt.strftime('%A')] += 1
                    demand['monthly'][dt.strftime('%B')] += 1
                except (ValueError, TypeError):
                    continue

        return {
            'peak_hours': demand['hourly'].most_common(5),
            'peak_days': demand['daily'].most_common(3),
            'peak_months': demand['monthly'].most_common(3),
        }

    def _learn_facility_performance(self, interactions: List[dict]) -> dict:
        """Learn facility response patterns."""
        facilities = {}
        
        for interaction in interactions:
            facility_id = interaction.get('facility_id', '')
            if facility_id:
                if facility_id not in facilities:
                    facilities[facility_id] = {
                        'total_routes': 0,
                        'successful_outcomes': 0,
                        'avg_response_time': 0,
                    }
                facilities[facility_id]['total_routes'] += 1
                if interaction.get('outcome') in ['booked', 'called']:
                    facilities[facility_id]['successful_outcomes'] += 1

        return facilities

    def _learn_community_preferences(self, interactions: List[dict]) -> dict:
        """Learn community patterns and preferences."""
        return {
            'preferred_payment': self._extract_preference(interactions, 'payment_method'),
            'preferred_language': self._extract_preference(interactions, 'language'),
            'preferred_time': self._extract_preference(interactions, 'time_of_day'),
        }

    def _extract_preference(self, interactions: List[dict], field: str) -> str:
        """Extract most common value for a field."""
        values = []
        for interaction in interactions:
            if field in interaction and interaction[field]:
                values.append(interaction[field])
        if values:
            return Counter(values).most_common(1)[0][0]
        return 'unknown'

    def _learn_transport_effectiveness(self, interactions: List[dict]) -> dict:
        """Learn which transport methods work best."""
        transport = {}
        
        for interaction in interactions:
            if interaction.get('type') == 'dispatch':
                method = interaction.get('transport_method', 'unknown')
                if method not in transport:
                    transport[method] = {'total': 0, 'on_time': 0}
                transport[method]['total'] += 1
                if interaction.get('arrived_on_time'):
                    transport[method]['on_time'] += 1

        return transport

    def _learn_new_keywords(self, interactions: List[dict]) -> dict:
        """Discover new keywords from successful interactions."""
        new_keywords = {}
        
        for interaction in interactions:
            if interaction.get('outcome') in ['booked', 'called']:
                text = interaction.get('text', '').lower()
                intent = interaction.get('intent', '')
                words = text.split()
                
                if intent not in new_keywords:
                    new_keywords[intent] = set()
                
                for word in words:
                    if len(word) > 2:
                        new_keywords[intent].add(word)

        # Convert sets to lists for JSON
        return {k: list(v) for k, v in new_keywords.items()}

    def _save_patterns(self):
        """Save learned patterns to disk."""
        os.makedirs(self.models_path, exist_ok=True)
        patterns_file = f'{self.models_path}learned_patterns.json'
        
        with open(patterns_file, 'w', encoding='utf-8') as f:
            json.dump(self.learned_patterns, f, indent=2, default=str)

    def predict_demand(self, date: datetime = None) -> dict:
        """Predict demand based on learned patterns."""
        if date is None:
            date = datetime.now()

        patterns = self.learned_patterns.get('peak_demand_times', {})
        
        return {
            'predicted_volume': self._predict_volume(date, patterns),
            'likely_emergency_types': self._predict_emergency_types(date),
            'recommended_staffing': self._calculate_staffing(date),
        }

    def _predict_volume(self, date: datetime, patterns: dict) -> str:
        """Predict interaction volume."""
        # Simple prediction based on day of week and month
        day = date.strftime('%A')
        month = date.strftime('%B')
        
        peak_days = [d for d, _ in patterns.get('peak_days', [])]
        peak_months = [m for m, _ in patterns.get('peak_months', [])]
        
        if day in peak_days and month in peak_months:
            return 'very_high'
        elif day in peak_days or month in peak_months:
            return 'high'
        else:
            return 'normal'

    def _predict_emergency_types(self, date: datetime) -> List[str]:
        """Predict likely emergency types for this time."""
        # In production: ML model trained on historical data
        month = date.strftime('%B').lower()
        
        seasonal = {
            'march': ['malaria', 'snakebite'],
            'april': ['malaria', 'flooding', 'snakebite'],
            'may': ['malaria', 'flooding', 'cholera'],
            'june': ['respiratory'],
            'july': ['respiratory'],
            'august': ['respiratory', 'farm_accidents'],
            'september': ['malaria'],
            'october': ['malaria', 'snakebite'],
            'november': ['malaria', 'snakebite'],
            'december': ['respiratory', 'road_accidents'],
            'january': ['respiratory', 'road_accidents'],
            'february': ['respiratory', 'farm_accidents'],
        }
        
        return seasonal.get(month, ['general'])

    def _calculate_staffing(self, date: datetime) -> dict:
        """Recommend staffing levels."""
        volume = self._predict_volume(date, {})
        
        return {
            'coordinators_needed': 2 if volume == 'normal' else 4 if volume == 'high' else 6,
            'responders_on_standby': 5 if volume == 'normal' else 10 if volume == 'high' else 15,
            'shift_recommendation': 'standard' if volume == 'normal' else 'extended' if volume == 'high' else 'full_coverage',
        }
# ml_service/context/african_context_engine.py

import json
import os
from datetime import datetime
from typing import Dict, List, Optional

class AfricanContextEngine:
    """
    Understands how things actually work in African communities.
    Not assumptions from Western datasets.
    """

    def __init__(self):
        self.load_local_knowledge()

    def load_local_knowledge(self):
        """Load community-sourced knowledge about local realities."""
        knowledge_path = 'data/local_knowledge.json'
        
        if os.path.exists(knowledge_path):
            with open(knowledge_path, 'r', encoding='utf-8') as f:
                self.knowledge = json.load(f)
        else:
            self.knowledge = self._default_knowledge()

    def _default_knowledge(self) -> dict:
        """Default knowledge base — grows with real data."""
        return {
            'transport_realities': {
                'rural': {
                    'primary': 'motorbike',
                    'average_response_minutes': 15,
                    'cost_range_ksh': [200, 500],
                    'payment_methods': ['mpesa', 'cash'],
                    'constraints': ['no_ambulance', 'poor_roads', 'night_travel_unsafe'],
                },
                'urban': {
                    'primary': 'uber_taxi',
                    'average_response_minutes': 10,
                    'cost_range_ksh': [300, 1000],
                    'payment_methods': ['mpesa', 'cash', 'card'],
                    'constraints': ['traffic', 'parking'],
                },
                'informal_settlement': {
                    'primary': 'boda_boda',
                    'average_response_minutes': 5,
                    'cost_range_ksh': [50, 200],
                    'payment_methods': ['cash', 'mpesa'],
                    'constraints': ['narrow_paths', 'no_street_names', 'security_concerns'],
                },
            },
            'health_seeking_behaviors': {
                'first_contact': ['pharmacy', 'community_health_worker', 'traditional_healer'],
                'delay_factors': ['cost', 'distance', 'wait_time', 'language_barrier', 'mistrust'],
                'decision_makers': ['self', 'spouse', 'elder', 'community_health_worker'],
                'traditional_medicine': {
                    'commonly_used': True,
                    'integration_needed': True,
                    'respect_required': True,
                },
            },
            'community_structures': {
                'trusted_authorities': [
                    'village_elder',
                    'religious_leader',
                    'community_health_worker',
                    'women_group_leader',
                    'youth_leader',
                ],
                'communication_channels': [
                    'whatsapp_group',
                    'church_mosque_announcement',
                    'chief_baraza',
                    'community_radio',
                    'word_of_mouth',
                ],
                'mutual_aid_systems': [
                    'chamas',
                    'merry_go_round',
                    'harambee',
                    'burial_societies',
                    'neighbor_networks',
                ],
            },
            'economic_realities': {
                'informal_economy_percentage': 80,
                'daily_wage_range_ksh': [200, 1000],
                'mobile_money_penetration': 90,
                'bank_account_penetration': 40,
                'insurance_penetration': 25,
                'health_expenditure': 'mostly_out_of_pocket',
            },
            'seasonal_patterns': {
                'rainy_season': {
                    'months': ['march', 'april', 'may', 'october', 'november'],
                    'increased_risks': ['malaria', 'cholera', 'snakebite', 'flooding', 'road_accidents'],
                },
                'dry_season': {
                    'months': ['june', 'july', 'august', 'september', 'december', 'january', 'february'],
                    'increased_risks': ['respiratory_infections', 'dehydration', 'malnutrition'],
                },
                'harvest_season': {
                    'months': ['february', 'august'],
                    'increased_risks': ['farm_accidents', 'pesticide_poisoning'],
                },
                'school_terms': {
                    'months': ['january', 'may', 'september'],
                    'patterns': ['increased_travel', 'boarding_school_health_checks'],
                },
            },
        }

    def get_transport_plan(self, location_type: str, urgency: str, time_of_day: str) -> dict:
        """Generate a transport plan based on local realities."""
        realities = self.knowledge['transport_realities'].get(location_type, {})
        
        plan = {
            'recommended_transport': realities.get('primary', 'motorbike'),
            'estimated_response_minutes': realities.get('average_response_minutes', 15),
            'estimated_cost': realities.get('cost_range_ksh', [200, 500]),
            'payment_options': realities.get('payment_methods', ['mpesa', 'cash']),
            'constraints': realities.get('constraints', []),
            'alternatives': [],
        }

        # Adjust for urgency
        if urgency == 'immediate':
            plan['priority'] = 'highest'
            plan['pre_paid_by_nafasi'] = True
        elif urgency == 'urgent':
            plan['priority'] = 'high'
        else:
            plan['priority'] = 'normal'

        # Adjust for time of day
        hour = int(time_of_day.split(':')[0]) if ':' in time_of_day else 12
        if hour < 6 or hour > 20:
            plan['constraints'].append('limited_transport_at_night')
            plan['estimated_response_minutes'] += 10

        return plan

    def get_community_trust_factors(self, facility_type: str, community_context: dict) -> dict:
        """Determine what builds trust in this community context."""
        return {
            'important_factors': [
                'staff_speaks_local_language',
                'known_to_community',
                'recommended_by_chief_or_elder',
                'has_served_community_before',
                'affordable_pricing',
                'respects_traditional_beliefs',
            ],
            'trust_building_actions': [
                'introduce_via_community_health_worker',
                'provide_cost_estimate_upfront',
                'explain_procedures_in_local_language',
                'allow_family_to_accompany',
                'offer_flexible_payment',
            ],
            'warning_signals': [
                'staff_only_speaks_english',
                'no_local_references',
                'demands_payment_before_service',
                'dismisses_traditional_medicine',
                'no_privacy_for_women',
            ],
        }

    def get_seasonal_risk_factors(self, date: datetime = None) -> dict:
        """Get current seasonal risks based on the calendar."""
        if date is None:
            date = datetime.now()
        
        month = date.strftime('%B').lower()
        seasonal = self.knowledge['seasonal_patterns']
        
        current_risks = []
        if month in seasonal['rainy_season']['months']:
            current_risks.extend(seasonal['rainy_season']['increased_risks'])
        if month in seasonal['dry_season']['months']:
            current_risks.extend(seasonal['dry_season']['increased_risks'])
        if month in seasonal['harvest_season']['months']:
            current_risks.extend(seasonal['harvest_season']['increased_risks'])

        return {
            'current_month': month,
            'season': 'rainy' if month in seasonal['rainy_season']['months'] else 'dry',
            'active_risks': list(set(current_risks)),
            'recommended_preparedness': self._get_preparedness_actions(current_risks),
        }

    def _get_preparedness_actions(self, risks: List[str]) -> List[str]:
        """Get recommended actions based on current risks."""
        actions = {
            'malaria': [
                'Ensure bed nets are available',
                'Stock malaria rapid tests',
                'Stock anti-malarial medication',
                'Train CHWs on malaria recognition',
            ],
            'cholera': [
                'Distribute water purification tablets',
                'Set up oral rehydration points',
                'Train on cholera recognition',
            ],
            'snakebite': [
                'Verify antivenom stock at facilities',
                'Train CHWs on snakebite first aid',
                'Check responder kits have pressure bandages',
            ],
            'flooding': [
                'Identify safe evacuation routes',
                'Pre-position emergency supplies',
                'Check communication channels',
            ],
            'respiratory_infections': [
                'Stock respiratory medications',
                'Train on pneumonia recognition in children',
            ],
            'farm_accidents': [
                'Train on bleeding control',
                'Verify trauma supplies at nearest facility',
            ],
        }

        recommended = []
        for risk in risks:
            if risk in actions:
                recommended.extend(actions[risk])
        return list(set(recommended))

    def get_economic_context(self, user_indicators: dict = None) -> dict:
        """Understand economic realities affecting health decisions."""
        realities = self.knowledge['economic_realities']
        
        return {
            'likely_payment_method': 'mpesa' if not user_indicators or not user_indicators.get('bank_account') else 'bank',
            'insurance_likely': False,
            'cost_sensitivity': 'high',
            'recommended_approach': [
                'show_all_costs_upfront',
                'offer_payment_plans',
                'mention_free_or_subsidized_options_first',
                'connect_to_community_financial_support_if_available',
            ],
            'avoid': [
                'surprise_bills',
                'hidden_fees',
                'upselling_unnecessary_services',
                'demanding_payment_before_emergency_care',
            ],
        }

    def understand_health_decision_context(self, situation: dict) -> dict:
        """
        Understand who makes decisions and how.
        Critical for appropriate routing and communication.
        """
        return {
            'likely_decision_makers': self._identify_decision_makers(situation),
            'communication_style': self._get_communication_style(situation),
            'timeline_expectation': self._get_timeline(situation),
            'cultural_considerations': self._get_cultural_considerations(situation),
        }

    def _identify_decision_makers(self, situation: dict) -> List[str]:
        """Identify who likely makes the health decision."""
        decision_makers = ['patient']
        
        if situation.get('age_group') == 'elderly':
            decision_makers.append('adult_children')
        elif situation.get('age_group') == 'child':
            decision_makers.append('parents')
        elif situation.get('gender') == 'female' and situation.get('marital_status') == 'married':
            decision_makers.append('spouse')
        
        if situation.get('location_type') == 'rural':
            decision_makers.append('community_elder')
        
        return decision_makers

    def _get_communication_style(self, situation: dict) -> dict:
        """Determine appropriate communication approach."""
        return {
            'language': situation.get('language', 'sw'),
            'formality': 'respectful',
            'pace': 'patient_and_explanatory',
            'include': ['cost_explanation', 'process_explanation', 'family_involvement'],
            'avoid': ['medical_jargon', 'rushing', 'dismissing_concerns'],
        }

    def _get_timeline(self, situation: dict) -> dict:
        """Understand expected timeline for decisions."""
        if situation.get('urgency') == 'immediate':
            return {'decision_time': 'minutes', 'action_time': 'immediate'}
        elif situation.get('urgency') == 'urgent':
            return {'decision_time': 'hours', 'action_time': 'today'}
        else:
            return {'decision_time': 'days', 'action_time': 'this_week'}

    def _get_cultural_considerations(self, situation: dict) -> List[str]:
        """Identify cultural factors to respect."""
        considerations = []
        
        if situation.get('gender') == 'female':
            considerations.append('may_prefer_female_provider')
        
        if situation.get('location_type') == 'rural':
            considerations.append('may_consult_traditional_healer_first')
            considerations.append('family_consensus_important')
        
        if situation.get('religion') == 'muslim':
            considerations.append('prayer_times_to_respect')
        
        return considerations
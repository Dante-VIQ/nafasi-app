# ml_service/dictionary/seed_data.py

import json
from dictionary_loader import DictionaryLoader

def seed_initial_dictionary():
    """Seed the dictionary with initial data from Kamusi/WordNet + community."""
    loader = DictionaryLoader()
    
    # This data would come from actual Kamusi XML → JSON conversion
    # For now, seed with the essential words we know
    entries = [
        # Sheng
        {'word': 'noma', 'language': 'sheng', 'tags': ['emergency', 'danger'], 'is_emergency': 1, 'definition': 'serious problem, danger, emergency'},
        {'word': 'mboka', 'language': 'sheng', 'tags': ['urgent'], 'is_emergency': 1, 'definition': 'quickly, fast, urgently'},
        {'word': 'manze', 'language': 'sheng', 'tags': [], 'definition': 'Sheng discourse marker, like "you know"'},
        {'word': 'gava', 'language': 'sheng', 'tags': ['police'], 'facility_hint': 'police_station', 'definition': 'police, government'},
        {'word': 'daktar', 'language': 'sheng', 'tags': ['medical'], 'facility_hint': 'hospital', 'definition': 'doctor'},
        {'word': 'kudedi', 'language': 'sheng', 'tags': ['emergency', 'crisis'], 'is_emergency': 1, 'is_crisis': 1, 'definition': 'to die, dying'},
        {'word': 'kuchapa', 'language': 'sheng', 'tags': ['violence', 'emergency'], 'is_emergency': 1, 'definition': 'to hit, beat, attack'},
        {'word': 'kubambwa', 'language': 'sheng', 'tags': ['emergency'], 'is_emergency': 1, 'definition': 'to be attacked, trapped'},
        {'word': 'nishike', 'language': 'sheng', 'tags': ['help'], 'is_dispatch': 1, 'definition': 'help me, pick me up'},
        {'word': 'nimekwama', 'language': 'sheng', 'tags': ['mobility'], 'is_dispatch': 1, 'definition': 'I am stuck, trapped'},
        {'word': 'nadedi', 'language': 'sheng', 'tags': ['crisis'], 'is_crisis': 1, 'definition': 'I am dying'},
        {'word': 'labu', 'language': 'sheng', 'tags': ['medical'], 'facility_hint': 'laboratory', 'definition': 'laboratory'},
        {'word': 'hosi', 'language': 'sheng', 'tags': ['medical'], 'facility_hint': 'hospital', 'definition': 'hospital'},
        {'word': 'famasi', 'language': 'sheng', 'tags': ['medical'], 'facility_hint': 'pharmacy', 'definition': 'pharmacy'},
        {'word': 'mzae', 'language': 'sheng', 'tags': ['medical'], 'facility_hint': 'maternity_home', 'definition': 'maternity, birth'},
        
        # Swahili
        {'word': 'moto', 'language': 'sw', 'tags': ['emergency', 'fire'], 'is_emergency': 1, 'definition': 'fire, flame, heat, danger'},
        {'word': 'damu', 'language': 'sw', 'tags': ['emergency', 'medical'], 'is_emergency': 1, 'definition': 'blood, bleeding'},
        {'word': 'ajali', 'language': 'sw', 'tags': ['emergency', 'accident'], 'is_emergency': 1, 'definition': 'accident, crash'},
        {'word': 'hatari', 'language': 'sw', 'tags': ['emergency', 'danger'], 'is_emergency': 1, 'definition': 'danger, risk'},
        {'word': 'kujiua', 'language': 'sw', 'tags': ['crisis', 'suicide'], 'is_crisis': 1, 'is_emergency': 1, 'definition': 'suicide, kill oneself'},
        {'word': 'kufa', 'language': 'sw', 'tags': ['crisis', 'death'], 'is_crisis': 1, 'definition': 'to die, death'},
        {'word': 'dawa', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'pharmacy', 'definition': 'medicine, drug'},
        {'word': 'hospitali', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'hospital', 'definition': 'hospital'},
        {'word': 'maabara', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'laboratory', 'definition': 'laboratory'},
        {'word': 'mimba', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'maternity_home', 'definition': 'pregnancy'},
        {'word': 'meno', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'dental_clinic', 'definition': 'teeth, dental'},
        {'word': 'daktari', 'language': 'sw', 'tags': ['medical'], 'facility_hint': 'hospital', 'definition': 'doctor'},
        {'word': 'anguka', 'language': 'sw', 'tags': ['emergency', 'fall'], 'is_emergency': 1, 'is_dispatch': 1, 'definition': 'to fall down'},
        {'word': 'kitandani', 'language': 'sw', 'tags': ['mobility'], 'is_dispatch': 1, 'definition': 'bedridden, in bed'},
        
        # English
        {'word': 'fire', 'language': 'en', 'tags': ['emergency', 'fire'], 'is_emergency': 1, 'definition': 'fire, burning'},
        {'word': 'suicide', 'language': 'en', 'tags': ['crisis'], 'is_crisis': 1, 'is_emergency': 1, 'definition': 'suicide, self-harm'},
        {'word': 'pharmacy', 'language': 'en', 'tags': ['medical'], 'facility_hint': 'pharmacy', 'definition': 'drugstore, chemist'},
        {'word': 'hospital', 'language': 'en', 'tags': ['medical'], 'facility_hint': 'hospital', 'definition': 'hospital'},
        {'word': 'bleeding', 'language': 'en', 'tags': ['emergency', 'medical'], 'is_emergency': 1, 'definition': 'losing blood'},
    ]
    
    for entry in entries:
        loader.add_word(
            word=entry['word'],
            language=entry['language'],
            definition=entry.get('definition'),
            tags=entry.get('tags', []),
            is_emergency=entry.get('is_emergency', 0),
            is_crisis=entry.get('is_crisis', 0),
            is_dispatch=entry.get('is_dispatch', 0),
            facility_hint=entry.get('facility_hint'),
            source=entry.get('source', 'seed'),
        )
    
    print(f"Seeded {len(entries)} words into dictionary.")

if __name__ == '__main__':
    seed_initial_dictionary()
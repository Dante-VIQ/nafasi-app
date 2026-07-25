# ml_service/dictionary/seed_swahili_dispatch.py

from dictionary_loader import DictionaryLoader

def seed_swahili_dispatch():
    loader = DictionaryLoader()
    
    entries = [
        # Dispatch phrases (whole)
        {'word': 'siwezi tembea', 'language': 'sw', 'is_dispatch': 1, 'tags': ['mobility'], 'definition': 'I cannot walk'},
        {'word': 'siwezi kutembea', 'language': 'sw', 'is_dispatch': 1, 'tags': ['mobility'], 'definition': 'I cannot walk'},
        {'word': 'nisaidie', 'language': 'sw', 'is_dispatch': 1, 'tags': ['help'], 'definition': 'help me'},
        {'word': 'nisaidieni', 'language': 'sw', 'is_dispatch': 1, 'tags': ['help'], 'definition': 'help me (plural)'},
        {'word': 'naomba msaada', 'language': 'sw', 'is_dispatch': 1, 'tags': ['help'], 'definition': 'I need help'},
        {'word': 'siwezi', 'language': 'sw', 'is_dispatch': 1, 'tags': ['mobility'], 'definition': 'I cannot'},
        {'word': 'nimeanguka', 'language': 'sw', 'is_dispatch': 1, 'tags': ['fall'], 'is_emergency': 1, 'definition': 'I have fallen'},
        {'word': 'nimekwama', 'language': 'sw', 'is_dispatch': 1, 'tags': ['stuck'], 'definition': 'I am stuck'},
        {'word': 'sina usafiri', 'language': 'sw', 'is_dispatch': 1, 'tags': ['transport'], 'definition': 'I have no transport'},
        {'word': 'njoo', 'language': 'sw', 'is_dispatch': 1, 'tags': ['help'], 'definition': 'come'},
        {'word': 'kuja', 'language': 'sw', 'is_dispatch': 1, 'tags': ['help'], 'definition': 'come'},
        {'word': 'haraka', 'language': 'sw', 'tags': ['urgent'], 'is_emergency': 1, 'definition': 'quickly'},
        {'word': 'hatari', 'language': 'sw', 'tags': ['danger'], 'is_emergency': 1, 'definition': 'danger'},
        {'word': 'dharura', 'language': 'sw', 'tags': ['emergency'], 'is_emergency': 1, 'definition': 'emergency'},
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
            source='seed_dispatch',
        )
    
    print(f"Seeded {len(entries)} dispatch-related words.")

if __name__ == '__main__':
    seed_swahili_dispatch()
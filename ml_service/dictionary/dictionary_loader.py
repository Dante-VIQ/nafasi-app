# ml_service/dictionary/dictionary_loader.py

import sqlite3
import os
import json
from typing import Optional, List, Dict

class DictionaryLoader:
    """
    Loads and queries the dictionary database.
    Data sources: Kamusi Project, Swahili WordNet, community submissions.
    NO hardcoded word lists.
    """

    def __init__(self, db_path: str = None):
        self.db_path = db_path or os.path.join(
            os.path.dirname(__file__), '..', 'data', 'dictionary.db'
        )
        self._ensure_database()

    def _ensure_database(self):
        """Create the database if it doesn't exist."""
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS dictionary (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                word TEXT NOT NULL,
                normalized TEXT NOT NULL,
                language TEXT NOT NULL CHECK(language IN ('sw', 'en', 'sheng')),
                part_of_speech TEXT,
                definition TEXT,
                tags TEXT,          -- JSON array: ["emergency","medical","fire"]
                is_emergency INTEGER DEFAULT 0,
                is_crisis INTEGER DEFAULT 0,
                is_dispatch INTEGER DEFAULT 0,
                facility_hint TEXT,
                source TEXT DEFAULT 'community',
                usage_count INTEGER DEFAULT 0,
                confidence REAL DEFAULT 1.0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        cursor.execute('''
            CREATE INDEX IF NOT EXISTS idx_normalized_lang 
            ON dictionary(normalized, language)
        ''')
        
        conn.commit()
        conn.close()

    def lookup(self, word: str, language: str = None) -> List[Dict]:
        """
        Look up a word. Returns ALL matching entries.
        If language specified, filter by language.
        """
        word = word.lower().strip()
        
        conn = sqlite3.connect(self.db_path)
        conn.row_factory = sqlite3.Row
        cursor = conn.cursor()
        
        if language:
            cursor.execute(
                'SELECT * FROM dictionary WHERE normalized = ? AND language = ?',
                (word, language)
            )
        else:
            cursor.execute(
                'SELECT * FROM dictionary WHERE normalized = ?',
                (word,)
            )
        
        results = [dict(row) for row in cursor.fetchall()]
        
        # Increment usage count for matched words
        if results:
            cursor.execute(
                'UPDATE dictionary SET usage_count = usage_count + 1 WHERE normalized = ?',
                (word,)
            )
            conn.commit()
        
        conn.close()
        return results

    def is_language_word(self, word: str, language: str) -> bool:
        """Check if a word exists in the dictionary for a given language."""
        results = self.lookup(word, language)
        return len(results) > 0

    def get_emergency_words(self, language: str = None) -> List[str]:
        """Get all words tagged as emergency signals."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        query = 'SELECT word FROM dictionary WHERE is_emergency = 1'
        params = ()
        if language:
            query += ' AND language = ?'
            params = (language,)
        
        cursor.execute(query, params)
        words = [row[0] for row in cursor.fetchall()]
        conn.close()
        return words

    def get_crisis_words(self, language: str = None) -> List[str]:
        """Get all words tagged as crisis signals."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        query = 'SELECT word FROM dictionary WHERE is_crisis = 1'
        params = ()
        if language:
            query += ' AND language = ?'
            params = (language,)
        
        cursor.execute(query, params)
        words = [row[0] for row in cursor.fetchall()]
        conn.close()
        return words

    def get_dispatch_words(self, language: str = None) -> List[str]:
        """Get all words tagged as 'help come to me' signals."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        query = 'SELECT word FROM dictionary WHERE is_dispatch = 1'
        params = ()
        if language:
            query += ' AND language = ?'
            params = (language,)
        
        cursor.execute(query, params)
        words = [row[0] for row in cursor.fetchall()]
        conn.close()
        return words

    def get_facility_hints(self, word: str) -> List[str]:
        """Get facility type hints for a word."""
        results = self.lookup(word)
        hints = []
        for r in results:
            if r.get('facility_hint'):
                hints.append(r['facility_hint'])
        return hints

    def add_word(self, word: str, language: str, **kwargs):
        """Add a new word to the dictionary."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            INSERT OR REPLACE INTO dictionary 
            (word, normalized, language, part_of_speech, definition, tags,
             is_emergency, is_crisis, is_dispatch, facility_hint, source)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            word,
            word.lower().strip(),
            language,
            kwargs.get('part_of_speech'),
            kwargs.get('definition'),
            json.dumps(kwargs.get('tags', [])),
            kwargs.get('is_emergency', 0),
            kwargs.get('is_crisis', 0),
            kwargs.get('is_dispatch', 0),
            kwargs.get('facility_hint'),
            kwargs.get('source', 'community'),
        ))
        
        conn.commit()
        conn.close()

    def seed_from_json(self, file_path: str):
        """Seed the dictionary from a JSON file (Kamusi, WordNet data)."""
        with open(file_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
        
        for entry in data:
            self.add_word(
                word=entry['word'],
                language=entry['language'],
                part_of_speech=entry.get('pos'),
                definition=entry.get('definition'),
                tags=entry.get('tags', []),
                is_emergency=entry.get('is_emergency', 0),
                is_crisis=entry.get('is_crisis', 0),
                is_dispatch=entry.get('is_dispatch', 0),
                facility_hint=entry.get('facility_hint'),
                source=entry.get('source', 'import'),
            )
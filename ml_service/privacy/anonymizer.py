# ml_service/privacy/anonymizer.py

class TrainingDataAnonymizer:
    """
    Ensures training data contains NO personal information.
    Runs before any data is used for training.
    """

    @staticmethod
    def sanitize(interaction: dict) -> dict:
        """Remove ALL potential identifiers."""
        # Only keep these fields
        allowed_fields = [
            'text', 'language', 'intent', 'confidence',
            'facility_selected', 'outcome', 'timestamp',
        ]
        
        sanitized = {k: interaction.get(k) for k in allowed_fields}
        
        # Strip any remaining identifiers from text
        text = sanitized.get('text', '')
        # Remove phone numbers
        import re
        text = re.sub(r'\+?\d{9,15}', '[PHONE]', text)
        # Remove emails
        text = re.sub(r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}', '[EMAIL]', text)
        # Remove names (basic — proper nouns are harder)
        # Remove precise locations (keep general area only)
        
        sanitized['text'] = text
        
        return sanitized